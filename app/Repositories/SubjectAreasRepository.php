<?php

namespace App\Repositories;

use App\Models\SubjectArea;
use App\Services\Kpi\KpiDeduplicationService;
use Illuminate\Http\Request;

class SubjectAreasRepository
{
    public function __construct(private KpiDeduplicationService $dedupe)
    {
    }

    public function get(Request $request)
    {
        $rows = (int) ($request->rows ?? 50);

        return SubjectArea::query()
            ->when($request->term, function ($q) use ($request) {
                $term = (string) $request->term;
                $q->where(function ($inner) use ($term) {
                    $inner->where('name', 'like', '%'.$term.'%')
                        ->orWhere('owid_topic', 'like', '%'.$term.'%');
                });
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate($rows)
            ->appends($request->all());
    }

    public function allActive()
    {
        return SubjectArea::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function save(Request $request): SubjectArea
    {
        $area = $request->id
            ? SubjectArea::query()->findOrFail($request->id)
            : new SubjectArea();

        $area->name = clean_unicode($request->name ?? '');
        $area->slug = $this->dedupe->ensureUniqueSubjectAreaSlug($area->name, $request->id ? (int) $request->id : null);
        $area->owid_topic = $request->owid_topic ?: null;
        $area->owid_search_query = $request->owid_search_query ?: null;
        $area->description = clean_unicode($request->description ?? '');
        $area->sort_order = (int) ($request->sort_order ?? 100);
        $area->is_active = (bool) $request->boolean('is_active', true);
        $area->save();

        return $area;
    }

    public function find(int $id): ?SubjectArea
    {
        return SubjectArea::query()->find($id);
    }

    public function delete(int $id): bool
    {
        $area = SubjectArea::query()->find($id);
        if (! $area) {
            return false;
        }

        if ($area->kpis()->exists()) {
            return false;
        }

        return (bool) $area->delete();
    }
}
