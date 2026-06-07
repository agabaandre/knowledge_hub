<?php

namespace App\Http\Controllers\Api;

use App\Services\FederatedHubLookupService;
use App\Services\FederatedHubService;
use App\Services\FederationHubAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FederatedHubApiController extends ApiController
{
    public function __construct(private FederatedHubService $federation)
    {
    }

    public function manifest(Request $request): JsonResponse
    {
        $this->authorizeFederationRequest($request);

        return response()->json([
            'status' => 'success',
            'manifest' => $this->federation->localManifest(),
        ]);
    }

    public function publicPublications(Request $request): JsonResponse
    {
        $this->authorizeFederationRequest($request);

        $perPage = min(100, max(1, (int) $request->input('per_page', $request->input('page_size', 20))));
        $page = max(1, (int) $request->input('page', 1));

        return response()->json([
            'status' => 'success',
            'hub' => $this->federation->localManifest(),
            ...$this->federation->publicPublicationsPayload($perPage, $page),
        ]);
    }

    public function publicForums(Request $request): JsonResponse
    {
        $this->authorizeFederationRequest($request);

        $perPage = min(100, max(1, (int) $request->input('per_page', $request->input('page_size', 20))));
        $page = max(1, (int) $request->input('page', 1));

        return response()->json([
            'status' => 'success',
            'hub' => $this->federation->localManifest(),
            ...$this->federation->publicForumsPayload($perPage, $page),
        ]);
    }

    public function lookupIndex(FederatedHubLookupService $lookup): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $lookup->lookupIndex(),
        ]);
    }

    public function lookupSettings(Request $request, FederatedHubLookupService $lookup): JsonResponse
    {
        $this->authorizeFederationRequest($request);

        return response()->json([
            'status' => 'success',
            'data' => $lookup->exportBrandingPayload(),
        ]);
    }

    public function lookupMetadata(Request $request, FederatedHubLookupService $lookup): JsonResponse
    {
        $this->authorizeFederationRequest($request);

        return response()->json([
            'status' => 'success',
            'data' => $lookup->exportMetadataPayload(),
        ]);
    }

    public function issueToken(Request $request, FederationHubAuthService $auth): JsonResponse
    {
        $data = $request->validate([
            'grant_type' => 'required|in:registration,refresh_token',
            'site_id' => 'required_if:grant_type,registration|string|max:128',
            'site_name' => 'nullable|string|max:255',
            'registration_token' => 'required_if:grant_type,registration|string|max:255',
            'refresh_token' => 'required_if:grant_type,refresh_token|string|max:255',
        ]);

        try {
            if ($data['grant_type'] === 'registration') {
                $tokens = $auth->exchangeRegistrationToken(
                    $data['registration_token'],
                    $data['site_id'],
                    $data['site_name'] ?? null
                );
            } else {
                $tokens = $auth->refreshTokenPair($data['refresh_token']);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            ...$tokens,
        ]);
    }

    protected function authorizeFederationRequest(Request $request): void
    {
        $token = $this->federation->federationToken();
        if ($token === null || $token === '') {
            return;
        }

        $provided = $request->bearerToken() ?: $request->query('token');
        if ($provided === null || $provided === '') {
            abort(401, 'Federation API token required.');
        }

        if (hash_equals($token, $provided)) {
            return;
        }

        if (app(FederationHubAuthService::class)->validateAccessToken($provided)) {
            return;
        }

        abort(401, 'Invalid federation API token.');
    }
}
