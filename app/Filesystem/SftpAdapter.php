<?php

namespace App\Filesystem;

use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Adapter\Polyfill\NotSupportingVisibilityTrait;
use League\Flysystem\Config;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Net\SFTP;

/**
 * Flysystem v1 SFTP adapter using phpseclib ^3 (already required by Laravel Passport).
 *
 * Avoids league/flysystem-sftp, which conflicts with phpseclib 3.x in this project.
 */
class SftpAdapter extends AbstractAdapter
{
    use NotSupportingVisibilityTrait;

    private string $host;

    private int $port;

    private string $username;

    private ?string $password;

    private ?string $privateKey;

    private ?string $passphrase;

    private int $timeout;

    private string $remoteRoot;

    private ?SFTP $connection = null;

    public function __construct(array $config)
    {
        $this->host = (string) ($config['host'] ?? '');
        $this->port = (int) ($config['port'] ?? 22);
        $this->username = (string) ($config['username'] ?? '');
        $this->password = isset($config['password']) ? (string) $config['password'] : null;
        $this->privateKey = isset($config['private_key']) ? (string) $config['private_key'] : null;
        if ($this->privateKey === null && ! empty($config['privateKey'])) {
            $this->privateKey = (string) $config['privateKey'];
        }
        $this->passphrase = isset($config['passphrase']) ? (string) $config['passphrase'] : null;
        $this->timeout = (int) ($config['timeout'] ?? 30);
        $this->remoteRoot = rtrim((string) ($config['root'] ?? '/'), '/');

        $prefix = trim((string) ($config['path_prefix'] ?? ''), '/');
        $this->setPathPrefix($prefix);
    }

    public function write($path, $contents, Config $config)
    {
        $remote = $this->remotePath($path);
        $this->ensureParentDirectory(dirname($remote));
        if (! $this->sftp()->put($remote, $contents, SFTP::SOURCE_STRING)) {
            throw new \RuntimeException('SFTP write failed for '.$remote);
        }

        return ['type' => 'file', 'path' => $this->applyPathPrefix($path), 'size' => strlen((string) $contents)];
    }

    public function writeStream($path, $resource, Config $config)
    {
        return $this->write($path, stream_get_contents($resource), $config);
    }

    public function update($path, $contents, Config $config)
    {
        return $this->write($path, $contents, $config);
    }

    public function updateStream($path, $resource, Config $config)
    {
        return $this->writeStream($path, $resource, $config);
    }

    public function rename($path, $newpath)
    {
        $from = $this->remotePath($path);
        $to = $this->remotePath($newpath);
        if (! $this->sftp()->rename($from, $to)) {
            throw new \RuntimeException('SFTP rename failed.');
        }

        return true;
    }

    public function copy($path, $newpath)
    {
        return $this->write($newpath, $this->read($path)['contents'], new Config());
    }

    public function delete($path)
    {
        $remote = $this->remotePath($path);
        if (! $this->sftp()->delete($remote)) {
            throw new \RuntimeException('SFTP delete failed for '.$remote);
        }

        return true;
    }

    public function deleteDir($dirname)
    {
        $remote = $this->remotePath($dirname);
        if (! $this->sftp()->rmdir($remote)) {
            throw new \RuntimeException('SFTP delete directory failed for '.$remote);
        }

        return true;
    }

    public function createDir($dirname, Config $config)
    {
        $remote = $this->remotePath($dirname);
        if (! $this->sftp()->is_dir($remote) && ! $this->sftp()->mkdir($remote, 0775, true)) {
            throw new \RuntimeException('SFTP mkdir failed for '.$remote);
        }

        return ['type' => 'dir', 'path' => trim($this->applyPathPrefix($dirname), '/')];
    }

    public function has($path)
    {
        $remote = $this->remotePath($path);

        return $this->sftp()->file_exists($remote) || $this->sftp()->is_dir($remote);
    }

    public function read($path)
    {
        $remote = $this->remotePath($path);
        $contents = $this->sftp()->get($remote);
        if ($contents === false) {
            throw new \RuntimeException('SFTP read failed for '.$remote);
        }

        return [
            'type' => 'file',
            'path' => $this->applyPathPrefix($path),
            'contents' => $contents,
        ];
    }

