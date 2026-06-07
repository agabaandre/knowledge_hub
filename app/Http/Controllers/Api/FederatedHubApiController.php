<?php

namespace App\Http\Controllers\Api;

use App\Services\FederatedHubLookupService;
use App\Services\FederatedHubService;
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

    protected function authorizeFederationRequest(Request $request): void
    {
        $token = $this->federation->federationToken();
        if ($token === null || $token === '') {
            return;
        }

        $provided = $request->bearerToken() ?: $request->query('token');
        if ($provided !== $token) {
            abort(401, 'Invalid federation API token.');
        }
    }
}
