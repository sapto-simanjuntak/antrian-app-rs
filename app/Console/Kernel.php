<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Optional: archive / delete old queue data (older than 7 days)
        $schedule->call(function () {
            \App\Models\Queue::where('created_at', '<', now()->subDays(7))->delete();
            \Illuminate\Support\Facades\Log::info('Queue cleanup: removed records older than 7 days.');
        })->dailyAt('00:05')->name('queue-cleanup')->withoutOverlapping();
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
