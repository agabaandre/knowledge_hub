<?php

namespace App\Console\Commands;

use App\Services\HubStorageService;
use Illuminate\Console\Command;

class MigrateHostStorageCommand extends Command
{
    protected $signature = 'hub:migrate-storage-to-host';

    protected $description = 'Copy uploads from storage/app/public to the configured host files root';

    public function handle(HubStorageService $hubStorage): int
    {
        if (! $hubStorage->needsLegacyToHostMigration()) {
            $this->info('No legacy-to-host migration needed.');

            return self::SUCCESS;
        }

        $this->line('Source: '.$hubStorage->legacyInternalRoot());
        $this->line('Destination: '.$hubStorage->configuredInternalRoot());

        $result = $hubStorage->migrateLegacyToHostPath(function (int $done, int $total, string $file) {
            $this->output->write("\r  {$done}/{$total} — {$file}".str_repeat(' ', 20));
        });

        $this->newLine();
        $status = $result['status'] ?? 'unknown';
        if ($status === 'completed') {
            $this->info('Migration completed: '.($result['files'] ?? 0).' file(s) copied.');

            return self::SUCCESS;
        }

        $this->warn($result['message'] ?? 'Migration skipped.');

        return $status === 'skipped' ? self::SUCCESS : self::FAILURE;
    }
}
