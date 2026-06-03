<?php

namespace App\Console\Commands;

use App\Services\InstallerService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class KhubMarkInstalledCommand extends Command
{
    protected $signature = 'khub:mark-installed';

    protected $description = 'Mark an existing deployment as installed (skips web installer)';

    public function handle(InstallerService $installer): int
    {
        File::ensureDirectoryExists(dirname(config('install.lock_file')));
        File::put(config('install.lock_file'), now()->toIso8601String());

        $installer->writeEnvValues([
            'APP_INSTALLED' => 'true',
            'INSTALLER_DISABLED' => 'true',
        ]);

        $installer->lockInstallerInSettings();

        $this->info('Application marked as installed. Web installer is locked.');

        return self::SUCCESS;
    }
}
