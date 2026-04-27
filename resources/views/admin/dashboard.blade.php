@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('role-icon', 'speedometer2')
@section('role-label', 'Administrator')

@section('nav-extra')
    <div style="display:flex;gap:.4rem;margin-right:.25rem;">
        <a href="{{ route('admin.dashboard') }}" class="admin-nav-link active">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="{{ route('admin.users') }}" class="admin-nav-link">
            <i class="bi bi-people-fill"></i> Pengguna
        </a>
        <a href="{{ route('admin.laporan') }}" class="admin-nav-link">
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
        padding: 5px 13px;
        border-radius: 8px;
        font-size: .8rem;
        font-weight: 700;
        color: var(--c-muted);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        transition: all .15s;
        border: 1.5px solid transparent;
    }
    .admin-nav-link:hover { background: var(--c-surface); color: var(--c-text); }
    .admin-nav-link.active { background: var(--c-primary-soft); color: var(--c-primary); border-color: rgba(0,87,255,.15); }

    .pg-header { margin-bottom: 1.75rem; }
    .pg-header h1 { font-size: 1.4rem; font-weight: 900; margin: 0; }
    .pg-header p  { color: var(--c-muted); font-size: .88rem; margin: .2rem 0 0; }

    .stat-grid  { display: grid; grid-template-columns: repeat(3,1fr); gap: 1rem; margin-bottom: 1.5rem; }
    .loket-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 1rem; }
    @media(max-width:768px) {
        .stat-grid, .loket-grid { grid-template-columns: 1fr; }
    }

    .stat-card {
        background: var(--c-white); border: 1px solid var(--c-border);
        border-radius: var(--radius-lg); padding: 1.3rem 1.5rem;
        box-shadow: var(--shadow-sm);
    }
    .stat-card .sc-label { font-size: .78rem; font-weight: 700; color: var(--c-muted); text-transform: uppercase; letter-spacing: .8px; }
    .stat-card .sc-value { font-size: 2.2rem; font-weight: 900; line-height: 1.1; margin: .3rem 0; }
    .stat-card .sc-sub   { font-size: .8rem; color: var(--c-muted); }

    .lk-card {
        background: var(--c-white); border: 1px solid var(--c-border);
        border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-sm);
    }
    .lk-card-head {
        padding: 1rem 1.3rem;
        display: flex; align-items: center; gap: .75rem;
        border-bottom: 1px solid var(--c-border);
    }
    .lk-card-icon { width: 40px; height: 40px; border-radius: 10px; display: grid; place-items: center; font-size: 1.1rem; flex-shrink: 0; }
    .lk-1 .lk-card-icon { background: var(--c-primary-soft); color: var(--c-primary); }
    .lk-2 .lk-card-icon { background: var(--c-green-soft);   color: var(--c-green); }
    .lk-3 .lk-card-icon { background: var(--c-amber-soft);   color: var(--c-amber); }
    .lk-card-head h3 { font-size: .95rem; font-weight: 800; margin: 0; }
    .lk-card-head p  { font-size: .75rem; color: var(--c-muted); margin: 0; }

    .lk-stats { display: grid; grid-template-columns: repeat(4,1fr); text-align: center; }
    .lk-stat  { padding: .9rem .5rem; border-right: 1px solid var(--c-border); }
    .lk-stat:last-child { border-right: none; }
    .lk-stat .v { font-size: 1.5rem; font-weight: 900; line-height: 1; }
    .lk-stat .l { font-size: .68rem; font-weight: 700; color: var(--c-muted); text-transform: uppercase; letter-spacing: .5px; margin-top: .25rem; }

    .lk-card-foot {
        padding: .7rem 1.3rem;
        background: var(--c-surface);
        border-top: 1px solid var(--c-border);
        display: flex; align-items: center; justify-content: space-between;
        font-size: .8rem;
    }
    .lk-card-foot .avg { color: var(--c-muted); font-weight: 600; }
    .lk-card-foot .avg strong { color: var(--c-text); }

    .btn-goto {
        padding: 5px 13px; border-radius: 8px; font-size: .78rem; font-weight: 700;
        text-decoration: none; display: inline-flex; align-items: center; gap: .35rem;
        transition: all .15s;
    }
    .lk-1 .btn-goto { background: var(--c-primary-soft); color: var(--c-primary); }
    .lk-2 .btn-goto { background: var(--c-green-soft);   color: var(--c-green); }
    .lk-3 .btn-goto { background: var(--c-amber-soft);   color: #7a5800; }
    .btn-goto:hover { filter: brightness(.92); }
</style>
@endpush

@section('content')
<div style="max-width:1100px;margin:0 auto;">

    <div class="pg-header">
        <h1>Dashboard Administrator</h1>
        <p>Ringkasan operasional hari ini — {{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
    </div>

    {{-- Summary cards --}}
    <div class="stat-grid">
        <div class="stat-card">
            <div class="sc-label"><i class="bi bi-ticket-perforated"></i> Total Antrian Hari Ini</div>
            <div class="sc-value" style="color:var(--c-primary)">{{ $totalToday }}</div>
            <div class="sc-sub">Semua loket gabungan</div>
        </div>
        <div class="stat-card">
            <div class="sc-label"><i class="bi bi-check-circle-fill"></i> Total Dilayani</div>
            <div class="sc-value" style="color:var(--c-green)">{{ $totalDone }}</div>
            <div class="sc-sub">Status selesai hari ini</div>
        </div>
        <div class="stat-card">
            <div class="sc-label"><i class="bi bi-people-fill"></i> Total Pengguna Sistem</div>
            <div class="sc-value" style="color:var(--c-violet)">{{ $userCount }}</div>
            <div class="sc-sub"><a href="{{ route('admin.users') }}" style="color:var(--c-primary);font-weight:700;text-decoration:none;">Kelola pengguna →</a></div>
        </div>
    </div>

    {{-- Per-loket cards --}}
    <div class="loket-grid">
        @foreach ($summary as $id => $lk)
        <div class="lk-card lk-{{ $id }}">
            <div class="lk-card-head">
                <div class="lk-card-icon"><i class="bi bi-{{ $lk['info']['icon'] }}-fill" onerror="this.className='bi bi-hospital'"></i></div>
                <div>
                    <h3>Loket {{ $id }}</h3>
                    <p>{{ $lk['info']['label'] }}</p>
                </div>
            </div>
            <div class="lk-stats">
                <div class="lk-stat">
                    <div class="v" style="color:var(--c-primary)">{{ $lk['waiting'] }}</div>
                    <div class="l">Menunggu</div>
                </div>
                <div class="lk-stat">
                    <div class="v" style="color:var(--c-amber)">{{ $lk['calling'] }}</div>
                    <div class="l">Dipanggil</div>
                </div>
                <div class="lk-stat">
                    <div class="v" style="color:var(--c-green)">{{ $lk['done'] }}</div>
                    <div class="l">Selesai</div>
                </div>
                <div class="lk-stat">
                    <div class="v" style="color:var(--c-red)">{{ $lk['cancelled'] }}</div>
                    <div class="l">Batal</div>
                </div>
            </div>
            <div class="lk-card-foot">
                <span class="avg">Rata-rata: <strong>{{ $lk['avg_human'] }}</strong></span>
                <a href="{{ route('loket.index', $id) }}" class="btn-goto">
                    <i class="bi bi-box-arrow-up-right"></i> Buka Loket
                </a>
            </div>
        </div>
        @endforeach
    </div>

</div>
@endsection
