<?php

namespace App\Services;

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

        return $this->success($refreshed, "{$refreshed->kode_antrian} selesai. Durasi: {$refreshed->duration_human}");
    }

    // ── Loket: batal ──────────────────────────────────────────────────────────

    public function batal(int $loketId): array
    {
        $active = $this->getActive($loketId);

        if (! $active) {
            return $this->error('Tidak ada antrian aktif untuk dibatalkan.');
        }

        $active->update([
            'status'  => 'cancelled',
            'done_at' => now(),
        ]);

        $this->touchUpdateToken();

        return $this->success(null, "{$active->kode_antrian} dibatalkan.");
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
        $lokets = [];

        foreach ([1, 2, 3] as $id) {
            $active = $this->getActive($id);
            $lokets[$id] = [
                'loket_id'    => $id,
                'loket_info'  => Queue::loketInfo($id),
                'active'      => $active?->toApiArray(),
                'waiting'     => Queue::today()->loket($id)->waiting()->count(),
                'done_today'  => Queue::today()->loket($id)->done()->count(),
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
        $doneQuery = Queue::today()->loket($loketId)->done();
        $avgRaw    = (int) ($doneQuery->avg('service_duration') ?? 0);
        $m = intdiv($avgRaw, 60);
        $s = $avgRaw % 60;

        return [
            'total_waiting'      => Queue::today()->loket($loketId)->waiting()->count(),
            'total_done'         => Queue::today()->loket($loketId)->done()->count(),
            'total_cancelled'    => Queue::today()->loket($loketId)->where('status', 'cancelled')->count(),
            'avg_duration_raw'   => $avgRaw,
            'avg_duration_human' => $avgRaw > 0 ? ($m > 0 ? "{$m}m {$s}d" : "{$s}d") : '-',
            'total_served_today' => Queue::today()->loket($loketId)->whereIn('status', ['done', 'cancelled'])->count(),
        ];
    }

    // ── Kiosk stats ───────────────────────────────────────────────────────────

    public function getKioskStats(): array
    {
        $stats = [];
        foreach ([1, 2, 3] as $id) {
            $stats[$id] = [
                'waiting'    => Queue::today()->loket($id)->waiting()->count(),
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
