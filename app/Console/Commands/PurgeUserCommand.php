<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\Purge\UserPurgeService;
use Illuminate\Console\Command;

class PurgeUserCommand extends Command
{
    protected $signature = 'hub:purge-user
        {identifiers* : User ids or email addresses}
        {--dry-run : Report what would be removed without touching anything}
        {--ban : Soft-ban (status=0 + banned_emails) instead of deleting the user row}
        {--reason= : Reason recorded in the audit trail and ban list}';

    protected $description = 'Remove a spam/malicious user with all content, files, tokens and sessions (snapshot first)';

    public function handle(UserPurgeService $service): int
    {
        $failed = false;

        foreach ((array) $this->argument('identifiers') as $identifier) {
            $user = is_numeric($identifier)
                ? User::find((int) $identifier)
                : User::where('email', strtolower(trim((string) $identifier)))->first();

            if (! $user) {
                $this->error("User not found: {$identifier}");
                $failed = true;
                continue;
            }

            if ((int) ($user->access_level_id ?? 0) > 1 && ! $this->confirm("{$user->email} is a privileged account (access level {$user->access_level_id}). Purge anyway?")) {
                $this->warn("Skipped {$user->email}");
                continue;
            }

            $summary = $service->purge($user, [
                'ban' => (bool) $this->option('ban'),
                'dryRun' => (bool) $this->option('dry-run'),
                'reason' => (string) ($this->option('reason') ?: 'Purged via hub:purge-user'),
            ]);

            $mode = $summary['dry_run'] ? 'DRY RUN' : strtoupper($summary['mode']);
            $this->info("[{$mode}] {$summary['email']} (id {$summary['user_id']}): "
                ."{$summary['publications']} publications, {$summary['forums']} forums, "
                .count($summary['files']).' files');
            foreach ($summary['rows'] as $table => $count) {
                $this->line("  {$table}: {$count}");
            }
            if (isset($summary['snapshot'])) {
                $this->line('  snapshot: '.$summary['snapshot']);
            }
        }

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
