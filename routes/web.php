<?php

use App\Http\Controllers\KioskController;
use App\Http\Controllers\LoketController;
use App\Http\Controllers\DisplayController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Antrian RS — Route Definitions
|--------------------------------------------------------------------------
|
| Dipisahkan per role:
|   /kiosk    → Terminal pasien (ambil nomor antrian)
|   /loket    → Operator loket (panggil, layani, selesai)
|   /display  → Layar TV / monitor besar
|
*/

// ── Kiosk (Terminal Pasien) ───────────────────────────────────────────────────
Route::prefix('kiosk')->name('kiosk.')->group(function () {
    Route::get('/',     [KioskController::class, 'index'])->name('index');
    Route::post('/ambil', [KioskController::class, 'ambil'])->name('ambil');
});

// ── Loket (Operator Counter) ──────────────────────────────────────────────────
Route::prefix('loket/{loketId}')
    ->name('loket.')
    ->where(['loketId' => '[1-3]'])
    ->group(function () {
        Route::get('/',               [LoketController::class, 'index'])->name('index');
        Route::get('/state',          [LoketController::class, 'state'])->name('state');
        Route::post('/panggil',       [LoketController::class, 'panggil'])->name('panggil');
        Route::post('/panggil-ulang', [LoketController::class, 'panggilUlang'])->name('panggil-ulang');
        Route::post('/pause',         [LoketController::class, 'pause'])->name('pause');
        Route::post('/selesai',       [LoketController::class, 'selesai'])->name('selesai');
        Route::post('/batal',         [LoketController::class, 'batal'])->name('batal');
    });

// ── Display TV ────────────────────────────────────────────────────────────────
Route::prefix('display')->name('display.')->group(function () {
    Route::get('/',       [DisplayController::class, 'index'])->name('index');
    Route::get('/state',  [DisplayController::class, 'state'])->name('state');
});

// ── Root → Kiosk ─────────────────────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('kiosk.index'));
