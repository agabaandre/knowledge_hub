<?php

namespace App\Filesystem;

use GuzzleHttp\Client;
use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Adapter\Polyfill\NotSupportingVisibilityTrait;
use League\Flysystem\Config;

/**
 * Flysystem v1 adapter for Azure Blob Storage via the REST API (Guzzle only).
 *
 * Avoids microsoft/azure-storage-blob and league/flysystem-azure-blob-storage,
 * which conflict with guzzlehttp/psr7 ^2 on Laravel 8 / Guzzle 7.
 *
 * @see https://learn.microsoft.com/en-us/rest/api/storageservices/authorize-with-shared-key
 */
class AzureBlobRestAdapter extends AbstractAdapter
{
    use NotSupportingVisibilityTrait;

    private Client $http;

    private string $accountName;

    private string $accountKey;

    private string $container;

    private string $endpoint;

    public function __construct(array $config)
    {
        if (! empty($config['connection_string'])) {
            $parsed = $this->parseConnectionString((string) $config['connection_string']);
            $this->accountName = $parsed['account_name'];
            $this->accountKey = $parsed['account_key'];
            $this->endpoint = $parsed['endpoint'];
        } else {
            $this->accountName = (string) ($config['name'] ?? '');
            $this->accountKey = $this->decodeAccountKey((string) ($config['key'] ?? ''));
            $suffix = (string) ($config['endpoint_suffix'] ?? 'core.windows.net');
            $this->endpoint = 'https://'.$this->accountName.'.blob.'.$suffix;
        }

        $this->container = (string) ($config['container'] ?? '');
        $prefix = trim((string) ($config['prefix'] ?? ''), '/');
        $this->setPathPrefix($prefix);

        $this->http = new Client([
            'timeout' => 120,
            'http_errors' => false,
        ]);
    }

    public function write($path, $contents, Config $config)
    {
        $path = $this->applyPathPrefix($path);
        $response = $this->request('PUT', $path, [
            'headers' => [
                'Content-Type' => 'application/octet-stream',
                'x-ms-blob-type' => 'BlockBlob',
            ],
            'body' => $contents,
        ]);

        if ($response->getStatusCode() >= 300) {
            throw new \RuntimeException('Azure Blob write failed: HTTP '.$response->getStatusCode());
        }

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
        $this->copy($path, $newpath);
        $this->delete($path);

        return true;
    }

    public function copy($path, $newpath)
    {
        $contents = $this->read($path);

        return $this->write($newpath, $contents['contents'], new Config());
    }

    public function delete($path)
    {
        $path = $this->applyPathPrefix($path);
        $response = $this->request('DELETE', $path);
        if (! in_array($response->getStatusCode(), [202, 204, 404], true)) {
            throw new \RuntimeException('Azure Blob delete failed: HTTP '.$response->getStatusCode());
        }

        return true;
    }

    public function deleteDir($dirname)
    {
        $dirname = trim($this->applyPathPrefix($dirname), '/');
        foreach ($this->listAllBlobs($dirname) as $blob) {
            $this->request('DELETE', $blob);
        }

        return true;
    }

    public function createDir($dirname, Config $config)
    {
        return ['type' => 'dir', 'path' => trim($this->applyPathPrefix($dirname), '/')];
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
        $response = $this->request('GET', $path);
        if ($response->getStatusCode() >= 300) {
            throw new \RuntimeException('Azure Blob read failed: HTTP '.$response->getStatusCode());
        }

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
        $prefix = $directory === '' ? '' : $directory.'/';
        $response = $this->request('GET', '', [
            'query' => [
                'restype' => 'container',
                'comp' => 'list',
                'prefix' => $prefix,
                'delimiter' => $recursive ? '' : '/',
            ],
        ]);

        if ($response->getStatusCode() >= 300) {
            throw new \RuntimeException('Azure Blob list failed: HTTP '.$response->getStatusCode());
        }

        $xml = simplexml_load_string((string) $response->getBody());
        if ($xml === false) {
            return [];
        }

        $items = [];
        foreach ($xml->Blobs->Blob ?? [] as $blob) {
            $name = (string) ($blob->Name ?? '');
            if ($name === '' || ! str_starts_with($name, $prefix)) {
                continue;
            }
            $relative = $prefix === '' ? $name : substr($name, strlen($prefix));
            if (str_contains($relative, '/')) {
                continue;
            }
            $items[] = [
                'type' => 'file',
                'path' => $this->removePathPrefix($name),
                'size' => (int) ($blob->Properties->Content-Length ?? 0),
                'timestamp' => isset($blob->Properties->{'Last-Modified'})
                    ? strtotime((string) $blob->Properties->{'Last-Modified'})
                    : time(),
            ];
        }

        if (! $recursive) {
            foreach ($xml->Blobs->BlobPrefix ?? [] as $prefixNode) {
                $name = rtrim((string) ($prefixNode->Name ?? ''), '/');
                $items[] = [
                    'type' => 'dir',
                    'path' => $this->removePathPrefix($name),
                ];
            }
        }

        return $items;
    }

