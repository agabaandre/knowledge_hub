<?php

namespace App\Jobs;

use App\Models\FederatedHubProvision;
use App\Services\FederationProvision\FederatedHubProvisionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;

class ProvisionFederatedHubJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 3600;

    public int $tries = 1;

    public function __construct(
        public int $provisionId,
        public string $encryptedAdminPassword
    ) {
    }

    public static function dispatchFor(FederatedHubProvision $provision, string $adminPassword): void
    {
        static::dispatch($provision->id, Crypt::encryptString($adminPassword));
    }

    public function handle(FederatedHubProvisionService $service): void
    {
        $provision = FederatedHubProvision::query()->find($this->provisionId);
        if (! $provision) {
            return;
        }

        if ($provision->status === FederatedHubProvision::STATUS_COMPLETED) {
            return;
        }

        $password = Crypt::decryptString($this->encryptedAdminPassword);

        $service->run($provision, [
            'email' => $provision->admin_email,
            'password' => $password,
            'first_name' => $provision->admin_first_name ?: 'Admin',
            'last_name' => $provision->admin_last_name ?: 'User',
        ]);
    }

    public function failed(?\Throwable $e): void
    {
        $provision = FederatedHubProvision::query()->find($this->provisionId);
        if ($provision && ! $provision->isTerminal()) {
            $provision->markFailed($e ? $e->getMessage() : 'Provisioning job failed.');
        }
    }
}
