<?php

namespace App\Console\Commands;

use App\Services\HubDatabaseBackupService;
use Illuminate\Console\Command;

class HubDatabaseBackup extends Command
{
    protected $signature = 'hub:backup-database {--full : Run a full backup instead of incremental}';

    protected $description = 'Backup critical Knowledge Hub tables to SQL files outside application storage';

    public function handle(HubDatabaseBackupService $backup): int
    {
        $result = $backup->runBackup(! $this->option('full'));
        $this->info("Backed up {$result['tables']} tables to {$result['path']}");

        return 0;
    }
}
