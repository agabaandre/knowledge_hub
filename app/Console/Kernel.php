<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        
        $schedule->command('moodle:fetch-courses')->hourly();
        $schedule->command('telescope:prune --hours=4')->daily();
        // Purge publications rejected for 90+ days without appeal
        $schedule->command('publications:purge-rejected --days=90')->dailyAt('02:15');
        // Prune expired community invitations
        $schedule->command('invitations:prune-expired')->dailyAt('03:00');
        // Automatically close expired events
        $schedule->command('events:close-expired')->dailyAt('04:00');
        
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
