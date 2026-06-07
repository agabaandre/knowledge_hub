<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\MapsRepository;
use Illuminate\Http\Request;

class MapsController extends Controller
{
    private MapsRepository $mapsRepository;

    public function __construct(MapsRepository $mapsRepository)
    {
        $this->mapsRepository = $mapsRepository;
    }

    public function index()
    {
        $definitions = map_all_definitions();
        $managed = $this->mapsRepository->managedDefinitions();
        $assignments = session('mapAssignments') ?: $this->mapsRepository->assignmentState();

        return view('admin.maps.index', [
            'definitions' => $definitions,
            'managedMaps' => $managed,
            'providers' => map_providers(),
            'topologyPresets' => map_topology_presets(),
            'joinOptions' => map_join_options(),
            'viewContexts' => map_view_context_labels(),
            'defaultMapId' => $assignments['defaultMapId'],
            'viewAssignments' => $assignments['viewAssignments'],
            'showAdminUnitsMap' => $assignments['showAdminUnitsMap'],
            'mapColumnsReady' => $assignments['columnsReady'],
            'missingMapColumns' => $assignments['missingColumns'] ?? [],
            'mapSettingsDebug' => [
                'rowId' => $assignments['settingsRowId'] ?? null,
                'rowTheme' => $assignments['settingsRowTheme'] ?? null,
                'rawDefault' => $assignments['rawDefaultMapId'] ?? null,
                'rawViews' => $assignments['rawViewVersions'] ?? null,
            ],
            'topologyVersion' => config('maps.topology_version', '2.3.3'),
        ]);
    }

    public function create()
    {
        return view('admin.maps.form', [
            'map' => null,
            'providers' => map_providers(),
            'topologyPresets' => map_topology_presets(),
            'joinOptions' => map_join_options(),
            'topologyVersion' => config('maps.topology_version', '2.3.3'),
        ]);
    }

    public function edit(int $id)
    {
        $map = $this->mapsRepository->find($id);
        if (! $map) {
            abort(404);
        }

        return view('admin.maps.form', [
            'map' => $map,
            'providers' => map_providers(),
            'topologyPresets' => map_topology_presets(),
            'joinOptions' => map_join_options(),
            'topologyVersion' => config('maps.topology_version', '2.3.3'),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'label' => 'required|string|max:255',
            'provider' => 'required|string|max:40',
            'source_type' => 'required|in:topojson_url,geojson_script',
            'join_by' => 'required|string|max:40',
        ]);

        $record = $this->mapsRepository->save($request);

        return redirect()
            ->route('admin.maps.index')
            ->with('message', 'Map definition saved.')
            ->with('status', 'success');
    }

    public function destroy(int $id)
    {
        $this->mapsRepository->delete($id);

        return redirect()
            ->route('admin.maps.index')
            ->with('message', 'Map definition deleted.')
            ->with('status', 'success');
    }

    public function saveAssignments(Request $request)
    {
        $saved = $this->mapsRepository->saveAssignments($request);

        if (! $saved) {
            return redirect()
                ->route('admin.maps.index')
                ->with('message', 'Map preferences could not be saved. Run database migrations on this server (php artisan migrate), then try again.')
                ->with('status', 'failure');
        }

        $assignments = $this->mapsRepository->assignmentState();

        return redirect()
            ->route('admin.maps.index')
            ->with('message', 'Map assignments updated.')
            ->with('status', 'success')
            ->with('mapAssignments', $assignments);
    }

    public function preview(Request $request, ?string $slug = null)
    {
        $slug = $slug ?: trim((string) $request->input('slug', ''));
        if ($slug === '') {
            return response()->json(['error' => 'Map slug required.'], 422);
        }

        $definitions = map_all_definitions();
        if (! isset($definitions[$slug])) {
            return response()->json(['error' => 'Map not found.'], 404);
        }

        $config = resolved_map_config(null, $slug);

        return response()->json([
            'slug' => $slug,
            'label' => $config['label'] ?? $slug,
            'provider' => $config['provider'] ?? 'highcharts',
            'topology_url' => $config['topology_url'] ?? null,
            'join_by' => $config['join_by'] ?? 'iso-a3',
            'iso_property' => $config['iso_property'] ?? null,
            'version' => $config['version'] ?? null,
            'type' => $config['type'] ?? null,
        ]);
    }
}
