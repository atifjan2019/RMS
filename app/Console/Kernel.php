<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\PollReviewsJob;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Poll reviews hourly as a fallback for missed webhooks
        $schedule->job(new PollReviewsJob)->hourly();
        
        // Sync Google Business Profile data daily at 2 AM
        $schedule->command('gbp:sync-data')->dailyAt('02:00');
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
