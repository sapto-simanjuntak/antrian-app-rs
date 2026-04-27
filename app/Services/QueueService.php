<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Queue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

/**
 * QueueService
 * Semua business logic antrian terpusat di sini.
 * Controller hanya sebagai HTTP layer.
 */
class QueueService
{
    // ── Kiosk: ambil nomor antrian ─────────────────────────────────────────────

    public function ambilAntrian(int $loketId): Queue
    {
        return DB::transaction(function () use ($loketId) {
            $nomor = Queue::nextNumber($loketId); // locked query
            $kode  = Queue::generateKode($loketId, $nomor);

            $queue = Queue::create([
                'loket_id'      => $loketId,
                'nomor_antrian' => $nomor,
                'kode_antrian'  => $kode,
                'status'        => 'waiting',
            ]);

            $this->touchUpdateToken();

            return $queue;
        });
    }

    // ── Loket: panggil antrian berikutnya ──────────────────────────────────────

    public function panggil(int $loketId): array
    {
        // Pastikan tidak ada antrian aktif terlebih dahulu
        if ($this->getActive($loketId)) {
            return $this->error('Selesaikan antrian aktif sebelum memanggil yang berikutnya.');
        }

        $next = Queue::today()->loket($loketId)->waiting()->orderBy('id')->first();

        if (! $next) {
            return $this->error('Tidak ada antrian yang menunggu.');
        }

        $next->update([
            'status'    => 'calling',
            'called_at' => now(),
        ]);

        $this->touchUpdateToken();
        ActivityLog::record($loketId, 'panggil', $next->kode_antrian);

        return $this->success($next->fresh(), "Memanggil {$next->kode_antrian}");
    }

    // ── Loket: panggil ulang ───────────────────────────────────────────────────

    public function panggilUlang(int $loketId): array
    {
        $active = $this->getActive($loketId);

        if (! $active) {
            return $this->error('Tidak ada antrian aktif untuk dipanggil ulang.');
        }

        $active->update([
            'status'    => 'calling',
            'called_at' => now(),
        ]);

        $this->touchUpdateToken();
        ActivityLog::record($loketId, 'panggil_ulang', $active->kode_antrian);

        return $this->success($active->fresh(), "Memanggil ulang {$active->kode_antrian}");
    }

    // ── Loket: lanjut DIHAPUS — timer langsung dari called_at ─────────────────

    // ── Loket: pause ──────────────────────────────────────────────────────────

    public function pause(int $loketId): array
    {
        $active = $this->getActive($loketId);

        if (! $active) {
            return $this->error('Tidak ada antrian aktif untuk di-pause.');
        }

        $active->update(['status' => 'paused']);
        $this->touchUpdateToken();
        ActivityLog::record($loketId, 'pause', $active->kode_antrian);

        return $this->success($active->fresh(), "{$active->kode_antrian} ditunda.");
    }

    // ── Loket: selesai ────────────────────────────────────────────────────────

    public function selesai(int $loketId): array
    {
        $active = $this->getActive($loketId);

        if (! $active) {
            return $this->error('Tidak ada antrian aktif.');
        }

        // Durasi dihitung dari called_at (tombol Lanjut sudah dihapus)
        $duration = $active->called_at
            ? (int) now()->diffInSeconds($active->called_at)
            : null;

        $active->update([
            'status'           => 'done',
            'done_at'          => now(),
            'service_duration' => $duration,
        ]);

        $refreshed = $active->fresh();
        $this->touchUpdateToken();
        ActivityLog::record($loketId, 'selesai', $refreshed->kode_antrian, "Durasi: {$refreshed->duration_human}");

        return $this->success($refreshed, "{$refreshed->kode_antrian} selesai. Durasi: {$refreshed->duration_human}");
    }

    // ── Loket: batal ──────────────────────────────────────────────────────────

    public function batal(int $loketId): array
    {
        $active = $this->getActive($loketId);

        if (! $active) {
            return $this->error('Tidak ada antrian aktif untuk dibatalkan.');
        }

        $kode = $active->kode_antrian;
        $active->update([
            'status'  => 'cancelled',
            'done_at' => now(),
        ]);

        $this->touchUpdateToken();
        ActivityLog::record($loketId, 'batal', $kode);

        return $this->success(null, "{$kode} dibatalkan.");
    }

    // ── Loket: tidak hadir (no-show) ─────────────────────────────────────────

    public function tidakHadir(int $loketId): array
    {
        $active = $this->getActive($loketId);

        if (! $active) {
            return $this->error('Tidak ada antrian aktif.');
        }

        $kode = $active->kode_antrian;
        $active->update([
            'status'  => 'no_show',
            'done_at' => now(),
        ]);

        $this->touchUpdateToken();
        ActivityLog::record($loketId, 'tidak_hadir', $kode, 'Pasien tidak hadir setelah dipanggil');

        return $this->success(null, "{$kode} ditandai tidak hadir.");
    }

