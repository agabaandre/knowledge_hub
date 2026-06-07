<?php

namespace App\Filesystem;

use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Adapter\Polyfill\NotSupportingVisibilityTrait;
use League\Flysystem\Config;

/**
 * Minimal Flysystem v1 FTP adapter for offsite SQL backup uploads.
 */
class FtpAdapter extends AbstractAdapter
{
    use NotSupportingVisibilityTrait;

    private string $host;

    private int $port;

    private string $username;

    private ?string $password;

    private bool $ssl;

    private bool $passive;

    private string $remoteRoot;

    private int $timeout;

    /** @var resource|null */
    private $connection = null;

    public function __construct(array $config)
    {
        $this->host = (string) ($config['host'] ?? '');
        $this->port = (int) ($config['port'] ?? 21);
        $this->username = (string) ($config['username'] ?? '');
        $this->password = isset($config['password']) ? (string) $config['password'] : null;
        $this->ssl = (bool) ($config['ssl'] ?? false);
        $this->passive = (bool) ($config['passive'] ?? true);
        $this->timeout = (int) ($config['timeout'] ?? 30);
        $this->remoteRoot = rtrim((string) ($config['root'] ?? '/'), '/');

        $prefix = trim((string) ($config['path_prefix'] ?? ''), '/');
        $this->setPathPrefix($prefix);
    }

    public function write($path, $contents, Config $config)
    {
        $remote = $this->remotePath($path);
        $this->ensureParentDirectory(dirname($remote));
        $stream = fopen('php://temp', 'r+');
        if ($stream === false) {
            throw new \RuntimeException('FTP write failed: could not open temp stream.');
        }
        fwrite($stream, (string) $contents);
        rewind($stream);
        $ok = @ftp_fput($this->ftp(), $remote, $stream, FTP_BINARY);
        fclose($stream);
        if (! $ok) {
            throw new \RuntimeException('FTP write failed for '.$remote);
        }

        return ['type' => 'file', 'path' => $this->applyPathPrefix($path), 'size' => strlen((string) $contents)];
    }

    public function writeStream($path, $resource, Config $config)
    {
        $remote = $this->remotePath($path);
        $this->ensureParentDirectory(dirname($remote));
        if (! @ftp_fput($this->ftp(), $remote, $resource, FTP_BINARY)) {
            throw new \RuntimeException('FTP stream write failed for '.$remote);
        }

        return ['type' => 'file', 'path' => $this->applyPathPrefix($path)];
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
        return @ftp_rename($this->ftp(), $this->remotePath($path), $this->remotePath($newpath));
    }

    public function copy($path, $newpath)
    {
        $contents = $this->read($path);

        return $this->write($newpath, $contents['contents'], new Config());
    }

    public function delete($path)
    {
        return @ftp_delete($this->ftp(), $this->remotePath($path));
    }

    public function deleteDir($dirname)
    {
        return false;
    }

    public function createDir($dirname, Config $config)
    {
        $remote = $this->remotePath($dirname);
        if (@ftp_chdir($this->ftp(), $remote)) {
            return ['path' => $this->applyPathPrefix($dirname), 'type' => 'dir'];
        }
        if (! @ftp_mkdir($this->ftp(), $remote)) {
            return false;
        }

        return ['path' => $this->applyPathPrefix($dirname), 'type' => 'dir'];
    }

    public function has($path)
    {
        $size = @ftp_size($this->ftp(), $this->remotePath($path));

        return $size !== -1;
    }

    public function read($path)
    {
        $stream = fopen('php://temp', 'r+');
        if ($stream === false || ! @ftp_fget($this->ftp(), $stream, $this->remotePath($path), FTP_BINARY)) {
            if (is_resource($stream)) {
                fclose($stream);
            }
            throw new \RuntimeException('FTP read failed for '.$path);
        }
        rewind($stream);
        $contents = stream_get_contents($stream);
        fclose($stream);

        return ['type' => 'file', 'path' => $this->applyPathPrefix($path), 'contents' => $contents];
    }

    public function readStream($path)
    {
        $stream = fopen('php://temp', 'r+');
        if ($stream === false || ! @ftp_fget($this->ftp(), $stream, $this->remotePath($path), FTP_BINARY)) {
            if (is_resource($stream)) {
                fclose($stream);
            }
            throw new \RuntimeException('FTP read stream failed for '.$path);
        }
        rewind($stream);

        return ['type' => 'file', 'path' => $this->applyPathPrefix($path), 'stream' => $stream];
    }

    public function listContents($directory = '', $recursive = false)
    {
        return [];
    }

    public function getMetadata($path)
    {
        $size = @ftp_size($this->ftp(), $this->remotePath($path));
        if ($size === -1) {
            return false;
        }

        return [
            'type' => 'file',
            'path' => $this->applyPathPrefix($path),
            'size' => $size,
            'timestamp' => time(),
        ];
    }

    public function getSize($path)
    {
        return $this->getMetadata($path);
    }

    public function getMimetype($path)
    {
        $meta = $this->getMetadata($path);
        if ($meta === false) {
            return false;
        }
        $meta['mimetype'] = 'application/octet-stream';

        return $meta;
    }

    public function getTimestamp($path)
    {
        return $this->getMetadata($path);
    }

    public function probe(): void
    {
        $this->ftp();
        if (! @ftp_chdir($this->ftp(), $this->remoteRoot)) {
            if (! @ftp_mkdir($this->ftp(), $this->remoteRoot)) {
                throw new \RuntimeException('FTP root is not accessible: '.$this->remoteRoot);
            }
            @ftp_chdir($this->ftp(), $this->remoteRoot);
        }
    }

    /** @return resource */
    private function ftp()
    {
        if (is_resource($this->connection)) {
            return $this->connection;
        }

        if (! function_exists('ftp_connect')) {
            throw new \RuntimeException('PHP FTP extension is not enabled.');
        }

        $connection = $this->ssl
            ? @ftp_ssl_connect($this->host, $this->port, $this->timeout)
            : @ftp_connect($this->host, $this->port, $this->timeout);

        if ($connection === false) {
            throw new \RuntimeException('FTP connection failed for '.$this->host.':'.$this->port);
        }

        if (! @ftp_login($connection, $this->username, $this->password ?? '')) {
            throw new \RuntimeException('FTP authentication failed for '.$this->username.'@'.$this->host);
        }

        if ($this->passive) {
            @ftp_pasv($connection, true);
        }

        $this->connection = $connection;

        return $this->connection;
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

        if (@ftp_chdir($this->ftp(), $remoteDir)) {
            return;
        }

        $this->ensureParentDirectory(dirname($remoteDir));
        @ftp_mkdir($this->ftp(), $remoteDir);
    }
}
