<?php

namespace App\Console\Commands;

use App\Services\HubStorageService;
use Illuminate\Console\Command;

class LinkHubStorageCommand extends Command
{
    protected $signature = 'hub:link-storage';

    protected $description = 'Link public/storage to the hub files root (macOS, Linux, and Windows)';

    public function handle(HubStorageService $hubStorage): int
    {
        try {
            $hubStorage->ensureHostDataDirectories();
            $hubStorage->ensurePublicStorageSymlink();
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $filesRoot = $hubStorage->filesRoot();
        $link = public_path('storage');
        $ok = $hubStorage->publicStorageLinkOk($link, $filesRoot);

        $this->line('Hub files root: '.$filesRoot);
        if (is_link($link)) {
            $this->line('public/storage -> '.readlink($link));
        } elseif (is_dir($link)) {
            $resolved = realpath($link) ?: $link;
            $this->line('public/storage -> '.$resolved.(PHP_OS_FAMILY === 'Windows' ? ' (junction/directory)' : ''));
        } else {
            $this->line('public/storage: not linked');
        }

        if (! $ok) {
            $this->error('public/storage is not pointing at the hub files root.');

            return self::FAILURE;
        }

        $this->info('public/storage is ready.');

        return self::SUCCESS;
    }
}
