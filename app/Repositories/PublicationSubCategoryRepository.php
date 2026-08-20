<?php

namespace App\Repositories;

use App\Models\PublicationCategory;
use App\Models\Publication;
use App\Models\DataCategory;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class PublicationSubCategoryRepository
{
    /**
     * Paginated list of categories (parent-only from publication_categories).
     */
    public function getPaginated(Request $request): LengthAwarePaginator
    {
        $query = PublicationCategory::parentOnly()
            ->withCount('sub_categories')
            ->with('linkedDataCategories:id,category_name')
            ->orderBy('category_name');

        if ($request->filled('term')) {
            $term = $request->term;
            $query->where(function ($q) use ($term) {
                $q->where('category_name', 'like', '%' . $term . '%')
                    ->orWhere('category_desc', 'like', '%' . $term . '%');
            });
        }

        return $query->paginate($request->get('per_page', 20))->withQueryString();
    }

    /**
     * Store a new category (parent; parent_id = null).
     */
    public function store(Request $request): PublicationCategory
    {
        $cat = new PublicationCategory();
        $cat->parent_id = null;
        $cat->category_name = $request->category_name;
        $cat->category_desc = $request->category_desc;
        $cat->save();

        $linkedIds = collect($request->input('linked_data_categories', []))
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        if (empty($linkedIds)) {
            $linkedIds = DataCategory::query()->pluck('id')->map(fn ($id) => (int) $id)->all();
        }
        $cat->linkedDataCategories()->sync($linkedIds);
        Cache::forget('file_categories');
        return $cat;
    }

    /**
     * Update a category (parent-only).
     */
    public function update(Request $request, int $id): ?PublicationCategory
    {
        $cat = PublicationCategory::parentOnly()->find($id);
        if (!$cat) {
            return null;
        }
        $cat->category_name = $request->category_name;
        $cat->category_desc = $request->category_desc;
        $cat->save();

        $linkedIds = collect($request->input('linked_data_categories', []))
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        if (empty($linkedIds)) {
            $linkedIds = DataCategory::query()->pluck('id')->map(fn ($id) => (int) $id)->all();
        }
        $cat->linkedDataCategories()->sync($linkedIds);
        Cache::forget('file_categories');
        return $cat;
    }

    /**
     * Delete a category (parent). Subcategories are cascade-deleted by DB.
     */
    public function destroy(int $id): bool
    {
        $cat = PublicationCategory::parentOnly()->find($id);
        return $cat ? $cat->delete() : false;
    }

    public function allParentCategoriesForMapping()
    {
        return PublicationCategory::parentOnly()
            ->orderBy('category_name')
            ->get(['id', 'category_name']);
    }

    public function allDataCategories()
    {
        return DataCategory::query()
            ->orderBy('category_name')
            ->get(['id', 'category_name']);
    }

    public function destroyWithMapping(int $id, int $replacementId): array
    {
        if ($id === $replacementId) {
            return ['status' => 'failure', 'message' => 'Please select a different category to map data to.'];
        }

        $old = PublicationCategory::parentOnly()->find($id);
        $new = PublicationCategory::parentOnly()->find($replacementId);
        if (! $old || ! $new) {
            return ['status' => 'failure', 'message' => 'Selected category was not found.'];
        }

        return DB::transaction(function () use ($old, $new) {
            $movedSubcategories = PublicationCategory::query()
                ->where('parent_id', $old->id)
                ->update(['parent_id' => $new->id]);

            $movedPublications = Publication::query()
                ->where('publication_catgory_id', $old->id)
                ->count();
            if ($movedPublications > 0) {
                Publication::query()
                    ->where('publication_catgory_id', $old->id)
                    ->update(['publication_catgory_id' => $new->id]);
            }

            // Defensive mapping in case legacy rows used parent id directly here.
            $legacySubcategoryRefs = Publication::query()
                ->where('publication_sub_category_id', $old->id)
                ->count();
            if ($legacySubcategoryRefs > 0) {
                Publication::query()
                    ->where('publication_sub_category_id', $old->id)
                    ->update(['publication_sub_category_id' => $new->id]);
            }

            $remappedLinkedDataCategories = 0;
            $linkedDataCategoryIds = DB::table('data_category_publication_category')
                ->where('publication_category_id', $old->id)
                ->pluck('data_category_id');
            foreach ($linkedDataCategoryIds as $dataCategoryId) {
                DB::table('data_category_publication_category')->updateOrInsert(
                    [
                        'data_category_id' => (int) $dataCategoryId,
                        'publication_category_id' => (int) $new->id,
                    ],
                    [
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
                $remappedLinkedDataCategories++;
            }
            DB::table('data_category_publication_category')
                ->where('publication_category_id', $old->id)
                ->delete();

            $old->delete();

            return [
                'status' => 'success',
                'message' => 'Category deleted and data mapped successfully.',
                'data' => [
                    'moved_subcategories' => (int) $movedSubcategories,
                    'moved_publications' => (int) $movedPublications,
                    'moved_legacy_subcategory_refs' => (int) $legacySubcategoryRefs,
                    'remapped_linked_data_categories' => (int) $remappedLinkedDataCategories,
                    'deleted_category_id' => (int) $old->id,
                    'replacement_category_id' => (int) $new->id,
                ],
            ];
        });
    }
}