    public function readStream($path)
    {
        $data = $this->read($path);
        $resource = fopen('php://temp', 'r+');
        fwrite($resource, $data['contents']);
        rewind($resource);
        $data['stream'] = $resource;

        return $data;
    }

    public function listContents($directory = '', $recursive = false)
    {
        $remoteDir = $this->remotePath($directory);
        $listing = $this->sftp()->rawlist($remoteDir) ?: [];
        $items = [];
        $base = trim($this->applyPathPrefix($directory), '/');

        foreach ($listing as $name => $meta) {
            if ($name === '.' || $name === '..') {
                continue;
            }
            $childPath = $base === '' ? $name : $base.'/'.$name;
            $type = ($meta['type'] ?? 0) === SFTP::TYPE_DIRECTORY ? 'dir' : 'file';
            $item = [
                'type' => $type,
                'path' => $childPath,
            ];
            if ($type === 'file') {
                $item['size'] = (int) ($meta['size'] ?? 0);
                $item['timestamp'] = (int) ($meta['mtime'] ?? time());
            }
            $items[] = $item;

            if ($recursive && $type === 'dir') {
                $items = array_merge($items, $this->listContents($childPath, true));
            }
        }

        return $items;
    }

    public function getMetadata($path)
    {
        $remote = $this->remotePath($path);
        $stat = $this->sftp()->stat($remote);
        if ($stat === false) {
            throw new \RuntimeException('SFTP stat failed for '.$remote);
        }

        return [
            'type' => $this->sftp()->is_dir($remote) ? 'dir' : 'file',
            'path' => $this->applyPathPrefix($path),
            'size' => (int) ($stat['size'] ?? 0),
            'timestamp' => (int) ($stat['mtime'] ?? time()),
        ];
    }

    public function getSize($path)
    {
        return $this->getMetadata($path);
    }

    public function getMimetype($path)
    {
        $meta = $this->getMetadata($path);
        $meta['mimetype'] = 'application/octet-stream';

        return $meta;
    }

    public function getTimestamp($path)
    {
        return $this->getMetadata($path);
    }

    public function probe(): void
    {
        if (! $this->sftp()->is_dir($this->remoteRoot) && ! $this->sftp()->mkdir($this->remoteRoot, 0775, true)) {
            throw new \RuntimeException('SFTP root is not accessible: '.$this->remoteRoot);
        }
    }

    private function sftp(): SFTP
    {
        if ($this->connection !== null) {
            return $this->connection;
        }

        if (! class_exists(SFTP::class)) {
            throw new \RuntimeException('SFTP requires phpseclib/phpseclib ^3 (install via composer require phpseclib/phpseclib:^3).');
        }

        $sftp = new SFTP($this->host, $this->port, $this->timeout);
        $credential = $this->resolveCredential();
        if (! $sftp->login($this->username, $credential)) {
            throw new \RuntimeException('SFTP authentication failed for '.$this->username.'@'.$this->host);
        }

        $this->connection = $sftp;

        return $this->connection;
    }

    /**
     * @return string|\phpseclib3\Crypt\Common\AsymmetricKey
     */
    private function resolveCredential()
    {
        if ($this->privateKey) {
            $keyMaterial = is_readable($this->privateKey)
                ? file_get_contents($this->privateKey)
                : $this->privateKey;

            return PublicKeyLoader::load($keyMaterial, $this->passphrase ?? false);
        }

        return $this->password ?? '';
    }

    private function remotePath(string $path): string
    {
        $path = trim(str_replace(['\\', '..'], ['/', ''], $path), '/');
        $prefixed = trim($this->getPathPrefix(), '/');
        if ($prefixed !== '') {
            $path = $path === '' ? $prefixed : $prefixed.'/'.$path;
        }

        return $this->remoteRoot.($path === '' ? '' : '/'.$path);
    }

    private function ensureParentDirectory(string $remoteDir): void
    {
        $remoteDir = rtrim($remoteDir, '/');
        if ($remoteDir === '' || $remoteDir === $this->remoteRoot) {
            return;
        }
        if (! $this->sftp()->is_dir($remoteDir)) {
            $this->sftp()->mkdir($remoteDir, 0775, true);
        }
    }
}
