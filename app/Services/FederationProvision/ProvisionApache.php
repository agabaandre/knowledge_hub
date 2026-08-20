<?php

namespace App\Services\FederationProvision;

use RuntimeException;

class ProvisionApache
{
    public function __construct(
        private readonly SudoRunner $sudo,
        private readonly ProvisionFilesystem $filesystem
    ) {
    }

    public function vhostPath(): string
    {
        return (string) config('federation_provision.apache_vhost');
    }

    public function assertAliasAvailable(string $slug): void
    {
        $path = $this->vhostPath();
        if (! is_readable($path) && ! is_file($path)) {
            // May still be writable via sudo; try reading via sudo cat.
            try {
                $contents = $this->sudo->run(['cat', $path]);
            } catch (\Throwable $e) {
                throw new RuntimeException('Apache vhost is not readable: '.$path);
            }
        } else {
            $contents = (string) file_get_contents($path);
        }

        if ($this->aliasExists($contents, $slug)) {
            throw new RuntimeException('Apache Alias already exists for /'.$slug);
        }
    }

    public function addAlias(string $slug): void
    {
        $path = $this->vhostPath();
        $targetPublic = $this->filesystem->targetPath($slug).'/public';
        $snippet = $this->buildSnippet($slug, $targetPublic);

        $contents = $this->readVhost($path);
        if ($this->aliasExists($contents, $slug)) {
            return;
        }

        $updated = $this->insertSnippet($contents, $snippet);
        $this->filesystem->writeFileAsRoot($path, $updated);

        try {
            $this->sudo->run(['apache2ctl', 'configtest']);
        } catch (\Throwable $e) {
            $this->removeAlias($slug);
            throw new RuntimeException('Apache configtest failed after adding Alias /'.$slug.': '.$e->getMessage());
        }

        $this->reload();
    }

    public function removeAlias(string $slug): void
    {
        $path = $this->vhostPath();
        try {
            $contents = $this->readVhost($path);
        } catch (\Throwable) {
            return;
        }

        $updated = $this->stripSnippet($contents, $slug);
        if ($updated === $contents) {
            return;
        }

        $this->filesystem->writeFileAsRoot($path, $updated);

        try {
            $this->sudo->run(['apache2ctl', 'configtest']);
            $this->reload();
        } catch (\Throwable) {
            // best effort during cleanup
        }
    }

    public function buildSnippet(string $slug, string $targetPublic): string
    {
        $slug = ProvisionSlug::normalize($slug);

        return <<<APACHE

    # BEGIN KHUB_FEDERATION_HUB /{$slug}
    Alias /{$slug} {$targetPublic}
    <Directory {$targetPublic}>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    # END KHUB_FEDERATION_HUB /{$slug}

APACHE;
    }

    public function aliasExists(string $vhostContents, string $slug): bool
    {
        $slug = ProvisionSlug::normalize($slug);

        return str_contains($vhostContents, '# BEGIN KHUB_FEDERATION_HUB /'.$slug)
            || preg_match('/Alias\s+\/'.preg_quote($slug, '/').'\s+/i', $vhostContents) === 1;
    }

    public function insertSnippet(string $contents, string $snippet): string
    {
        if (preg_match('/<\/VirtualHost>/i', $contents)) {
            return preg_replace('/<\/VirtualHost>/i', $snippet."\n</VirtualHost>", $contents, 1) ?? ($contents.$snippet);
        }

        return rtrim($contents).$snippet;
    }

    public function stripSnippet(string $contents, string $slug): string
    {
        $slug = ProvisionSlug::normalize($slug);
        $pattern = '/\n?\s*# BEGIN KHUB_FEDERATION_HUB \/'.preg_quote($slug, '/').'.*?# END KHUB_FEDERATION_HUB \/'.preg_quote($slug, '/').'\s*/s';

        $updated = preg_replace($pattern, "\n", $contents);
        if ($updated !== null && $updated !== $contents) {
            return $updated;
        }

        // Fallback: remove Alias + following Directory block for this public path.
        $targetPublic = preg_quote($this->filesystem->targetPath($slug).'/public', '/');
        $fallback = '/\n?\s*Alias\s+\/'.preg_quote($slug, '/').'\s+'.$targetPublic.'\s*\n\s*<Directory\s+'.$targetPublic.'>.*?<\/Directory>\s*/is';

        return preg_replace($fallback, "\n", $contents) ?? $contents;
    }

    protected function readVhost(string $path): string
    {
        if (is_readable($path)) {
            return (string) file_get_contents($path);
        }

        return $this->sudo->run(['cat', $path]);
    }

    protected function reload(): void
    {
        try {
            $this->sudo->run(['systemctl', 'reload', 'apache2']);
        } catch (\Throwable) {
            $this->sudo->run(['apache2ctl', 'graceful']);
        }
    }
}
