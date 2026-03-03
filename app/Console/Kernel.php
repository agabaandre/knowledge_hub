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
        // Send daily approval summary emails to approvers
        $schedule->command('approvals:daily-summary')->dailyAt('08:00');
        // Award community badges at the beginning of each month for the previous month
        $schedule->command('badges:award-community')->monthlyOn(1, '01:00');
        // Cache forum and community counts every 5 minutes for menu badges
        $schedule->command('cache:forum-community-counts')->everyFiveMinutes();
        // Clean up PDF chat sessions older than 7 days (saved chats linked to user profiles)
        $schedule->command('pdf-chat:prune --days=7')->dailyAt('03:30');
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
