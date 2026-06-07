<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\FederatedKnowledgeHub;
use App\Services\FederatedContentStagingService;
use App\Services\FederatedHubLookupService;
use App\Services\FederatedHubService;
use App\Services\FederationHubAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class FederatedHubsController extends Controller
{
    public function index(FederatedHubService $federation)
    {
        $hubs = Schema::hasTable('federated_knowledge_hubs')
            ? FederatedKnowledgeHub::with('mappedCountry')->orderBy('name')->get()
            : collect();

        $settings = function_exists('settings') ? settings() : null;

        return view('admin.federation.index', [
            'hubs' => $hubs,
            'localManifest' => $federation->localManifest(),
            'lookupIndex' => app(FederatedHubLookupService::class)->lookupIndex(),
            'countries' => Country::orderBy('name')->get(),
            'federationToken' => $federation->federationToken(),
            'federationApiBase' => url('/api/federation'),
            'centralHubUrl' => $settings->central_hub_url ?? env('CENTRAL_HUB_URL'),
            'centralHubSiteId' => $settings->central_hub_site_id ?? null,
            'centralHubConnectedAt' => $settings->central_hub_connected_at ?? null,
            'centralMetadataSyncedAt' => $settings->central_metadata_synced_at ?? null,
            'centralHubTokenExpiresAt' => $settings->central_hub_token_expires_at ?? null,
            'centralHubHasRefreshToken' => ! empty($settings->central_hub_refresh_token ?? null),
            'pendingFederatedContentCount' => app(FederatedContentStagingService::class)->pendingCount(),
            'isCountryHub' => hub_admin_units_enabled(),
        ]);
    }

    public function refreshCentralToken(FederationHubAuthService $auth)
    {
        try {
            $tokens = $auth->refreshStoredCentralToken();
            if ($tokens === null) {
                return redirect()->route('admin.federation.index')
                    ->with('alert-danger', 'No refresh token stored. Sync from central using the parent hub registration token first.');
            }

            return redirect()->route('admin.federation.index')
                ->with('alert-success', 'Central hub access token refreshed successfully.');
        } catch (\Throwable $e) {
            return redirect()->route('admin.federation.index')
                ->with('alert-danger', 'Token refresh failed: '.$e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'base_url' => 'required|url|max:500',
            'api_token' => 'nullable|string|max:255',
            'mapped_country_id' => 'nullable|integer|exists:country,id',
            'is_active' => 'nullable|boolean',
            'auto_sync' => 'nullable|boolean',
        ]);

        $hub = FederatedKnowledgeHub::create([
            'name' => $data['name'],
            'base_url' => rtrim($data['base_url'], '/'),
            'api_token' => $data['api_token'] ?? null,
            'mapped_country_id' => $data['mapped_country_id'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'auto_sync' => $request->boolean('auto_sync', false),
        ]);

        return redirect()->route('admin.federation.index')
            ->with('alert-success', 'Knowledge hub "'.$hub->name.'" added. Use Connect to fetch its manifest.');
    }

    public function update(Request $request, FederatedKnowledgeHub $hub)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'base_url' => 'required|url|max:500',
            'api_token' => 'nullable|string|max:255',
            'mapped_country_id' => 'nullable|integer|exists:country,id',
            'is_active' => 'nullable|boolean',
            'auto_sync' => 'nullable|boolean',
        ]);

        $hub->update([
            'name' => $data['name'],
            'base_url' => rtrim($data['base_url'], '/'),
            'api_token' => $data['api_token'] ?? null,
            'mapped_country_id' => $data['mapped_country_id'] ?? null,
            'is_active' => $request->boolean('is_active', false),
            'auto_sync' => $request->boolean('auto_sync', false),
        ]);

        return redirect()->route('admin.federation.index')
            ->with('alert-success', 'Knowledge hub "'.$hub->name.'" updated.');
    }

    public function destroy(FederatedKnowledgeHub $hub)
    {
        $name = $hub->name;
        $hub->delete();

        return redirect()->route('admin.federation.index')
            ->with('alert-success', 'Removed federated hub "'.$name.'".');
    }

    public function connect(FederatedKnowledgeHub $hub, FederatedHubService $federation)
    {
        $result = $federation->testConnection($hub);

        if ($result['ok']) {
            return redirect()->route('admin.federation.index')
                ->with('alert-success', 'Connected to "'.$hub->name.'". Manifest stored.');
        }

        return redirect()->route('admin.federation.index')
            ->with('alert-danger', 'Connection failed for "'.$hub->name.'": '.($result['error'] ?? 'Unknown error'));
    }

    public function sync(FederatedKnowledgeHub $hub, FederatedHubService $federation)
    {
        try {
            $federation->syncPublicData($hub);

            return redirect()->route('admin.federation.index')
                ->with('alert-success', 'Public data synced from "'.$hub->name.'". New items are queued for central admin approval before they appear on the portal.');
        } catch (\Throwable $e) {
            $hub->connection_status = 'failed';
            $hub->connection_error = $e->getMessage();
            $hub->save();

            return redirect()->route('admin.federation.index')
                ->with('alert-danger', 'Sync failed for "'.$hub->name.'": '.$e->getMessage());
        }
    }

    public function testCentral(Request $request, FederatedHubLookupService $lookup)
    {
        $data = $request->validate([
            'central_hub_url' => 'required|url|max:500',
            'central_hub_api_token' => 'nullable|string|max:255',
        ]);

        $result = $lookup->testCentralConnection(
            $data['central_hub_url'],
            $data['central_hub_api_token'] ?: null
        );

        return response()->json($result);
    }

    public function syncFromCentral(Request $request, FederatedHubLookupService $lookup)
    {
        $data = $request->validate([
            'central_hub_url' => 'required|url|max:500',
            'central_hub_api_token' => 'nullable|string|max:255',
            'import_branding' => 'nullable|boolean',
            'import_metadata' => 'nullable|boolean',
        ]);

        try {
            $summary = $lookup->importFromCentral(
                $data['central_hub_url'],
                $data['central_hub_api_token'] ?: null,
                $request->boolean('import_branding', true),
                $request->boolean('import_metadata', true)
            );

            $metaTotal = array_sum($summary['metadata'] ?? []);
            $brandTotal = array_sum($summary['branding'] ?? []);

            return redirect()->route('admin.federation.index')
                ->with('alert-success', "Central hub sync complete: {$brandTotal} branding field(s), {$metaTotal} metadata row(s) imported.");
        } catch (\Throwable $e) {
            return redirect()->route('admin.federation.index')
                ->with('alert-danger', 'Central hub sync failed: '.$e->getMessage());
        }
    }
}
