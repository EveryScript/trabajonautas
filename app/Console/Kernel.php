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
        // $schedule->command('inspire')->hourly();
        $schedule->command('trabajonautas:send-unnotified-clients')->dailyAt('20:00'); // Send notification to unnotified clients
        $schedule->command('trabajonautas:update-expired-accounts')->hourly(); // Convert to FREE when user is expired account
        // Send email in queue when: Client is register completed and User/Admin verified account (PRO of PRO-MAX)
        $schedule->command('queue:restart')->everyFiveMinutes();
        $schedule->command('queue:work --max-time=55 --stop-when-empty')->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
