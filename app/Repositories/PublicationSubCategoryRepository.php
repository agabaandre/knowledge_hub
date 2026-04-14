<?php

namespace App\Repositories;

use App\Models\PublicationCategory;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PublicationSubCategoryRepository
{
    /**
     * Paginated list of categories (parent-only from publication_categories).
     */
    public function getPaginated(Request $request): LengthAwarePaginator
    {
        $query = PublicationCategory::parentOnly()->withCount('sub_categories')->orderBy('category_name');

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
}
