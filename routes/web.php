<?php

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
    Route::get('/',        [KioskController::class, 'index'])->name('index');
    Route::post('/ambil',  [KioskController::class, 'ambil'])->name('ambil');
});

// ── Display TV (PUBLIC) ───────────────────────────────────────────────────────
Route::prefix('display')->name('display.')->group(function () {
    Route::get('/',       [DisplayController::class, 'index'])->name('index');
    Route::get('/state',  [DisplayController::class, 'state'])->name('state');
});

// ── Loket (PROTECTED — wajib login + cek akses loket) ────────────────────────
Route::prefix('loket/{loketId}')
    ->name('loket.')
    ->where(['loketId' => '[1-3]'])
    ->middleware(['auth', 'loket.access'])
    ->group(function () {
        Route::get('/',               [LoketController::class, 'index'])->name('index');
        Route::get('/state',          [LoketController::class, 'state'])->name('state');
        Route::post('/panggil',       [LoketController::class, 'panggil'])->name('panggil');
        Route::post('/panggil-ulang', [LoketController::class, 'panggilUlang'])->name('panggil-ulang');
        Route::post('/pause',         [LoketController::class, 'pause'])->name('pause');
        Route::post('/selesai',       [LoketController::class, 'selesai'])->name('selesai');
        Route::post('/batal',         [LoketController::class, 'batal'])->name('batal');
    });

// ── Root ─────────────────────────────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('kiosk.index'));
