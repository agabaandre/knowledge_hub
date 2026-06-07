<?php

namespace App\Console\Commands;

use App\Services\HubOffsiteBackupService;
use Illuminate\Console\Command;

class HubOffsiteBackup extends Command
{
    protected $signature = 'hub:offsite-backup {--reuse-latest : Upload the latest full backup instead of creating a new one}';

    protected $description = 'Upload a full SQL backup archive to the configured offsite storage';

    public function handle(HubOffsiteBackupService $offsite): int
    {
        if (! $offsite->isEnabled()) {
            $this->warn('Offsite backup is disabled.');

            return 0;
        }

        $result = $offsite->runWeeklyUpload(! $this->option('reuse-latest'));
        if (($result['status'] ?? '') === 'ok') {
            $this->info($result['message']);

            return 0;
        }

        if (($result['status'] ?? '') === 'skipped') {
            $this->warn($result['message']);

            return 0;
        }

        $this->error($result['message'] ?? 'Offsite backup failed.');

        return 1;
    }
}
