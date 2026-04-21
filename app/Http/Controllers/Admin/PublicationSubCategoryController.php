<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\PublicationSubCategoryRepository;
use Illuminate\Http\Request;

class PublicationSubCategoryController extends Controller
{
    public function __construct(
        protected PublicationSubCategoryRepository $repo
    ) {}

    public function index(Request $request)
    {
        $subcategories = $this->repo->getPaginated($request);
        $allCategoriesForMapping = $this->repo->allParentCategoriesForMapping();
        $search = (object) $request->all();
        return view('admin.subcategories.index', compact('subcategories', 'search', 'allCategoriesForMapping'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:255',
            'category_desc' => 'nullable|string|max:500',
        ]);
        $this->repo->store($request);
        return redirect()->route('admin.subcategories.index')
            ->with('success', 'Category created successfully.');
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'category_name' => 'required|string|max:255',
            'category_desc' => 'nullable|string|max:500',
        ]);
        $this->repo->update($request, $id);
        return redirect()->route('admin.subcategories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Request $request)
    {
        if (!auth()->user()?->can('delete_publication_metadata')) {
            return response()->json(['status' => 'failure', 'message' => 'Unauthorized.'], 403);
        }

        $request->validate([
            'id' => 'required|integer|exists:publication_categories,id',
            'replacement_category_id' => 'required|integer|exists:publication_categories,id|different:id',
        ]);

        $result = $this->repo->destroyWithMapping((int) $request->id, (int) $request->replacement_category_id);
        $statusCode = ($result['status'] ?? 'failure') === 'success' ? 200 : 422;

        return response()->json($result, $statusCode);
    }
}
