<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\FederatedKnowledgeHub;
use App\Services\FederatedHubService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class FederatedHubsController extends Controller
{
    public function index(FederatedHubService $federation)
    {
        $hubs = Schema::hasTable('federated_knowledge_hubs')
            ? FederatedKnowledgeHub::with('mappedCountry')->orderBy('name')->get()
            : collect();

        return view('admin.federation.index', [
            'hubs' => $hubs,
            'localManifest' => $federation->localManifest(),
            'countries' => Country::orderBy('name')->get(),
            'federationToken' => $federation->federationToken(),
            'federationApiBase' => url('/api/federation'),
        ]);
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
                ->with('alert-success', 'Public data synced from "'.$hub->name.'".');
        } catch (\Throwable $e) {
            $hub->connection_status = 'failed';
            $hub->connection_error = $e->getMessage();
            $hub->save();

            return redirect()->route('admin.federation.index')
                ->with('alert-danger', 'Sync failed for "'.$hub->name.'": '.$e->getMessage());
        }
    }
}
