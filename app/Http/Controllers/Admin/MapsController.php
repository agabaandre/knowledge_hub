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
        $this->mapsRepository->ensureMapAssignmentColumns();
        $definitions = map_all_definitions();
        $managed = $this->mapsRepository->managedDefinitions();
        $assignments = session('mapAssignments') ?: $this->mapsRepository->assignmentState();
        $definitionGroups = [
            'Regional & continental' => [],
            'African countries' => [],
            'Custom / managed' => [],
        ];
        foreach ($definitions as $id => $definition) {
            if (($definition['scope'] ?? '') === 'country') {
                $definitionGroups['African countries'][$id] = $definition;
            } elseif (! empty($definition['managed'])) {
                $definitionGroups['Custom / managed'][$id] = $definition;
            } else {
                $definitionGroups['Regional & continental'][$id] = $definition;
            }
        }

        return view('admin.maps.index', [
            'definitions' => $definitions,
            'definitionGroups' => $definitionGroups,
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
                ->with('message', 'Map preferences could not be saved. Per-view assignment columns may be missing — run php artisan migrate on this server, then try again.')
                ->with('status', 'failure');
        }

        $assignments = $this->mapsRepository->assignmentState();

        return redirect()
            ->route('admin.maps.index')
            ->with('message', 'Map assignments updated.')
            ->with('status', 'success')
            ->with('mapAssignments', $assignments);
    }

    public function searchDefinitions(Request $request)
    {
        $definitions = map_all_definitions();
        $providers = map_providers();
        $term = mb_strtolower(trim((string) $request->input('q', '')));

        $filtered = $definitions;
        if ($term !== '') {
            $filtered = array_filter(
                $definitions,
                static function (array $definition, string $id) use ($term, $providers): bool {
                    $providerLabel = (string) ($providers[$definition['provider'] ?? 'highcharts']['label'] ?? ($definition['provider'] ?? ''));
                    $tags = [];
                    if (! empty($definition['builtin'])) {
                        $tags[] = 'built-in';
                    }
                    if (($definition['scope'] ?? '') === 'country') {
                        $tags[] = 'country';
                    }
                    if (! empty($definition['managed'])) {
                        $tags[] = 'managed';
                    }

                    $haystack = mb_strtolower(implode(' ', array_filter([
                        $id,
                        $definition['label'] ?? '',
                        $providerLabel,
                        $definition['provider'] ?? '',
                        $definition['join_by'] ?? '',
                        implode(' ', $tags),
                    ])));

                    return str_contains($haystack, $term);
                },
                ARRAY_FILTER_USE_BOTH
            );
        }

        $html = view('admin.maps.partials.definitions_table_rows', [
            'definitions' => $filtered,
            'providers' => $providers,
        ])->render();

        return response()->json([
            'total' => count($definitions),
            'filtered' => count($filtered),
            'html' => $html,
        ]);
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
