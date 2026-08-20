<?php

namespace App\Services\FederationProvision;

use RuntimeException;

class ProvisionFilesystem
{
    public function __construct(private readonly SudoRunner $sudo)
    {
    }

    public function targetPath(string $slug): string
    {
        return rtrim((string) config('federation_provision.target_parent'), '/').'/'.$slug;
    }

    public function sourceRoot(): string
    {
        return rtrim((string) config('federation_provision.source_root'), '/');
    }

    public function assertCopyPossible(string $slug): void
    {
        $source = $this->sourceRoot();
        if (! is_dir($source)) {
            throw new RuntimeException('Source root does not exist: '.$source);
        }

        $target = $this->targetPath($slug);
        if (is_dir($target) || is_file($target)) {
            throw new RuntimeException('Target path already exists: '.$target);
        }
    }

    public function copyInstance(string $slug): string
    {
        $source = $this->sourceRoot();
        $target = $this->targetPath($slug);
        $parent = dirname($target);

        $this->sudo->run(['mkdir', '-p', $parent]);

        $excludes = config('federation_provision.rsync_excludes', []);
        $excludeArgs = '';
        foreach ($excludes as $pattern) {
            $excludeArgs .= ' --exclude='.escapeshellarg($pattern);
        }

        $cmd = sprintf(
            'rsync -a %s %s/ %s/',
            $excludeArgs,
            escapeshellarg($source),
            escapeshellarg($target)
        );
        $this->sudo->runShell($cmd, null, 3600);

        $user = (string) config('federation_provision.web_user', 'www-data');
        $group = (string) config('federation_provision.web_group', 'www-data');
        $this->sudo->run(['chown', '-R', $user.':'.$group, $target]);

        // Ensure writable runtime dirs exist even if excluded from rsync content.
        foreach ([
            'storage',
            'storage/app',
            'storage/logs',
            'storage/framework',
            'storage/framework/cache',
            'storage/framework/sessions',
            'storage/framework/views',
            'bootstrap/cache',
            'public/uploads',
        ] as $dir) {
            $this->sudo->run(['mkdir', '-p', $target.'/'.$dir]);
        }
        $this->sudo->run(['chown', '-R', $user.':'.$group, $target.'/storage', $target.'/bootstrap/cache', $target.'/public/uploads']);
        $this->sudo->run(['chmod', '-R', 'ug+rwx', $target.'/storage', $target.'/bootstrap/cache', $target.'/public/uploads']);

        // Drop continental install lock if somehow copied.
        $lock = $target.'/storage/app/installed.lock';
        $this->sudo->runShell('rm -f '.escapeshellarg($lock));

        // Ensure .env can be written by the web installer / artisan.
        $envPath = $target.'/.env';
        $this->sudo->runShell(
            'touch '.escapeshellarg($envPath)
            .' && chown '.$user.':'.$group.' '.escapeshellarg($envPath)
            .' && chmod ug+rw '.escapeshellarg($envPath)
        );

        return $target;
    }

    public function removeTarget(string $slug): void
    {
        $target = $this->targetPath($slug);
        if (! is_dir($target)) {
            return;
        }

        $parent = rtrim((string) config('federation_provision.target_parent'), '/');
        if (! str_starts_with(realpath($target) ?: $target, $parent)) {
            throw new RuntimeException('Refusing to delete path outside target parent: '.$target);
        }

        $this->sudo->run(['rm', '-rf', $target]);
    }

    public function writeFileAsRoot(string $path, string $contents): void
    {
        $tmp = tempnam(sys_get_temp_dir(), 'khubprov');
        if ($tmp === false) {
            throw new RuntimeException('Could not create temp file.');
        }
        file_put_contents($tmp, $contents);
        $this->sudo->run(['cp', $tmp, $path]);
        @unlink($tmp);

        $user = (string) config('federation_provision.web_user', 'www-data');
        $group = (string) config('federation_provision.web_group', 'www-data');
        if (str_contains($path, '/var/www/')) {
            $this->sudo->run(['chown', $user.':'.$group, $path]);
        }
    }
}