    // ── State queries ─────────────────────────────────────────────────────────

    public function getActive(int $loketId): ?Queue
    {
        return Queue::today()->loket($loketId)->active()->orderBy('called_at')->first();
    }

    public function getLoketState(int $loketId): array
    {
        $active  = $this->getActive($loketId);
        $history = Queue::today()
            ->loket($loketId)
            ->whereIn('status', ['done', 'cancelled'])
            ->orderByDesc('done_at')
            ->limit(25)
            ->get()
            ->map(fn($q) => $q->toApiArray());

        $waitingList = Queue::today()
            ->loket($loketId)
            ->waiting()
            ->orderBy('id')
            ->get()
            ->map(fn($q) => $q->toApiArray());

        return [
            'active'       => $active?->toApiArray(),
            'waiting'      => $waitingList->count(),
            'waiting_list' => $waitingList,
            'history'      => $history,
            'stats'        => $this->getStats($loketId),
        ];
    }

    public function getDisplayState(): array
    {
        $loketIds = array_keys(Queue::LOKETS);

        // 1 query: hitung waiting & done per loket sekaligus
        $counts = Queue::today()
            ->whereIn('loket_id', $loketIds)
            ->whereIn('status', ['waiting', 'done'])
            ->selectRaw('loket_id, status, COUNT(*) as cnt')
            ->groupBy('loket_id', 'status')
            ->get()
            ->groupBy('loket_id');

        // 1 query: semua antrian aktif dari seluruh loket
        $actives = Queue::today()
            ->whereIn('loket_id', $loketIds)
            ->whereIn('status', Queue::ACTIVE_STATUSES)
            ->orderBy('called_at')
            ->get()
            ->keyBy('loket_id');

        $lokets = [];
        foreach ($loketIds as $id) {
            $byStat = $counts->get($id, collect())->keyBy('status');
            $lokets[$id] = [
                'loket_id'   => $id,
                'loket_info' => Queue::loketInfo($id),
                'active'     => $actives->get($id)?->toApiArray(),
                'waiting'    => (int) ($byStat->get('waiting')?->cnt ?? 0),
                'done_today' => (int) ($byStat->get('done')?->cnt    ?? 0),
            ];
        }

        return [
            'lokets'       => $lokets,
            'update_token' => Cache::get('queue_update_token', 0),
            'server_time'  => now()->format('H:i:s'),
            'server_date'  => now()->locale('id')->isoFormat('dddd, D MMMM Y'),
        ];
    }

    public function getStats(int $loketId): array
    {
        // 1 query: aggregate semua status sekaligus
        $rows = Queue::today()
            ->loket($loketId)
            ->selectRaw('status, COUNT(*) as cnt, AVG(service_duration) as avg_dur')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $done     = (int) ($rows->get('done')?->cnt      ?? 0);
        $cancelled = (int) ($rows->get('cancelled')?->cnt ?? 0);
        $noShow   = (int) ($rows->get('no_show')?->cnt   ?? 0);
        $avgRaw   = (int) ($rows->get('done')?->avg_dur  ?? 0);
        $m = intdiv($avgRaw, 60);
        $s = $avgRaw % 60;

        return [
            'total_waiting'      => (int) ($rows->get('waiting')?->cnt ?? 0),
            'total_done'         => $done,
            'total_cancelled'    => $cancelled,
            'total_no_show'      => $noShow,
            'avg_duration_raw'   => $avgRaw,
            'avg_duration_human' => $avgRaw > 0 ? ($m > 0 ? "{$m} mnt {$s} dtk" : "{$s} dtk") : '-',
            'total_served_today' => $done + $cancelled + $noShow,
        ];
    }

    // ── Kiosk stats ───────────────────────────────────────────────────────────

    public function getKioskStats(): array
    {
        $loketIds = array_keys(Queue::LOKETS);

        // 1 query: hitung waiting per loket sekaligus
        $waitingCounts = Queue::today()
            ->whereIn('loket_id', $loketIds)
            ->waiting()
            ->selectRaw('loket_id, COUNT(*) as cnt')
            ->groupBy('loket_id')
            ->get()
            ->keyBy('loket_id');

        $stats = [];
        foreach ($loketIds as $id) {
            $stats[$id] = [
                'waiting'    => (int) ($waitingCounts->get($id)?->cnt ?? 0),
                'loket_info' => Queue::loketInfo($id),
            ];
        }
        return $stats;
    }

    // ── Internal helpers ──────────────────────────────────────────────────────

    private function touchUpdateToken(): void
    {
        Cache::put('queue_update_token', now()->valueOf(), now()->addDay());
    }

    private function success(?Queue $queue, string $message = 'Berhasil'): array
    {
        return [
            'success' => true,
            'message' => $message,
            'data'    => $queue?->toApiArray(),
        ];
    }

    private function error(string $message): array
    {
        return [
            'success' => false,
            'message' => $message,
            'data'    => null,
        ];
    }
}
