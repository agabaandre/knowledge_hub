<?php

namespace App\Filesystem;

use GuzzleHttp\Client;
use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Adapter\Polyfill\NotSupportingVisibilityTrait;
use League\Flysystem\Config;

/**
 * Flysystem v1 adapter for SharePoint / OneDrive document libraries via Microsoft Graph.
 *
 * @see https://learn.microsoft.com/en-us/graph/api/resources/onedrive
 * @see https://learn.microsoft.com/en-us/graph/api/driveitem-put-content
 */
class SharePointGraphAdapter extends AbstractAdapter
{
    use NotSupportingVisibilityTrait;

    private Client $http;

    private string $tenantId;

    private string $clientId;

    private string $clientSecret;

    private ?string $driveId;

    private ?string $siteId;

    private ?string $siteHostname;

    private ?string $sitePath;

    private ?string $accessToken = null;

    private ?int $tokenExpiresAt = null;

    public function __construct(array $config)
    {
        $this->tenantId = (string) ($config['tenant_id'] ?? '');
        $this->clientId = (string) ($config['client_id'] ?? '');
        $this->clientSecret = (string) ($config['client_secret'] ?? '');
        $this->driveId = ! empty($config['drive_id']) ? (string) $config['drive_id'] : null;
        $this->siteId = ! empty($config['site_id']) ? (string) $config['site_id'] : null;
        $this->siteHostname = ! empty($config['site_hostname']) ? (string) $config['site_hostname'] : null;
        $this->sitePath = ! empty($config['site_path']) ? (string) $config['site_path'] : null;
        $prefix = trim((string) ($config['prefix'] ?? $config['root_prefix'] ?? ''), '/');
        $this->setPathPrefix($prefix);

        $this->http = new Client([
            'base_uri' => 'https://graph.microsoft.com/v1.0/',
            'timeout' => 120,
        ]);
    }

    public function write($path, $contents, Config $config)
    {
        $path = $this->applyPathPrefix($path);
        $response = $this->graphRequest('PUT', $this->itemContentPath($path), [
            'headers' => ['Content-Type' => 'application/octet-stream'],
            'body' => $contents,
        ]);

        return ['type' => 'file', 'path' => $path, 'size' => strlen((string) $contents)];
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
        $path = $this->applyPathPrefix($path);
        $newpath = $this->applyPathPrefix($newpath);
        $this->graphRequest('PATCH', $this->itemPath($path), [
            'json' => [
                'parentReference' => ['path' => $this->parentReferencePath(dirname($newpath))],
                'name' => basename($newpath),
            ],
        ]);

        return true;
    }

    public function copy($path, $newpath)
    {
        return $this->write($newpath, $this->read($path), new Config());
    }

    public function delete($path)
    {
        $path = $this->applyPathPrefix($path);
        $this->graphRequest('DELETE', $this->itemPath($path));

        return true;
    }

    public function deleteDir($dirname)
    {
        return $this->delete($dirname);
    }

    public function createDir($dirname, Config $config)
    {
        $dirname = trim($this->applyPathPrefix($dirname), '/');
        if ($dirname === '') {
            return ['type' => 'dir', 'path' => ''];
        }

        $parts = explode('/', $dirname);
        $current = '';
        foreach ($parts as $part) {
            $current = $current === '' ? $part : $current.'/'.$part;
            if (! $this->has($this->removePathPrefix($current))) {
                $this->createSingleDir($current);
            }
        }

        return ['type' => 'dir', 'path' => $dirname];
    }

