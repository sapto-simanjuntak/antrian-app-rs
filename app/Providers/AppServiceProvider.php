<?php

namespace App\Providers;

use App\Services\QueueService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind QueueService as singleton — one instance for entire request lifecycle
        $this->app->singleton(QueueService::class, fn() => new QueueService());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Carbon\Carbon::setLocale('id');

        // Rate limiter untuk kiosk terminal pasien:
        // maks 10 request/menit & 200/hari per IP — cegah spam tanpa blokir pasien legit
        RateLimiter::for('kiosk', function (Request $request) {
            return [
                Limit::perMinute(10)->by($request->ip())
                    ->response(fn() => response()->json([
                        'success' => false,
                        'message' => 'Terlalu banyak permintaan. Harap tunggu sebentar sebelum mengambil nomor antrian lagi.',
                    ], 429)),
                Limit::perDay(200)->by($request->ip())
                    ->response(fn() => response()->json([
                        'success' => false,
                        'message' => 'Batas harian tercapai. Hubungi petugas jika Anda membutuhkan bantuan.',
                    ], 429)),
            ];
        });
    }
}
