@extends('layouts.app')

@section('title', 'Laporan Harian')
@section('role-icon', 'bar-chart-fill')
@section('role-label', 'Laporan Harian')

@section('nav-extra')
    <div style="display:flex;gap:.4rem;margin-right:.25rem;">
        <a href="{{ route('admin.dashboard') }}" class="admin-nav-link">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="{{ route('admin.users') }}" class="admin-nav-link">
            <i class="bi bi-people-fill"></i> Pengguna
        </a>
        <a href="{{ route('admin.laporan') }}" class="admin-nav-link active">
            <i class="bi bi-bar-chart-fill"></i> Laporan
        </a>
        <a href="{{ route('admin.audit') }}" class="admin-nav-link">
            <i class="bi bi-shield-lock-fill"></i> Audit
        </a>
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

    .date-form { display: flex; align-items: center; gap: .6rem; }
    .date-form input[type="date"] {
        padding: 7px 12px; border: 1.5px solid var(--c-border); border-radius: 10px;
        font-family: var(--font); font-size: .88rem; color: var(--c-text); outline: none;
        transition: border-color .15s;
    }
    .date-form input[type="date"]:focus { border-color: var(--c-primary); }
    .date-form button { padding: 8px 16px; background: var(--c-primary); color: #fff; border: none; border-radius: 10px; font-family: var(--font); font-size: .88rem; font-weight: 700; cursor: pointer; transition: filter .15s; }
    .date-form button:hover { filter: brightness(.9); }

    .summary-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 1rem; margin-bottom: 1.5rem; }
    @media(max-width:768px) { .summary-grid { grid-template-columns: 1fr; } }

    .sum-card { background: var(--c-white); border: 1px solid var(--c-border); border-radius: var(--radius-lg); padding: 1.2rem 1.4rem; box-shadow: var(--shadow-sm); }
    .sum-card-head { display: flex; align-items: center; gap: .7rem; margin-bottom: 1rem; padding-bottom: .75rem; border-bottom: 1px solid var(--c-border); }
    .sum-card-icon { width: 38px; height: 38px; border-radius: 9px; display: grid; place-items: center; font-size: 1rem; flex-shrink: 0; }
    .sc-1 .sum-card-icon { background: var(--c-primary-soft); color: var(--c-primary); }
    .sc-2 .sum-card-icon { background: var(--c-green-soft);   color: var(--c-green); }
    .sc-3 .sum-card-icon { background: var(--c-amber-soft);   color: var(--c-amber); }
    .sum-card-head h3 { font-size: .9rem; font-weight: 800; margin: 0; }
    .sum-card-head p  { font-size: .73rem; color: var(--c-muted); margin: 0; }

    .sum-stats { display: grid; grid-template-columns: repeat(2,1fr); gap: .6rem; }
    .ss { background: var(--c-surface); border-radius: 8px; padding: .6rem .8rem; }
    .ss .v { font-size: 1.4rem; font-weight: 900; line-height: 1; }
    .ss .l { font-size: .68rem; font-weight: 700; color: var(--c-muted); text-transform: uppercase; letter-spacing: .5px; margin-top: .15rem; }
    .sum-avg { margin-top: .7rem; font-size: .8rem; color: var(--c-muted); font-weight: 600; }
    .sum-avg strong { color: var(--c-text); }

    .card { background: var(--c-white); border: 1px solid var(--c-border); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); overflow: hidden; }
    .card-head { padding: 1rem 1.4rem; border-bottom: 1px solid var(--c-border); display: flex; align-items: center; justify-content: space-between; }
    .card-head h2 { font-size: .98rem; font-weight: 800; margin: 0; }
    .card-head span { font-size: .8rem; color: var(--c-muted); font-weight: 600; }

    table { width: 100%; border-collapse: collapse; }
    th { font-size: .72rem; font-weight: 800; text-transform: uppercase; letter-spacing: .6px; color: var(--c-muted); padding: .75rem 1.1rem; border-bottom: 1.5px solid var(--c-border); text-align: left; white-space: nowrap; }
    td { padding: .72rem 1.1rem; border-bottom: 1px solid var(--c-border); font-size: .86rem; vertical-align: middle; }
    tr:last-child td { border-bottom: none; }
    tr:hover td { background: var(--c-surface); }

    .kode-mono { font-family: var(--font-mono); font-size: .9rem; font-weight: 500; }
    .status-badge { display: inline-flex; align-items: center; gap: .3rem; padding: 3px 9px; border-radius: 999px; font-size: .72rem; font-weight: 800; }
    .badge-waiting   { background: var(--c-primary-soft); color: var(--c-primary); }
    .badge-calling   { background: var(--c-amber-soft);   color: #7a5800; }
    .badge-paused    { background: var(--c-amber-soft);   color: #7a5800; }
    .badge-done      { background: var(--c-green-soft);   color: #006844; }
    .badge-cancelled { background: var(--c-red-soft);     color: var(--c-red); }

    .empty-state { text-align: center; padding: 3rem 1rem; color: var(--c-muted); }
    .empty-state i { font-size: 2.5rem; opacity: .4; }
    .empty-state p { margin: .5rem 0 0; font-size: .9rem; }

    .btn-export {
        padding: 8px 16px; background: var(--c-green); color: #fff;
        border-radius: 10px; font-size: .88rem; font-weight: 700;
        text-decoration: none; display: inline-flex; align-items: center; gap: .4rem;
        transition: filter .15s;
    }
    .btn-export:hover { filter: brightness(.9); color: #fff; }
    .btn-export.disabled { opacity: .45; pointer-events: none; }
    .header-actions { display: flex; align-items: center; gap: .6rem; flex-wrap: wrap; }
</style>
@endpush

@section('content')
<div style="max-width:1100px;margin:0 auto;">

    <div class="pg-header">
        <div>
            <h1>Laporan Harian</h1>
            <p>Data antrian per tanggal — klik tanggal lain untuk melihat riwayat</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.laporan.export', ['tanggal' => $tanggal]) }}"
               class="btn-export {{ $queues->isEmpty() ? 'disabled' : '' }}">
                <i class="bi bi-file-earmark-spreadsheet-fill"></i> Export Excel
            </a>
        </div>
        <form class="date-form" method="GET" action="{{ route('admin.laporan') }}">
            <input type="date" name="tanggal" value="{{ $tanggal }}" max="{{ today()->toDateString() }}">
            <button type="submit"><i class="bi bi-search"></i> Tampilkan</button>
        </form>
    </div>

    {{-- Summary per loket --}}
    <div class="summary-grid">
        @foreach($summary as $id => $lk)
        <div class="sum-card sc-{{ $id }}">
            <div class="sum-card-head">
                <div class="sum-card-icon"><i class="bi bi-{{ $lk['info']['icon'] }}-fill" onerror="this.className='bi bi-hospital'"></i></div>
                <div>
                    <h3>Loket {{ $id }}</h3>
                    <p>{{ $lk['info']['label'] }}</p>
                </div>
            </div>
            <div class="sum-stats">
                <div class="ss">
                    <div class="v" style="color:var(--c-primary)">{{ $lk['total'] }}</div>
                    <div class="l">Total Masuk</div>
                </div>
                <div class="ss">
                    <div class="v" style="color:var(--c-green)">{{ $lk['done'] }}</div>
                    <div class="l">Selesai</div>
                </div>
                <div class="ss">
                    <div class="v" style="color:var(--c-red)">{{ $lk['cancelled'] }}</div>
                    <div class="l">Dibatalkan</div>
                </div>
                <div class="ss">
                    <div class="v" style="color:var(--c-amber)">{{ $lk['waiting'] }}</div>
                    <div class="l">Belum Selesai</div>
                </div>
            </div>
            <div class="sum-avg">Rata-rata layanan: <strong>{{ $lk['avg_human'] }}</strong></div>
        </div>
        @endforeach
    </div>

    {{-- Detail table --}}
    <div class="card">
        <div class="card-head">
            <h2><i class="bi bi-list-ul"></i> Detail Antrian — {{ \Carbon\Carbon::parse($tanggal)->locale('id')->isoFormat('D MMMM Y') }}</h2>
            <span>{{ $queues->count() }} antrian</span>
        </div>

        @if($queues->isEmpty())
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <p>Tidak ada data antrian untuk tanggal ini.</p>
            </div>
        @else
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Loket</th>
                        <th>Status</th>
                        <th>Waktu Ambil</th>
                        <th>Waktu Panggil</th>
                        <th>Waktu Selesai</th>
                        <th>Durasi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($queues as $q)
                    <tr>
                        <td><span class="kode-mono">{{ $q->kode_antrian }}</span></td>
                        <td style="color:var(--c-muted);">Loket {{ $q->loket_id }}</td>
                        <td>
                            <span class="status-badge badge-{{ $q->status }}">
                                {{ $q->status_label }}
                            </span>
                        </td>
                        <td style="color:var(--c-muted);">{{ $q->created_at->format('H:i:s') }}</td>
                        <td style="color:var(--c-muted);">{{ $q->called_at?->format('H:i:s') ?? '—' }}</td>
                        <td style="color:var(--c-muted);">{{ $q->done_at?->format('H:i:s') ?? '—' }}</td>
                        <td>{{ $q->duration_human }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

</div>
@endsection
