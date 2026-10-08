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

        // Send notification and email to clients PRO-MAX still not notified in all day
        $schedule->command('trabajonautas:send-unnotified-clients')->dailyAt('20:30');
        // Send notification and email to clients PRO or PRO-MAX if their account will be expire in 2 days
        $schedule->command('trabajonautas:send-expiring-account-notification-clients')->dailyAt('07:00');
        // Delete clients if register_completed = false
        $schedule->command('trabajonautas:delete-incomplete-clients')->dailyAt('20:00');
        // Set account FREE to all clients if expired time is after now
        $schedule->command('trabajonautas:update-expired-accounts')->hourly();

        $schedule->command('queue:restart')->everyFiveMinutes();
        $schedule->command('queue:work --max-time=55 --stop-when-empty')->everyMinute();
        $schedule->command('queue:prune-failed --hours=168')->weekly(); // Clear failed_jobs after 3 trying times 
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
