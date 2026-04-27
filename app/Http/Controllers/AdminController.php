<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Queue;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    // ── Dashboard ─────────────────────────────────────────────────────────────

    public function dashboard()
    {
        $loketIds = array_keys(Queue::LOKETS);

        $rows = Queue::today()
            ->selectRaw('loket_id, status, COUNT(*) as cnt, AVG(service_duration) as avg_dur')
            ->groupBy('loket_id', 'status')
            ->get()
            ->groupBy('loket_id');

        $summary = [];
        foreach ($loketIds as $id) {
            $byStat = $rows->get($id, collect())->keyBy('status');
            $avgRaw = (int) ($byStat->get('done')?->avg_dur ?? 0);
            $m = intdiv($avgRaw, 60);
            $s = $avgRaw % 60;
            $summary[$id] = [
                'info'      => Queue::loketInfo($id),
                'waiting'   => (int) ($byStat->get('waiting')?->cnt   ?? 0),
                'calling'   => (int) ($byStat->get('calling')?->cnt   ?? 0),
                'done'      => (int) ($byStat->get('done')?->cnt      ?? 0),
                'cancelled' => (int) ($byStat->get('cancelled')?->cnt ?? 0),
                'avg_human' => $avgRaw > 0 ? ($m > 0 ? "{$m} mnt {$s} dtk" : "{$s} dtk") : '-',
            ];
        }

        $totalToday = Queue::today()->count();
        $totalDone  = Queue::today()->done()->count();
        $userCount  = User::count();

        return view('admin.dashboard', compact('summary', 'totalToday', 'totalDone', 'userCount'));
    }

    // ── User management ───────────────────────────────────────────────────────

    public function users()
    {
        $users = User::orderBy('role')->orderBy('name')->get();
        return view('admin.users.index', compact('users'));
    }

    public function createUser()
    {
        return view('admin.users.form', ['user' => null]);
    }

    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'name'                  => 'required|string|max:100',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|string|min:8|confirmed',
            'role'                  => 'required|in:admin,operator',
            'loket_id'              => 'nullable|integer|in:1,2,3',
        ]);

        User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => $data['role'],
            'loket_id' => $data['role'] === 'operator' ? ($data['loket_id'] ?? null) : null,
        ]);

        return redirect()->route('admin.users')
            ->with('success', "Akun \"{$data['name']}\" berhasil dibuat.");
    }

    public function editUser(User $user)
    {
        return view('admin.users.form', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $data = $request->validate([
            'name'                  => 'required|string|max:100',
            'email'                 => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password'              => 'nullable|string|min:8|confirmed',
            'role'                  => 'required|in:admin,operator',
            'loket_id'              => 'nullable|integer|in:1,2,3',
        ]);

        $update = [
            'name'     => $data['name'],
            'email'    => $data['email'],
            'role'     => $data['role'],
            'loket_id' => $data['role'] === 'operator' ? ($data['loket_id'] ?? null) : null,
        ];

        if (! empty($data['password'])) {
            $update['password'] = Hash::make($data['password']);
        }

        $user->update($update);

        return redirect()->route('admin.users')
            ->with('success', "Akun \"{$user->name}\" berhasil diperbarui.");
    }

    public function destroyUser(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Tidak dapat menghapus akun Anda sendiri.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', "Akun \"{$name}\" berhasil dihapus.");
    }

    // ── Laporan harian ────────────────────────────────────────────────────────

    public function laporan(Request $request)
    {
        $tanggal = $request->get('tanggal', today()->toDateString());

        $queues = Queue::whereDate('created_at', $tanggal)
            ->orderBy('loket_id')
            ->orderBy('nomor_antrian')
            ->get();

        $loketIds = array_keys(Queue::LOKETS);
        $perLoket = $queues->groupBy('loket_id');

        $summary = [];
        foreach ($loketIds as $id) {
            $group  = $perLoket->get($id, collect());
            $done   = $group->where('status', 'done');
            $avgRaw = (int) ($done->avg('service_duration') ?? 0);
            $m = intdiv($avgRaw, 60);
            $s = $avgRaw % 60;
            $summary[$id] = [
                'info'      => Queue::loketInfo($id),
                'total'     => $group->count(),
                'done'      => $done->count(),
                'cancelled' => $group->where('status', 'cancelled')->count(),
                'waiting'   => $group->whereIn('status', ['waiting', 'calling', 'paused'])->count(),
                'avg_human' => $avgRaw > 0 ? ($m > 0 ? "{$m} mnt {$s} dtk" : "{$s} dtk") : '-',
            ];
        }

        return view('admin.laporan', compact('queues', 'summary', 'tanggal'));
    }

    // ── Export CSV (bisa dibuka Excel) ────────────────────────────────────────

    public function exportCsv(Request $request)
    {
        $tanggal  = $request->get('tanggal', today()->toDateString());
        $filename = 'laporan-antrian-' . $tanggal . '.csv';

        $queues = Queue::whereDate('created_at', $tanggal)
            ->orderBy('loket_id')
            ->orderBy('nomor_antrian')
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
        ];

        $callback = function () use ($queues, $tanggal) {
            $out = fopen('php://output', 'w');

            // BOM agar Excel baca UTF-8 dengan benar
            fwrite($out, "\xEF\xBB\xBF");

            // Baris judul
            fputcsv($out, ['LAPORAN ANTRIAN HARIAN — RS SEHAT SENTOSA']);
            fputcsv($out, ['Tanggal', $tanggal]);
            fputcsv($out, ['Dicetak', now()->format('d/m/Y H:i:s')]);
            fputcsv($out, []);

            // Header kolom
            fputcsv($out, [
                'Kode Antrian',
                'Loket',
                'Jenis Layanan',
                'Status',
                'Waktu Ambil',
                'Waktu Dipanggil',
                'Waktu Selesai',
                'Durasi Layanan',
            ]);

            foreach ($queues as $q) {
                fputcsv($out, [
                    $q->kode_antrian,
                    'Loket ' . $q->loket_id,
                    Queue::loketInfo($q->loket_id)['label'],
                    $q->status_label,
                    $q->created_at->format('H:i:s'),
                    $q->called_at?->format('H:i:s') ?? '-',
                    $q->done_at?->format('H:i:s')   ?? '-',
                    $q->duration_human,
                ]);
            }

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ── Audit log ─────────────────────────────────────────────────────────────

    public function audit(Request $request)
    {
        $tanggal = $request->get('tanggal', today()->toDateString());
        $loketId = $request->get('loket_id');

        $query = ActivityLog::whereDate('created_at', $tanggal)
            ->orderByDesc('created_at');

        if ($loketId) {
            $query->where('loket_id', $loketId);
        }

        $logs    = $query->limit(500)->get();
        $lokets  = Queue::LOKETS;

        return view('admin.audit', compact('logs', 'tanggal', 'loketId', 'lokets'));
    }
}
