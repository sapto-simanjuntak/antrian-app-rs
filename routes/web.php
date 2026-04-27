<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\KioskController;
use App\Http\Controllers\LoketController;
use App\Http\Controllers\DisplayController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Antrian RS — Route Definitions
|--------------------------------------------------------------------------
|
| Public  : /kiosk, /display  (pasien & TV publik — tidak perlu login)
| Auth    : /loket/*           (operator & admin — wajib login)
| Auth    : /login, /logout
|
*/

// ── Auth ─────────────────────────────────────────────────────────────────────
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')
    ->middleware('auth');

// ── Kiosk (Terminal Pasien — PUBLIC) ─────────────────────────────────────────
Route::prefix('kiosk')->name('kiosk.')->group(function () {
    Route::get('/',       [KioskController::class, 'index'])->name('index');
    Route::post('/ambil', [KioskController::class, 'ambil'])->name('ambil')
        ->middleware('throttle:kiosk');
});

// ── Display TV (PUBLIC) ───────────────────────────────────────────────────────
Route::prefix('display')->name('display.')->group(function () {
    Route::get('/',      [DisplayController::class, 'index'])->name('index');
    Route::get('/state', [DisplayController::class, 'state'])->name('state')
        ->middleware('throttle:60,1'); // display poll 2dtk = 30/mnt, beri headroom 2×
});

// ── Loket (PROTECTED — wajib login + cek akses loket) ────────────────────────
Route::prefix('loket/{loketId}')
    ->name('loket.')
    ->where(['loketId' => '[1-3]'])
    ->middleware(['auth', 'loket.access'])
    ->group(function () {
        Route::get('/',                [LoketController::class, 'index'])->name('index');
        Route::get('/state',           [LoketController::class, 'state'])->name('state');
        Route::post('/panggil',        [LoketController::class, 'panggil'])->name('panggil');
        Route::post('/panggil-ulang',  [LoketController::class, 'panggilUlang'])->name('panggil-ulang');
        Route::post('/pause',          [LoketController::class, 'pause'])->name('pause');
        Route::post('/selesai',        [LoketController::class, 'selesai'])->name('selesai');
        Route::post('/batal',          [LoketController::class, 'batal'])->name('batal');
        Route::post('/tidak-hadir',    [LoketController::class, 'tidakHadir'])->name('tidak-hadir');
    });

// ── Admin (PROTECTED — wajib login + role admin) ──────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/',                    [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users',               [AdminController::class, 'users'])->name('users');
    Route::get('/users/create',        [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users',              [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}/edit',   [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}',        [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}',     [AdminController::class, 'destroyUser'])->name('users.destroy');
    Route::get('/laporan',             [AdminController::class, 'laporan'])->name('laporan');
    Route::get('/laporan/export',      [AdminController::class, 'exportCsv'])->name('laporan.export');
    Route::get('/audit',               [AdminController::class, 'audit'])->name('audit');
});

// ── Root ─────────────────────────────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('kiosk.index'));
