<?php

namespace App\Providers;

use App\Filesystem\AzureBlobRestAdapter;
use App\Filesystem\FtpAdapter;
use App\Filesystem\SharePointGraphAdapter;
use App\Filesystem\SftpAdapter;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;

class HubStorageServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Storage::extend('sharepoint-graph', function ($app, array $config) {
            $adapter = new SharePointGraphAdapter($config);

            return new FilesystemAdapter(new Filesystem($adapter), $adapter, $config);
        });

        Storage::extend('gcs', function ($app, array $config) {
            if (! class_exists(\Google\Cloud\Storage\StorageClient::class)) {
                throw new \RuntimeException(
                    'Google Cloud Storage requires: composer require google/cloud-storage superbalist/flysystem-google-storage'
                );
            }

            $clientConfig = ['projectId' => $config['project_id'] ?? null];
            if (! empty($config['key_file_path']) && is_readable($config['key_file_path'])) {
                $clientConfig['keyFilePath'] = $config['key_file_path'];
            } elseif (! empty($config['key_file'])) {
                $clientConfig['keyFile'] = $config['key_file'];
            }

            $client = new \Google\Cloud\Storage\StorageClient(array_filter($clientConfig));
            $bucket = $client->bucket($config['bucket'] ?? '');
            $adapter = new \Superbalist\Flysystem\GoogleStorage\GoogleStorageAdapter(
                $client,
                $bucket,
                $config['path_prefix'] ?? ''
            );

            if (! empty($config['storage_api_uri'])) {
                $adapter->setStorageApiUri($config['storage_api_uri']);
            }

            return new FilesystemAdapter(new Filesystem($adapter), $adapter, $config);
        });

        Storage::extend('azure-blob', function ($app, array $config) {
            $adapter = new AzureBlobRestAdapter($config);

            return new FilesystemAdapter(new Filesystem($adapter), $adapter, $config);
        });

        Storage::extend('sftp-phpseclib', function ($app, array $config) {
            $adapter = new SftpAdapter($config);

            return new FilesystemAdapter(new Filesystem($adapter), $adapter, $config);
        });

        Storage::extend('ftp-php', function ($app, array $config) {
            $adapter = new FtpAdapter($config);

            return new FilesystemAdapter(new Filesystem($adapter), $adapter, $config);
        });
    }
}
