@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('role-icon', 'people-fill')
@section('role-label', 'Manajemen Pengguna')

@section('nav-extra')
    <div style="display:flex;gap:.4rem;margin-right:.25rem;">
        <a href="{{ route('admin.dashboard') }}" class="admin-nav-link">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="{{ route('admin.users') }}" class="admin-nav-link active">
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
        padding: 5px 13px; border-radius: 8px; font-size: .8rem; font-weight: 700;
        color: var(--c-muted); text-decoration: none;
        display: inline-flex; align-items: center; gap: .35rem;
        transition: all .15s; border: 1.5px solid transparent;
    }
    .admin-nav-link:hover  { background: var(--c-surface); color: var(--c-text); }
    .admin-nav-link.active { background: var(--c-primary-soft); color: var(--c-primary); border-color: rgba(0,87,255,.15); }

    .pg-header { margin-bottom: 1.75rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; }
    .pg-header div h1 { font-size: 1.4rem; font-weight: 900; margin: 0; }
    .pg-header div p  { color: var(--c-muted); font-size: .88rem; margin: .2rem 0 0; }

    .card { background: var(--c-white); border: 1px solid var(--c-border); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); }
    table { width: 100%; border-collapse: collapse; }
    th { font-size: .73rem; font-weight: 800; text-transform: uppercase; letter-spacing: .7px; color: var(--c-muted); padding: .85rem 1.25rem; border-bottom: 1.5px solid var(--c-border); text-align: left; }
    td { padding: .85rem 1.25rem; border-bottom: 1px solid var(--c-border); font-size: .88rem; vertical-align: middle; }
    tr:last-child td { border-bottom: none; }
    tr:hover td { background: var(--c-surface); }

    .role-badge { display: inline-flex; align-items: center; gap: .3rem; padding: 3px 10px; border-radius: 999px; font-size: .74rem; font-weight: 800; }
    .role-admin    { background: var(--c-violet-soft); color: var(--c-violet); }
    .role-operator { background: var(--c-primary-soft); color: var(--c-primary); }

    .loket-chip { display: inline-flex; align-items: center; gap: .3rem; padding: 3px 10px; border-radius: 999px; font-size: .74rem; font-weight: 700; background: var(--c-surface); color: var(--c-muted); border: 1px solid var(--c-border); }

    .btn-sm { padding: 5px 12px; border-radius: 8px; font-size: .78rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: .3rem; transition: all .15s; cursor: pointer; border: none; }
    .btn-edit   { background: var(--c-surface); color: var(--c-text); border: 1px solid var(--c-border); }
    .btn-edit:hover   { border-color: var(--c-primary); color: var(--c-primary); }
    .btn-delete { background: var(--c-red-soft); color: var(--c-red); }
    .btn-delete:hover { filter: brightness(.92); }
    .btn-primary { background: var(--c-primary); color: #fff; padding: 8px 18px; border-radius: 10px; font-size: .88rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: .4rem; transition: filter .15s; }
    .btn-primary:hover { filter: brightness(.9); color: #fff; }

    .flash { padding: .9rem 1.2rem; border-radius: 10px; font-size: .88rem; font-weight: 600; margin-bottom: 1.25rem; display: flex; align-items: center; gap: .5rem; }
    .flash-success { background: var(--c-green-soft); color: #006844; }
    .flash-error   { background: var(--c-red-soft); color: var(--c-red); }

    .self-row td { background: var(--c-primary-soft) !important; }
</style>
@endpush

@section('content')
<div style="max-width:900px;margin:0 auto;">

    <div class="pg-header">
        <div>
            <h1>Manajemen Pengguna</h1>
            <p>Kelola akun operator dan administrator sistem</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn-primary">
            <i class="bi bi-person-plus-fill"></i> Tambah Pengguna
        </a>
    </div>

    @if(session('success'))
        <div class="flash flash-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="flash flash-error"><i class="bi bi-x-circle-fill"></i> {{ session('error') }}</div>
    @endif

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Loket</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                <tr class="{{ $u->id === auth()->id() ? 'self-row' : '' }}">
                    <td>
                        <div style="font-weight:700;">{{ $u->name }}</div>
                        @if($u->id === auth()->id())
                            <div style="font-size:.72rem;color:var(--c-primary);font-weight:700;">— Anda</div>
                        @endif
                    </td>
                    <td style="color:var(--c-muted);">{{ $u->email }}</td>
                    <td>
                        <span class="role-badge {{ $u->isAdmin() ? 'role-admin' : 'role-operator' }}">
                            <i class="bi bi-{{ $u->isAdmin() ? 'shield-fill' : 'person-badge-fill' }}"></i>
                            {{ $u->isAdmin() ? 'Admin' : 'Operator' }}
                        </span>
                    </td>
                    <td>
                        @if($u->isAdmin())
                            <span class="loket-chip"><i class="bi bi-infinity"></i> Semua Loket</span>
                        @elseif($u->loket_id)
                            <span class="loket-chip"><i class="bi bi-door-open"></i> Loket {{ $u->loket_id }}</span>
                        @else
                            <span style="color:var(--c-muted);font-size:.8rem;">—</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:.4rem;justify-content:flex-end;">
                            <a href="{{ route('admin.users.edit', $u) }}" class="btn-sm btn-edit">
                                <i class="bi bi-pencil-fill"></i> Edit
                            </a>
                            @if($u->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.destroy', $u) }}"
                                  onsubmit="return confirm('Hapus akun {{ $u->name }}? Tindakan ini tidak dapat dibatalkan.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-sm btn-delete">
                                    <i class="bi bi-trash3-fill"></i> Hapus
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;color:var(--c-muted);padding:2rem;">
                        Belum ada pengguna terdaftar.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
