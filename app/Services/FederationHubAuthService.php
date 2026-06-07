<?php

namespace App\Services;

use App\Models\FederatedKnowledgeHub;
use App\Models\FederationHubCredential;
use App\Support\HubSiteIdentifier;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class FederationHubAuthService
{
    /**
     * @return array{access_token: string, refresh_token: string, expires_in: int, token_type: string}
     */
    public function issueTokenPair(string $childSiteId, ?string $childName = null): array
    {
        $this->ensureCredentialsTable();

        $accessToken = $this->generateToken();
        $refreshToken = $this->generateToken();
        $accessTtl = (int) config('federation.access_token_ttl_seconds', 86400);
        $refreshTtl = (int) config('federation.refresh_token_ttl_seconds', 7776000);

        FederationHubCredential::query()->updateOrCreate(
            ['child_site_id' => $childSiteId],
            [
                'child_name' => $childName,
                'access_token_hash' => $this->hashToken($accessToken),
                'refresh_token_hash' => $this->hashToken($refreshToken),
                'access_token_expires_at' => now()->addSeconds($accessTtl),
                'refresh_token_expires_at' => now()->addSeconds($refreshTtl),
                'is_active' => true,
                'last_used_at' => now(),
            ]
        );

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_in' => $accessTtl,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * @return array{access_token: string, refresh_token: string, expires_in: int, token_type: string}
     */
    public function refreshTokenPair(string $refreshToken): array
    {
        $this->ensureCredentialsTable();

        $credential = FederationHubCredential::query()
            ->where('refresh_token_hash', $this->hashToken($refreshToken))
            ->where('is_active', true)
            ->first();

        if (! $credential) {
            throw new \RuntimeException('Invalid or expired refresh token.');
        }

        if ($credential->refresh_token_expires_at && $credential->refresh_token_expires_at->isPast()) {
            throw new \RuntimeException('Refresh token has expired. Re-register with the central hub registration token.');
        }

        return $this->issueTokenPair($credential->child_site_id, $credential->child_name);
    }

    public function validateAccessToken(?string $accessToken): bool
    {
        if ($accessToken === null || $accessToken === '') {
            return false;
        }

        if (! Schema::hasTable('federation_hub_credentials')) {
            return false;
        }

        $credential = FederationHubCredential::query()
            ->where('access_token_hash', $this->hashToken($accessToken))
            ->where('is_active', true)
            ->first();

        if (! $credential || $credential->access_token_expires_at->isPast()) {
            return false;
        }

        $credential->forceFill(['last_used_at' => now()])->save();

        return true;
    }

    /**
     * @return array{access_token: string, refresh_token: string, expires_in: int, token_type: string}
     */
    public function exchangeRegistrationToken(string $registrationToken, string $childSiteId, ?string $childName = null): array
    {
        $master = app(FederatedHubService::class)->federationToken();
        if ($master === null || $master === '' || ! hash_equals($master, $registrationToken)) {
            throw new \RuntimeException('Invalid federation registration token.');
        }

        return $this->issueTokenPair($childSiteId, $childName);
    }

    /**
     * Bootstrap or refresh tokens for this hub's connection to the central parent.
     *
     * @return array{access_token: string, refresh_token: string, expires_in: int}
     */
    public function ensureCentralAccessToken(?string $registrationToken = null): array
    {
        $state = $this->centralConnectionState();

        if ($registrationToken && ! $state['refresh_token'] && $state['base_url'] !== '') {
            return $this->bootstrapCentralTokens($state['base_url'], $registrationToken);
        }

        if ($state['refresh_token'] && $this->centralTokenNeedsRefresh($state)) {
            try {
                return $this->refreshCentralTokens($state['base_url'], $state['refresh_token']);
            } catch (\Throwable $e) {
                if ($registrationToken === null && $state['registration_token']) {
                    $registrationToken = $state['registration_token'];
                } else {
                    throw $e;
                }
            }
        }

        if ($state['access_token'] && ! $this->centralTokenNeedsRefresh($state)) {
            return [
                'access_token' => $state['access_token'],
                'refresh_token' => $state['refresh_token'],
                'expires_in' => $state['expires_at']
                    ? max(0, $state['expires_at']->getTimestamp() - now()->getTimestamp())
                    : 0,
            ];
        }

        if ($registrationToken) {
            return $this->bootstrapCentralTokens($state['base_url'], $registrationToken);
        }

        if ($state['access_token']) {
            return [
                'access_token' => $state['access_token'],
                'refresh_token' => $state['refresh_token'],
                'expires_in' => 0,
            ];
        }

        throw new \RuntimeException('Central hub is not authenticated. Provide a registration token to connect.');
    }

    /**
     * @return array{access_token: string, refresh_token: string, expires_in: int}
     */
    public function bootstrapCentralTokens(string $baseUrl, string $registrationToken): array
    {
        $payload = $this->requestCentralToken($baseUrl, [
            'grant_type' => 'registration',
            'site_id' => $this->localSiteId(),
            'site_name' => config('app.name'),
            'registration_token' => $registrationToken,
        ]);

        $this->persistCentralTokens($baseUrl, $payload, $registrationToken);

        return $payload;
    }

    /**
     * Refresh the stored central hub token if a refresh token exists.
     *
     * @return array{access_token: string, refresh_token: string, expires_in: int}|null
     */
    public function refreshStoredCentralToken(): ?array
    {
        $state = $this->centralConnectionState();
        if ($state['base_url'] === '' || empty($state['refresh_token'])) {
            return null;
        }

        return $this->refreshCentralTokens($state['base_url'], $state['refresh_token']);
    }

    /**
     * @return array{
     *     base_url: string,
     *     access_token: ?string,
     *     refresh_token: ?string,
     *     expires_at: ?\Illuminate\Support\Carbon,
     *     registration_token: ?string
     * }
     */
    public function centralConnectionState(): array
    {
        $settings = function_exists('settings') ? settings() : null;

        return [
            'base_url' => (string) ($settings->central_hub_url ?? env('CENTRAL_HUB_URL', '')),
            'access_token' => $settings->central_hub_api_token ?? env('CENTRAL_HUB_API_TOKEN') ?: null,
            'refresh_token' => Schema::hasColumn('setting', 'central_hub_refresh_token')
                ? ($settings->central_hub_refresh_token ?? null)
                : null,
            'expires_at' => Schema::hasColumn('setting', 'central_hub_token_expires_at')
                ? ($settings->central_hub_token_expires_at ?? null)
                : null,
            'registration_token' => env('CENTRAL_HUB_API_TOKEN') ?: null,
        ];
    }

    /**
     * @return array{access_token: string, refresh_token: string, expires_in: int}
     */
    public function refreshCentralTokens(string $baseUrl, string $refreshToken): array
    {
        $payload = $this->requestCentralToken($baseUrl, [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
        ]);

        $this->persistCentralTokens($baseUrl, $payload);

        return $payload;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function persistCentralTokens(string $baseUrl, array $payload, ?string $registrationToken = null): void
    {
        if (! Schema::hasTable('setting')) {
            return;
        }

        $expiresAt = now()->addSeconds((int) ($payload['expires_in'] ?? 3600));
        $updates = [
            'central_hub_url' => rtrim($baseUrl, '/'),
            'central_hub_api_token' => (string) ($payload['access_token'] ?? ''),
            'central_hub_token_expires_at' => $expiresAt,
            'central_hub_connected_at' => now(),
        ];

        if (Schema::hasColumn('setting', 'central_hub_refresh_token')) {
            $updates['central_hub_refresh_token'] = (string) ($payload['refresh_token'] ?? '');
        }

        DB::table('setting')->where('status', 'active')->update($updates);
        Cache::forget('settings');
    }

    /**
     * @param  callable(\Illuminate\Http\Client\PendingRequest): \Illuminate\Http\Client\Response  $callback
     * @return array<string, mixed>
     */
    public function centralAuthorizedRequest(string $baseUrl, callable $callback, ?string $registrationToken = null): array
    {
        $tokens = $this->ensureCentralAccessToken($registrationToken);
        $request = Http::timeout(25)->acceptJson()->withToken($tokens['access_token']);
        $response = $callback($request);

        if ($response->status() !== 401) {
            return $this->decodeJsonResponse($response);
        }

        if (empty($tokens['refresh_token'])) {
            throw new \RuntimeException('Central hub rejected the access token and no refresh token is available.');
        }

        $refreshed = $this->refreshCentralTokens($baseUrl, $tokens['refresh_token']);
        $retry = $callback(Http::timeout(25)->acceptJson()->withToken($refreshed['access_token']));

        if (! $retry->successful()) {
            throw new \RuntimeException('Central hub request failed after token refresh ('.$retry->status().'): '.$retry->body());
        }

        return $this->decodeJsonResponse($retry);
    }

    /**
     * @return array{access_token: string, refresh_token: string, expires_in: int}
     */
    public function ensureRemoteHubAccessToken(FederatedKnowledgeHub $hub, ?string $registrationToken = null): array
    {
        if ($hub->api_refresh_token && $this->remoteTokenNeedsRefresh($hub)) {
            try {
                return $this->refreshRemoteHubTokens($hub, $hub->api_refresh_token);
            } catch (\Throwable $e) {
                if (! $registrationToken && $hub->api_token) {
                    $registrationToken = $hub->api_token;
                } else {
                    throw $e;
                }
            }
        }

        if ($hub->api_token && ! $this->remoteTokenNeedsRefresh($hub)) {
            return [
                'access_token' => $hub->api_token,
                'refresh_token' => $hub->api_refresh_token,
                'expires_in' => $hub->api_token_expires_at
                    ? max(0, now()->diffInSeconds($hub->api_token_expires_at, false))
                    : 0,
            ];
        }

        if ($registrationToken) {
            return $this->bootstrapRemoteHubTokens($hub, $registrationToken);
        }

        if ($hub->api_token) {
            return [
                'access_token' => $hub->api_token,
                'refresh_token' => $hub->api_refresh_token,
                'expires_in' => 0,
            ];
        }

        return [
            'access_token' => '',
            'refresh_token' => '',
            'expires_in' => 0,
        ];
    }

    /**
     * @return array{access_token: string, refresh_token: string, expires_in: int}
     */
    public function bootstrapRemoteHubTokens(FederatedKnowledgeHub $hub, string $registrationToken): array
    {
        $payload = $this->requestCentralToken($hub->normalizedBaseUrl(), [
            'grant_type' => 'registration',
            'site_id' => $this->localSiteId(),
            'site_name' => config('app.name'),
            'registration_token' => $registrationToken,
        ]);

        $this->persistRemoteHubTokens($hub, $payload);

        return $payload;
    }

    /**
     * @return array{access_token: string, refresh_token: string, expires_in: int}
     */
    public function refreshRemoteHubTokens(FederatedKnowledgeHub $hub, string $refreshToken): array
    {
        $payload = $this->requestCentralToken($hub->normalizedBaseUrl(), [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
        ]);

        $this->persistRemoteHubTokens($hub, $payload);

        return $payload;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function persistRemoteHubTokens(FederatedKnowledgeHub $hub, array $payload): void
    {
        $hub->forceFill([
            'api_token' => (string) ($payload['access_token'] ?? ''),
            'api_refresh_token' => (string) ($payload['refresh_token'] ?? ''),
            'api_token_expires_at' => now()->addSeconds((int) ($payload['expires_in'] ?? 3600)),
        ])->save();
    }

    /**
     * @param  callable(\Illuminate\Http\Client\PendingRequest): \Illuminate\Http\Client\Response  $callback
     * @return array<string, mixed>
     */
    public function remoteHubAuthorizedRequest(FederatedKnowledgeHub $hub, callable $callback, ?string $registrationToken = null): array
    {
        $tokens = $this->ensureRemoteHubAccessToken($hub, $registrationToken);
        $request = Http::timeout(20)->acceptJson();
        if ($tokens['access_token'] !== '') {
            $request = $request->withToken($tokens['access_token']);
        }

        $response = $callback($request);
        if ($response->status() !== 401 || empty($tokens['refresh_token'])) {
            if (! $response->successful()) {
                throw new \RuntimeException('Remote hub request failed ('.$response->status().'): '.$response->body());
            }

            return $this->decodeJsonResponse($response);
        }

        $refreshed = $this->refreshRemoteHubTokens($hub, $tokens['refresh_token']);
        $retry = $callback(Http::timeout(20)->acceptJson()->withToken($refreshed['access_token']));
        if (! $retry->successful()) {
            throw new \RuntimeException('Remote hub request failed after token refresh ('.$retry->status().'): '.$retry->body());
        }

        return $this->decodeJsonResponse($retry);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{access_token: string, refresh_token: string, expires_in: int}
     */
    protected function requestCentralToken(string $baseUrl, array $data): array
    {
        $response = Http::timeout(25)
            ->acceptJson()
            ->asJson()
            ->post(rtrim($baseUrl, '/').'/api/federation/auth/token', $data);

        if (! $response->successful()) {
            throw new \RuntimeException('Token request failed ('.$response->status().'): '.$response->body());
        }

        $json = $response->json();
        if (! is_array($json) || empty($json['access_token']) || empty($json['refresh_token'])) {
            throw new \RuntimeException('Central hub returned an invalid token response.');
        }

        return [
            'access_token' => (string) $json['access_token'],
            'refresh_token' => (string) $json['refresh_token'],
            'expires_in' => (int) ($json['expires_in'] ?? 3600),
        ];
    }

    /**
     * @param  array<string, mixed>  $state
     */
    protected function centralTokenNeedsRefresh(array $state): bool
    {
        if (empty($state['access_token'])) {
            return true;
        }

        if (empty($state['expires_at'])) {
            return false;
        }

        $buffer = (int) config('federation.refresh_buffer_seconds', 300);

        return $state['expires_at']->lte(now()->addSeconds($buffer));
    }

    protected function remoteTokenNeedsRefresh(FederatedKnowledgeHub $hub): bool
    {
        if (! $hub->api_token) {
            return true;
        }

        if (! $hub->api_token_expires_at) {
            return false;
        }

        $buffer = (int) config('federation.refresh_buffer_seconds', 300);

        return $hub->api_token_expires_at->lte(now()->addSeconds($buffer));
    }

    /**
     * @param  mixed  $response
     * @return array<string, mixed>
     */
    protected function decodeJsonResponse($response): array
    {
        if (! $response->successful()) {
            throw new \RuntimeException('Request failed ('.$response->status().'): '.$response->body());
        }

        $json = $response->json();

        return is_array($json) ? $json : [];
    }

    protected function localSiteId(): string
    {
        return env('HUB_SITE_ID') ?: HubSiteIdentifier::fromAppUrl();
    }

    protected function generateToken(): string
    {
        return Str::random(80);
    }

    protected function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }

    protected function ensureCredentialsTable(): void
    {
        if (! Schema::hasTable('federation_hub_credentials')) {
            throw new \RuntimeException('Federation credentials table is missing. Run database migrations.');
        }
    }
}