    public function has($path)
    {
        try {
            $this->getMetadata($path);

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function read($path)
    {
        $path = $this->applyPathPrefix($path);
        $response = $this->graphRequest('GET', $this->itemContentPath($path));

        return ['type' => 'file', 'path' => $path, 'contents' => (string) $response->getBody()];
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
        $directory = trim($this->applyPathPrefix($directory), '/');
        $response = $this->graphRequest('GET', $this->childrenPath($directory));
        $payload = json_decode((string) $response->getBody(), true);
        $items = [];

        foreach ($payload['value'] ?? [] as $entry) {
            $name = $entry['name'] ?? '';
            $childPath = $directory === '' ? $name : $directory.'/'.$name;
            if (isset($entry['folder'])) {
                $items[] = [
                    'type' => 'dir',
                    'path' => $this->removePathPrefix($childPath),
                ];
            } else {
                $items[] = [
                    'type' => 'file',
                    'path' => $this->removePathPrefix($childPath),
                    'size' => (int) ($entry['size'] ?? 0),
                    'timestamp' => isset($entry['lastModifiedDateTime']) ? strtotime($entry['lastModifiedDateTime']) : time(),
                ];
            }
        }

        return $items;
    }

    public function getMetadata($path)
    {
        $path = $this->applyPathPrefix($path);
        $response = $this->graphRequest('GET', $this->itemPath($path));
        $entry = json_decode((string) $response->getBody(), true);

        return [
            'type' => isset($entry['folder']) ? 'dir' : 'file',
            'path' => $path,
            'size' => (int) ($entry['size'] ?? 0),
            'timestamp' => isset($entry['lastModifiedDateTime']) ? strtotime($entry['lastModifiedDateTime']) : time(),
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
        $this->resolveDriveId();
        $this->graphRequest('GET', 'drives/'.$this->resolveDriveId().'/root');
    }

    private function createSingleDir(string $dirname): void
    {
        $parent = trim(dirname($dirname), '/.');
        $this->graphRequest('POST', $this->childrenPath($parent), [
            'json' => [
                'name' => basename($dirname),
                'folder' => new \stdClass(),
                '@microsoft.graph.conflictBehavior' => 'replace',
            ],
        ]);
    }

    private function resolveDriveId(): string
    {
        if ($this->driveId) {
            return $this->driveId;
        }

        if ($this->siteId) {
            $response = $this->graphRequest('GET', 'sites/'.$this->siteId.'/drive');
            $payload = json_decode((string) $response->getBody(), true);
            $this->driveId = (string) ($payload['id'] ?? '');

            return $this->driveId;
        }

        if ($this->siteHostname && $this->sitePath) {
            $siteKey = $this->siteHostname.':/'.$this->trimSitePath($this->sitePath).':';
            $response = $this->graphRequest('GET', 'sites/'.$siteKey.'/drive');
            $payload = json_decode((string) $response->getBody(), true);
            $this->driveId = (string) ($payload['id'] ?? '');

            return $this->driveId;
        }

        throw new \RuntimeException('SharePoint drive not configured. Provide drive_id or site_id, or site_hostname + site_path.');
    }

    private function trimSitePath(string $path): string
    {
        return trim($path, '/');
    }

    private function itemPath(string $path): string
    {
        $driveId = $this->resolveDriveId();
        $encoded = $this->encodePath($path);

        return "drives/{$driveId}/root:/{$encoded}";
    }

    private function itemContentPath(string $path): string
    {
        return $this->itemPath($path).':/content';
    }

    private function childrenPath(string $directory): string
    {
        $driveId = $this->resolveDriveId();
        $directory = trim($directory, '/');
        if ($directory === '') {
            return "drives/{$driveId}/root/children";
        }

        return "drives/{$driveId}/root:/".$this->encodePath($directory).':/children';
    }

    private function parentReferencePath(string $directory): string
    {
        $driveId = $this->resolveDriveId();
        $directory = trim($directory, '/');

        return $directory === ''
            ? "/drives/{$driveId}/root"
            : "/drives/{$driveId}/root:/".$this->encodePath($directory);
    }

    private function encodePath(string $path): string
    {
        return str_replace('%2F', '/', rawurlencode($path));
    }

    private function graphRequest(string $method, string $uri, array $options = [])
    {
        $options['headers'] = array_merge([
            'Authorization' => 'Bearer '.$this->accessToken(),
            'Accept' => 'application/json',
        ], $options['headers'] ?? []);

        return $this->http->request($method, ltrim($uri, '/'), $options);
    }

    private function accessToken(): string
    {
        if ($this->accessToken && $this->tokenExpiresAt && $this->tokenExpiresAt > time() + 60) {
            return $this->accessToken;
        }

        $tokenClient = new Client(['timeout' => 30]);
        $response = $tokenClient->post(
            'https://login.microsoftonline.com/'.$this->tenantId.'/oauth2/v2.0/token',
            [
                'form_params' => [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'scope' => 'https://graph.microsoft.com/.default',
                    'grant_type' => 'client_credentials',
                ],
            ]
        );

        $payload = json_decode((string) $response->getBody(), true);
        $this->accessToken = (string) ($payload['access_token'] ?? '');
        $this->tokenExpiresAt = time() + (int) ($payload['expires_in'] ?? 3600);

        if ($this->accessToken === '') {
            throw new \RuntimeException('Unable to obtain Microsoft Graph access token.');
        }

        return $this->accessToken;
    }
}
