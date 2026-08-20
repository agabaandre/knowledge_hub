<?php

namespace App\Services\FederationProvision;

use App\Services\FederatedHubLookupService;
use RuntimeException;
use Symfony\Component\Process\Process;

class ProvisionMetadataCopy
{
    public function __construct(
        private readonly FederatedHubLookupService $lookup,
        private readonly ProvisionFilesystem $filesystem
    ) {
    }

    /**
     * @param  array{database: string, username: string, password: string, host: string, port: int}  $db
     * @return array{branding: array<string, int>, metadata: array<string, int>}
     */
    public function copyToTargetInstance(string $targetPath, array $db): array
    {
        $payload = [
            'branding' => $this->lookup->exportBrandingPayload(),
            'metadata' => $this->lookup->exportMetadataPayload(),
        ];

        $jsonPath = rtrim($targetPath, '/').'/storage/app/federation_bootstrap.json';
        $json = json_encode($payload, JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            throw new RuntimeException('Could not encode metadata payload.');
        }

        $this->filesystem->writeFileAsRoot($jsonPath, $json);

        $php = (string) config('federation_provision.php_binary', PHP_BINARY);
        $code = <<<'PHP'
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$path = storage_path('app/federation_bootstrap.json');
if (!is_file($path)) { fwrite(STDERR, "missing bootstrap json\n"); exit(1); }
$data = json_decode(file_get_contents($path), true);
if (!is_array($data)) { fwrite(STDERR, "invalid bootstrap json\n"); exit(1); }
$lookup = app(App\Services\FederatedHubLookupService::class);
$branding = $lookup->importBrandingPayload($data['branding'] ?? []);
$metadata = $lookup->importMetadataPayload($data['metadata'] ?? []);
@unlink($path);
echo json_encode(['branding' => $branding, 'metadata' => $metadata]);
PHP;

        $process = new Process([$php, '-r', $code], $targetPath, null, null, 600);
        $process->run();
        if (! $process->isSuccessful()) {
            throw new RuntimeException('Metadata copy failed: '.trim($process->getErrorOutput() ?: $process->getOutput()));
        }

        $decoded = json_decode(trim($process->getOutput()), true);
        if (! is_array($decoded)) {
            return ['branding' => [], 'metadata' => []];
        }

        return [
            'branding' => $decoded['branding'] ?? [],
            'metadata' => $decoded['metadata'] ?? [],
        ];
    }
}
