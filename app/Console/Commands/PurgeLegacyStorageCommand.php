<?php

namespace App\Console\Commands;

use App\Services\HubStorageService;
use Illuminate\Console\Command;

class PurgeLegacyStorageCommand extends Command
{
    protected $signature = 'hub:purge-legacy-storage
                            {--dry-run : Report what would be deleted without removing files}';

    protected $description = 'Remove verified legacy upload copies from storage/app/public after host migration';

    public function handle(HubStorageService $hubStorage): int
    {
        $preview = $hubStorage->previewPurgeLegacyInternalStorage();

        $this->line('Legacy root: '.$preview['legacy_root']);
        $this->line('Host root: '.$preview['host_root']);
        $this->line('Legacy upload files: '.$preview['total']);
        $this->line('Verified on host: '.$preview['verified']);
        $this->line('Skipped (missing/mismatch): '.$preview['skipped']);
        $this->line('Reclaimable size: '.$this->formatBytes((int) $preview['bytes']));

        if (($preview['skipped_samples'] ?? []) !== []) {
            $this->warn('Examples not verified on host:');
            foreach ($preview['skipped_samples'] as $sample) {
                $this->line('  '.$sample);
            }
        }

        if (! ($preview['can_purge'] ?? false)) {
            $this->warn('Nothing to purge. Complete host migration and confirm files on the host path first.');

            return self::SUCCESS;
        }

        if (($preview['skipped'] ?? 0) > 0) {
            $this->error('Purge blocked: not every legacy file is verified on the host path. Re-run migration or resolve mismatches first.');

            return self::FAILURE;
        }

        if ($this->option('dry-run')) {
            $result = $hubStorage->purgeLegacyInternalStorage(true);
            $this->info($result['message'] ?? 'Dry run complete.');

            return self::SUCCESS;
        }

        if (! $this->confirm('Delete verified legacy copies from '.$preview['legacy_root'].'?', false)) {
            $this->warn('Cancelled.');

            return self::SUCCESS;
        }

        $result = $hubStorage->purgeLegacyInternalStorage(false);
        if (($result['status'] ?? '') === 'completed') {
            $this->info($result['message'] ?? 'Purge completed.');

            return self::SUCCESS;
        }

        $this->error($result['message'] ?? 'Purge did not complete.');

        return self::FAILURE;
    }

    protected function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes.' B';
        }

        $units = ['KB', 'MB', 'GB', 'TB'];
        $value = (float) $bytes;
        foreach ($units as $unit) {
            $value /= 1024;
            if ($value < 1024) {
                return sprintf('%.1f %s', $value, $unit);
            }
        }

        return sprintf('%.1f PB', $value / 1024);
    }
}