    public function getMetadata($path)
    {
        $path = $this->applyPathPrefix($path);
        $response = $this->request('HEAD', $path);
        if ($response->getStatusCode() >= 300) {
            throw new \RuntimeException('Azure Blob metadata failed: HTTP '.$response->getStatusCode());
        }

        return [
            'type' => 'file',
            'path' => $path,
            'size' => (int) ($response->getHeaderLine('Content-Length') ?: 0),
            'timestamp' => strtotime($response->getHeaderLine('Last-Modified') ?: 'now'),
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
        $response = $this->request('GET', '', [
            'query' => [
                'restype' => 'container',
                'comp' => 'list',
                'maxresults' => 1,
            ],
        ]);

        if ($response->getStatusCode() >= 300) {
            throw new \RuntimeException('Azure Blob connection failed: HTTP '.$response->getStatusCode());
        }
    }

    /**
     * @return array{account_name: string, account_key: string, endpoint: string}
     */
    private function parseConnectionString(string $connectionString): array
    {
        $parts = [];
        foreach (explode(';', $connectionString) as $segment) {
            if (! str_contains($segment, '=')) {
                continue;
            }
            [$key, $value] = explode('=', $segment, 2);
            $parts[strtolower(trim($key))] = trim($value);
        }

        $accountName = $parts['accountname'] ?? '';
        $accountKey = $parts['accountkey'] ?? '';
        $suffix = $parts['endpointsuffix'] ?? 'core.windows.net';
        $protocol = $parts['defaultendpointsprotocol'] ?? 'https';
        $endpoint = $parts['blobendpoint']
            ?? $protocol.'://'.$accountName.'.blob.'.$suffix;

        if ($accountName === '' || $accountKey === '') {
            throw new \RuntimeException('Invalid Azure connection string: AccountName and AccountKey are required.');
        }

        return [
            'account_name' => $accountName,
            'account_key' => $this->decodeAccountKey($accountKey),
            'endpoint' => rtrim($endpoint, '/'),
        ];
    }

    private function decodeAccountKey(string $key): string
    {
        $decoded = base64_decode($key, true);

        return $decoded !== false && $decoded !== '' ? $decoded : $key;
    }

    /**
     * @param  array<string, mixed>  $options
     */
    private function request(string $method, string $blobPath, array $options = [])
    {
        $blobPath = ltrim($blobPath, '/');
        $url = $this->endpoint.'/'.$this->container;
        if ($blobPath !== '') {
            $url .= '/'.$this->encodeBlobPath($blobPath);
        }

        $query = $options['query'] ?? [];
        if ($query) {
            $url .= '?'.http_build_query($query);
        }

        $headers = array_merge([
            'x-ms-date' => gmdate('D, d M Y H:i:s').' GMT',
            'x-ms-version' => '2020-10-02',
        ], $options['headers'] ?? []);

        $headers['Authorization'] = $this->authorizationHeader(
            $method,
            $blobPath,
            $query,
            $headers,
            isset($options['body']) ? (string) $options['body'] : ''
        );

        return $this->http->request($method, $url, [
            'headers' => $headers,
            'body' => $options['body'] ?? null,
        ]);
    }

    /**
     * @param  array<string, string>  $query
     * @param  array<string, string>  $headers
     */
    private function authorizationHeader(
        string $method,
        string $blobPath,
        array $query,
        array $headers,
        string $body
    ): string {
        $contentLength = $body === '' ? '' : (string) strlen($body);
        $contentType = $headers['Content-Type'] ?? '';
        $date = $headers['x-ms-date'] ?? '';

        $canonicalizedHeaders = $this->canonicalizedHeaders($headers);
        $canonicalizedResource = $this->canonicalizedResource($blobPath, $query);

        $stringToSign = implode("\n", [
            strtoupper($method),
            '', // Content-Encoding
            '', // Content-Language
            $contentLength,
            '', // Content-MD5
            $contentType,
            '', // Date (use x-ms-date instead)
            '', // If-Modified-Since
            '', // If-Match
            '', // If-None-Match
            '', // If-Unmodified-Since
            '', // Range
            $canonicalizedHeaders.$canonicalizedResource,
        ]);

        $signature = base64_encode(hash_hmac('sha256', $stringToSign, $this->accountKey, true));

        return 'SharedKey '.$this->accountName.':'.$signature;
    }

    /**
     * @param  array<string, string>  $headers
     */
    private function canonicalizedHeaders(array $headers): string
    {
        $msHeaders = [];
        foreach ($headers as $name => $value) {
            $lower = strtolower($name);
            if (str_starts_with($lower, 'x-ms-')) {
                $msHeaders[$lower] = trim(preg_replace('/\s+/', ' ', (string) $value));
            }
        }
        ksort($msHeaders);
        $lines = '';
        foreach ($msHeaders as $name => $value) {
            $lines .= $name.':'.$value."\n";
        }

        return $lines;
    }

    /**
     * @param  array<string, string>  $query
     */
    private function canonicalizedResource(string $blobPath, array $query): string
    {
        $resource = '/'.$this->accountName.'/'.$this->container;
        if ($blobPath !== '') {
            $resource .= '/'.$blobPath;
        }

        ksort($query);
        foreach ($query as $key => $value) {
            $resource .= "\n".strtolower($key).':'.$value;
        }

        return $resource;
    }

    private function encodeBlobPath(string $path): string
    {
        return implode('/', array_map('rawurlencode', explode('/', $path)));
    }

    /**
     * @return array<int, string>
     */
    private function listAllBlobs(string $prefix): array
    {
        $response = $this->request('GET', '', [
            'query' => [
                'restype' => 'container',
                'comp' => 'list',
                'prefix' => $prefix === '' ? '' : $prefix.'/',
            ],
        ]);

        $xml = simplexml_load_string((string) $response->getBody());
        $names = [];
        foreach ($xml->Blobs->Blob ?? [] as $blob) {
            $names[] = (string) ($blob->Name ?? '');
        }

        return array_values(array_filter($names));
    }
}
