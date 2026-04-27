@extends('layouts.app')

@section('title', $user ? 'Edit Pengguna' : 'Tambah Pengguna')
@section('role-icon', $user ? 'pencil-square' : 'person-plus-fill')
@section('role-label', $user ? 'Edit Pengguna' : 'Tambah Pengguna')

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

    .form-card { max-width: 560px; margin: 0 auto; background: var(--c-white); border: 1px solid var(--c-border); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); overflow: hidden; }
    .form-head { padding: 1.4rem 1.75rem; border-bottom: 1px solid var(--c-border); }
    .form-head h2 { font-size: 1.1rem; font-weight: 900; margin: 0; }
    .form-head p  { font-size: .82rem; color: var(--c-muted); margin: .2rem 0 0; }
    .form-body { padding: 1.75rem; display: flex; flex-direction: column; gap: 1.1rem; }
    .form-foot { padding: 1.1rem 1.75rem; border-top: 1px solid var(--c-border); background: var(--c-surface); display: flex; gap: .6rem; justify-content: flex-end; }

    .field label { display: block; font-size: .8rem; font-weight: 700; color: var(--c-muted); margin-bottom: .4rem; text-transform: uppercase; letter-spacing: .5px; }
    .field input, .field select {
        width: 100%; padding: .7rem .9rem;
        border: 1.5px solid var(--c-border); border-radius: 10px;
        font-family: var(--font); font-size: .9rem; color: var(--c-text);
        background: var(--c-white); transition: border-color .15s;
        outline: none;
    }
    .field input:focus, .field select:focus { border-color: var(--c-primary); }
    .field.has-error input, .field.has-error select { border-color: var(--c-red); }
    .field-error { font-size: .78rem; color: var(--c-red); font-weight: 600; margin-top: .3rem; }
    .field-hint  { font-size: .78rem; color: var(--c-muted); margin-top: .3rem; }

    .btn-primary { background: var(--c-primary); color: #fff; padding: 9px 22px; border-radius: 10px; font-family: var(--font); font-size: .9rem; font-weight: 700; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: .4rem; transition: filter .15s; }
    .btn-primary:hover { filter: brightness(.9); }
    .btn-cancel  { background: var(--c-surface); color: var(--c-muted); padding: 9px 18px; border-radius: 10px; font-family: var(--font); font-size: .9rem; font-weight: 700; border: 1.5px solid var(--c-border); text-decoration: none; display: inline-flex; align-items: center; gap: .4rem; transition: all .15s; }
    .btn-cancel:hover { border-color: var(--c-text); color: var(--c-text); }

    #loket-field { transition: opacity .2s; }
    #loket-field.hidden { opacity: 0; pointer-events: none; height: 0; overflow: hidden; margin: 0; }
</style>
@endpush

@section('content')

<div class="form-card">
    <div class="form-head">
        <h2>{{ $user ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}</h2>
        <p>{{ $user ? "Memperbarui akun {$user->name}" : 'Buat akun operator atau administrator baru' }}</p>
    </div>

    <form method="POST"
          action="{{ $user ? route('admin.users.update', $user) : route('admin.users.store') }}">
        @csrf
        @if($user) @method('PUT') @endif

        <div class="form-body">

            <div class="field {{ $errors->has('name') ? 'has-error' : '' }}">
                <label>Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', $user?->name) }}"
                       placeholder="Contoh: Budi Santoso" required>
                @error('name') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div class="field {{ $errors->has('email') ? 'has-error' : '' }}">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email', $user?->email) }}"
                       placeholder="email@rumahsakit.com" required>
                @error('email') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div class="field {{ $errors->has('password') ? 'has-error' : '' }}">
                <label>Password {{ $user ? '(kosongkan jika tidak diubah)' : '' }}</label>
                <input type="password" name="password" placeholder="{{ $user ? '••••••••' : 'Min. 8 karakter' }}"
                       {{ $user ? '' : 'required' }}>
                @error('password') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            @if(!$user || request()->has('_method'))
            <div class="field {{ $errors->has('password_confirmation') ? 'has-error' : '' }}">
                <label>Konfirmasi Password</label>
                <input type="password" name="password_confirmation" placeholder="Ulangi password">
            </div>
            @endif

            <div class="field {{ $errors->has('role') ? 'has-error' : '' }}">
                <label>Role</label>
                <select name="role" id="roleSelect" onchange="toggleLoket(this.value)">
                    <option value="operator" {{ old('role', $user?->role) === 'operator' ? 'selected' : '' }}>Operator</option>
                    <option value="admin"    {{ old('role', $user?->role) === 'admin'    ? 'selected' : '' }}>Administrator</option>
                </select>
                @error('role') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div class="field {{ $errors->has('loket_id') ? 'has-error' : '' }}" id="loket-field">
                <label>Loket yang Ditangani</label>
                <select name="loket_id">
                    <option value="">— Pilih Loket —</option>
                    <option value="1" {{ old('loket_id', $user?->loket_id) == 1 ? 'selected' : '' }}>Loket 1 — BPJS Kesehatan</option>
                    <option value="2" {{ old('loket_id', $user?->loket_id) == 2 ? 'selected' : '' }}>Loket 2 — Pasien Umum</option>
                    <option value="3" {{ old('loket_id', $user?->loket_id) == 3 ? 'selected' : '' }}>Loket 3 — Pasien Lansia</option>
                </select>
                @error('loket_id') <div class="field-error">{{ $message }}</div> @enderror
                <div class="field-hint">Operator hanya dapat mengakses loket yang ditugaskan.</div>
            </div>

        </div>

        <div class="form-foot">
            <a href="{{ route('admin.users') }}" class="btn-cancel">
                <i class="bi bi-x-lg"></i> Batal
            </a>
            <button type="submit" class="btn-primary">
                <i class="bi bi-{{ $user ? 'floppy-fill' : 'person-plus-fill' }}"></i>
                {{ $user ? 'Simpan Perubahan' : 'Buat Akun' }}
            </button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
    function toggleLoket(role) {
        const el = document.getElementById('loket-field');
        el.classList.toggle('hidden', role === 'admin');
    }
    toggleLoket(document.getElementById('roleSelect').value);
</script>
@endpush
