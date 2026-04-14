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
        $search = (object) $request->all();
        return view('admin.subcategories.index', compact('subcategories', 'search'));
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
            return redirect()->route('admin.subcategories.index')->with('error', 'Unauthorized.');
        }
        $this->repo->destroy((int) $request->id);
        return redirect()->route('admin.subcategories.index')
            ->with('success', 'Category deleted.');
    }
}
