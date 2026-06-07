<?php

namespace App\Console\Commands;

use App\Services\HubStorageService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class RecoverHubStorageCommand extends Command
{
    protected $signature = 'hub:recover-storage
                            {--sync-legacy : Copy files from storage/uploads into storage/app/public/uploads (skips existing)}
                            {--migrate-host : After sync, copy uploads to the configured host files root}';

    protected $description = 'Diagnose and repair hub file storage (symlink, legacy paths, optional host migration)';

    public function handle(HubStorageService $hubStorage): int
    {
        $this->printDiagnostics($hubStorage);

        if ($this->option('sync-legacy')) {
            $this->syncLegacyUploads($hubStorage);
        }

        try {
            $hubStorage->ensurePublicStorageSymlink();
        } catch (\Throwable $e) {
            $this->error('Could not link public/storage: '.$e->getMessage());

            return self::FAILURE;
        }

        $link = public_path('storage');
        $filesRoot = $hubStorage->filesRoot();
        $linkOk = $hubStorage->publicStorageLinkOk();

        $this->newLine();
        $this->line('Active files root: '.$filesRoot);
        if (is_link($link)) {
            $this->line('public/storage -> '.readlink($link));
        }
        $this->line('public/storage link: '.($linkOk ? 'OK' : 'MISMATCH'));

        if ($this->option('migrate-host') && $hubStorage->needsLegacyToHostMigration()) {
            $this->newLine();
            $this->info('Migrating uploads to host files root…');
            $result = $hubStorage->migrateLegacyToHostPath(function (int $done, int $total) {
                $this->output->write("\r  {$done}/{$total}");
            });
            $this->newLine();
            $this->line($result['message'] ?? ($result['status'] ?? 'done'));
        }

        $this->newLine();
        if ($hubStorage->isUsingLegacyUploadFallback()) {
            $this->warn('Serving from legacy storage/app/public until host path has uploads.');
            $this->line('Run: php artisan hub:migrate-storage-to-host');
        } elseif ($linkOk) {
            $this->info('Storage recovery complete.');
        } else {
            $this->warn('Symlink may still need manual fix — check permissions on '.$filesRoot);

            return self::FAILURE;
        }

        $this->line('Clear caches: php artisan config:clear && php artisan view:clear');

        return self::SUCCESS;
    }

    protected function printDiagnostics(HubStorageService $hubStorage): void
    {
        $this->info('Hub storage diagnostics');
        $this->table(
            ['Setting', 'Value'],
            [
                ['Site storage ID', $hubStorage->siteStorageId()],
                ['Configured host path', $hubStorage->configuredInternalRoot()],
                ['Active files root', $hubStorage->filesRoot()],
                ['Legacy app/public', $hubStorage->legacyInternalRoot()],
                ['Old storage/uploads', $hubStorage->deprecatedUploadsRoot()],
                ['Legacy fallback active', $hubStorage->isUsingLegacyUploadFallback() ? 'yes' : 'no'],
                ['Host migration needed', $hubStorage->needsLegacyToHostMigration() ? 'yes' : 'no'],
                ['Uploads in app/public', $hubStorage->pathHasHubUploads($hubStorage->legacyInternalRoot()) ? 'yes' : 'no'],
                ['Uploads in storage/uploads', $this->deprecatedHasFiles($hubStorage) ? 'yes' : 'no'],
                ['Uploads on host path', $hubStorage->pathHasHubUploads($hubStorage->configuredInternalRoot()) ? 'yes' : 'no'],
            ]
        );
    }

    protected function deprecatedHasFiles(HubStorageService $hubStorage): bool
    {
        $root = $hubStorage->deprecatedUploadsRoot();
        foreach (config('hub_storage.content_prefixes', []) as $prefix) {
            $dir = $root.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, preg_replace('#^uploads/#', '', $prefix));
            if (! is_dir($dir)) {
                continue;
            }
            foreach (scandir($dir) ?: [] as $entry) {
                if ($entry !== '.' && $entry !== '..') {
                    return true;
                }
            }
        }

        return false;
    }

    protected function syncLegacyUploads(HubStorageService $hubStorage): void
    {
        $source = $hubStorage->deprecatedUploadsRoot();
        $dest = $hubStorage->legacyInternalRoot().'/uploads';

        if (! is_dir($source)) {
            $this->line('No storage/uploads directory — skipping legacy sync.');

            return;
        }

        File::ensureDirectoryExists($dest, 0775, true);

        $copied = 0;
        $skipped = 0;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $fileInfo) {
            if (! $fileInfo->isFile()) {
                continue;
            }
            $relative = substr($fileInfo->getPathname(), strlen($source) + 1);
            $target = $dest.DIRECTORY_SEPARATOR.$relative;
            File::ensureDirectoryExists(dirname($target), 0775, true);

            if (is_file($target)) {
                $skipped++;

                continue;
            }

            File::copy($fileInfo->getPathname(), $target);
            $copied++;
        }

        $this->info("Legacy sync: {$copied} copied, {$skipped} already present.");
    }
}
