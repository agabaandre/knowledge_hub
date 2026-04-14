<?php

namespace App\Console\Commands;

use App\Models\JobTitle;
use App\Models\User;
use Illuminate\Console\Command;

class FixUserJobTitleIds extends Command
{
    protected $signature = 'users:fix-job-title-ids {--dry-run : Show what would change without updating rows}';
    protected $description = 'Replace numeric job_title values on users with the matching job title name.';

    public function handle(): int
    {
        $isDryRun = (bool) $this->option('dry-run');

        $users = User::query()
            ->whereNotNull('job_title')
            ->where('job_title', 'REGEXP', '^[0-9]+$')
            ->get(['id', 'name', 'email', 'job_title']);

        if ($users->isEmpty()) {
            $this->info('No users with numeric job_title values found.');

            return self::SUCCESS;
        }

        $updated = 0;
        $skipped = 0;

        foreach ($users as $user) {
            $jobTitleId = (int) $user->job_title;
            $jobName = JobTitle::query()->whereKey($jobTitleId)->value('name');

            if (! $jobName) {
                $skipped++;
                $this->warn("Skipped user #{$user->id}: no JobTitle found for id={$jobTitleId}");
                continue;
            }

            if ($isDryRun) {
                $this->line("Would update user #{$user->id} ({$user->email}): {$user->job_title} -> {$jobName}");
                continue;
            }

            $user->job_title = $jobName;
            $user->save();
            $updated++;
        }

        if ($isDryRun) {
            $this->info("Dry run complete. Matched {$users->count()} users.");
            return self::SUCCESS;
        }

        $this->info("Done. Updated {$updated} users. Skipped {$skipped} users.");

        return self::SUCCESS;
    }
}

