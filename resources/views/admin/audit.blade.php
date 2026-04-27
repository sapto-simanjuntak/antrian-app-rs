@extends('layouts.app')

@section('title', 'Audit Log')
@section('role-icon', 'shield-lock-fill')
@section('role-label', 'Audit Log')

@section('nav-extra')
    <div style="display:flex;gap:.4rem;margin-right:.25rem;">
        <a href="{{ route('admin.dashboard') }}" class="admin-nav-link"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <a href="{{ route('admin.users') }}"     class="admin-nav-link"><i class="bi bi-people-fill"></i> Pengguna</a>
        <a href="{{ route('admin.laporan') }}"   class="admin-nav-link"><i class="bi bi-bar-chart-fill"></i> Laporan</a>
        <a href="{{ route('admin.audit') }}"     class="admin-nav-link active"><i class="bi bi-shield-lock-fill"></i> Audit</a>
    </div>
@endsection

@push('styles')
<style>
    .admin-nav-link {
        padding: 5px 13px; border-radius: 8px; font-size: .8rem; font-weight: 700;
        color: var(--c-muted); text-decoration: none;
        display: inline-flex; align-items: center; gap: .35rem;
        transition: all .15s; border: 1.5px solid transparent;
    }
    .admin-nav-link:hover  { background: var(--c-surface); color: var(--c-text); }
    .admin-nav-link.active { background: var(--c-primary-soft); color: var(--c-primary); border-color: rgba(0,87,255,.15); }

    .pg-header { margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; }
    .pg-header h1 { font-size: 1.4rem; font-weight: 900; margin: 0; }
    .pg-header p  { color: var(--c-muted); font-size: .88rem; margin: .2rem 0 0; }

    .filter-bar { display: flex; gap: .6rem; flex-wrap: wrap; }
    .filter-bar input[type="date"], .filter-bar select {
        padding: 7px 12px; border: 1.5px solid var(--c-border); border-radius: 10px;
        font-family: var(--font); font-size: .88rem; color: var(--c-text); outline: none;
        transition: border-color .15s; background: var(--c-white);
    }
    .filter-bar input:focus, .filter-bar select:focus { border-color: var(--c-primary); }
    .filter-bar button { padding: 8px 16px; background: var(--c-primary); color: #fff; border: none; border-radius: 10px; font-family: var(--font); font-size: .88rem; font-weight: 700; cursor: pointer; }

    .card { background: var(--c-white); border: 1px solid var(--c-border); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); overflow: hidden; }
    .card-head { padding: 1rem 1.4rem; border-bottom: 1px solid var(--c-border); display: flex; align-items: center; justify-content: space-between; }
    .card-head h2 { font-size: .98rem; font-weight: 800; margin: 0; }
    .card-head span { font-size: .8rem; color: var(--c-muted); }

    table { width: 100%; border-collapse: collapse; }
    th { font-size: .72rem; font-weight: 800; text-transform: uppercase; letter-spacing: .6px; color: var(--c-muted); padding: .75rem 1rem; border-bottom: 1.5px solid var(--c-border); text-align: left; white-space: nowrap; }
    td { padding: .7rem 1rem; border-bottom: 1px solid var(--c-border); font-size: .86rem; vertical-align: middle; }
    tr:last-child td { border-bottom: none; }
    tr:hover td { background: var(--c-surface); }

    .action-badge { display: inline-flex; align-items: center; gap: .3rem; padding: 3px 9px; border-radius: 999px; font-size: .73rem; font-weight: 800; }
    .ab-panggil       { background: var(--c-primary-soft); color: var(--c-primary); }
    .ab-panggil_ulang { background: var(--c-violet-soft);  color: var(--c-violet); }
    .ab-pause         { background: var(--c-amber-soft);   color: #7a5800; }
    .ab-selesai       { background: var(--c-green-soft);   color: #006844; }
    .ab-batal         { background: var(--c-red-soft);     color: var(--c-red); }
    .ab-tidak_hadir   { background: #f1f5f9; color: #475569; }
    .ab-ambil         { background: var(--c-surface); color: var(--c-muted); }

    .kode-mono { font-family: var(--font-mono); font-weight: 500; }
    .time-muted { color: var(--c-muted); font-size: .8rem; }

    .empty-state { text-align: center; padding: 3rem 1rem; color: var(--c-muted); }
    .empty-state i { font-size: 2.5rem; opacity: .4; }
    .empty-state p { margin: .5rem 0 0; }
</style>
@endpush

@section('content')
<div style="max-width:1000px;margin:0 auto;">

    <div class="pg-header">
        <div>
            <h1>Audit Log</h1>
            <p>Rekam jejak semua aksi operator — siapa melakukan apa dan kapan</p>
        </div>
        <form class="filter-bar" method="GET" action="{{ route('admin.audit') }}">
            <input type="date" name="tanggal" value="{{ $tanggal }}" max="{{ today()->toDateString() }}">
            <select name="loket_id">
                <option value="">Semua Loket</option>
                @foreach($lokets as $id => $lk)
                    <option value="{{ $id }}" {{ $loketId == $id ? 'selected' : '' }}>
                        Loket {{ $id }} — {{ $lk['short'] }}
                    </option>
                @endforeach
            </select>
            <button type="submit"><i class="bi bi-search"></i> Filter</button>
        </form>
    </div>

    <div class="card">
        <div class="card-head">
            <h2><i class="bi bi-list-check"></i> Log Aktivitas — {{ \Carbon\Carbon::parse($tanggal)->locale('id')->isoFormat('D MMMM Y') }}</h2>
            <span>{{ $logs->count() }} entri</span>
        </div>

        @if($logs->isEmpty())
            <div class="empty-state">
                <i class="bi bi-shield-check"></i>
                <p>Tidak ada aktivitas tercatat untuk filter ini.</p>
            </div>
        @else
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Operator</th>
                        <th>Loket</th>
                        <th>Aksi</th>
                        <th>Antrian</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr>
                        <td class="time-muted">{{ $log->created_at->format('H:i:s') }}</td>
                        <td style="font-weight:600;">{{ $log->user_name }}</td>
                        <td style="color:var(--c-muted);">Loket {{ $log->loket_id }}</td>
                        <td>
                            <span class="action-badge ab-{{ $log->action }}">
                                {{ \App\Models\ActivityLog::actionLabel($log->action) }}
                            </span>
                        </td>
                        <td>
                            @if($log->kode_antrian)
                                <span class="kode-mono">{{ $log->kode_antrian }}</span>
                            @else
                                <span style="color:var(--c-muted);">—</span>
                            @endif
                        </td>
                        <td style="color:var(--c-muted);font-size:.8rem;">{{ $log->notes ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

</div>
@endsection
