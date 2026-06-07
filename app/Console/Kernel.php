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
        
        $schedule->command('learning:fetch-courses')->hourly();
        $schedule->command('telescope:prune --hours=4')->daily();
        // Purge publications rejected for 90+ days without appeal
        $schedule->command('publications:purge-rejected --days=90')->dailyAt('02:15');
        // Prune expired community invitations
        $schedule->command('invitations:prune-expired')->dailyAt('03:00');
        // Clean rejected community membership requests (remove from table daily at midnight)
        $schedule->command('commsofpractice:clean-rejected')->dailyAt('00:00');
        // Automatically close expired events
        $schedule->command('events:close-expired')->dailyAt('04:00');
        // Send daily approval summary emails to approvers
        $schedule->command('approvals:daily-summary')->dailyAt('08:00');
        // Weekly digest to mailing list subscribers (new resources, forums, community activity)
        $schedule->command('mailing:weekly-digest')->weeklyOn(1, '09:00');
        // Fetch new items from RSS feeds weekly
        $schedule->command('rss:fetch')->weeklyOn(2, '03:00');
        // Refresh Africa health “Did you know?” facts (OpenAI + fallback) weekly
        $schedule->command('facts:refresh-ai')->weeklyOn(1, '05:30');
        // Award community badges at the beginning of each month for the previous month (queued)
        $schedule->job(new \App\Jobs\AwardCommunityBadgesJob(null, null, 'scheduler'))->monthlyOn(1, '01:00');
        // Monthly profile-completion reminder (optional; disabled by default in admin settings)
        if (settings()->auto_profile_completion_reminder ?? false) {
            $day = (int) (settings()->profile_reminder_day_of_month ?? 1);
            $day = max(1, min(28, $day));
            $schedule->command('profiles:remind-incomplete')->monthlyOn($day, '09:00');
        }
        // Refresh published OWID indicator values weekly when automatic fetch is enabled
        if (kpi_owid_auto_fetch_enabled() && ! kpi_manual_data_only()) {
            $schedule->command('kpi:sync-owid --discover --sync --published-only')->weeklyOn(0, '04:30');
        }
        // Clean up PDF chat sessions older than 7 days (when enabled in admin settings)
        if (settings()->enable_ai_chat_prune ?? true) {
            $schedule->command('pdf-chat:prune --days=7')->dailyAt('03:30');
        }
        $schedule->command('hub:backup-database')
            ->dailyAt(config('hub_storage.backup_schedule_time', '01:30'))
            ->when(function () {
                try {
                    return \Illuminate\Support\Facades\Schema::hasTable('hub_storage_settings')
                        && app(\App\Services\HubStorageService::class)->settings()->auto_sql_backup;
                } catch (\Throwable $e) {
                    return false;
                }
            });
        $schedule->command('hub:offsite-backup')
            ->weeklyOn(
                (int) config('hub_storage.offsite_backup_schedule_day', 0),
                config('hub_storage.offsite_backup_schedule_time', '02:15')
            )
            ->when(function () {
                try {
                    return \Illuminate\Support\Facades\Schema::hasTable('hub_storage_settings')
                        && app(\App\Services\HubOffsiteBackupService::class)->isEnabled();
                } catch (\Throwable $e) {
                    return false;
                }
            });
        // Sync public content from country hubs (continental portal only)
        $schedule->command('federation:sync')
            ->dailyAt('02:45')
            ->when(function () {
                try {
                    return function_exists('federation_consumer_enabled') && federation_consumer_enabled();
                } catch (\Throwable $e) {
                    return false;
                }
            });
        $schedule->command('federation:refresh-central-token')
            ->hourly()
            ->when(function () {
                try {
                    return function_exists('hub_admin_units_enabled') && hub_admin_units_enabled();
                } catch (\Throwable $e) {
                    return false;
                }
            });
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
