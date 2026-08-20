<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\AuthorsRepository;
use App\Models\Author;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class AuthorsAdminController extends Controller
{
    private $authorsRepo;

    public function __construct(AuthorsRepository $authorsRepo)
    {
        $this->authorsRepo = $authorsRepo;
    }

    public function store(Request $request){
        if (! $this->userCanEditAuthors()) {
            return back()->with(['message' => 'Unauthorized.', 'status' => 'failure']);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'icon' => 'nullable|string|max:30',
            'is_organsiation' => 'nullable|string|max:10',
            'address' => 'nullable|string|max:200',
            'telephone' => 'nullable|string|max:13',
            'email' => 'nullable|string|max:20',
            'orcid' => 'nullable|string|max:19',
            'logo' => 'nullable|string|max:100',
        ]);
        $rawId = $request->input('id');
        $editId = ($rawId !== null && $rawId !== '') ? (int) $rawId : null;
        if ($editId) {
            $request->validate(['id' => 'integer|exists:author,id']);
        }
        $author = $editId ? Author::find($editId) : new Author();
        if (! $author) {
            return back()->with(['message' => 'Author not found', 'status' => 'failure']);
        }

        $orcid = isset($validated['orcid']) ? trim((string) $validated['orcid']) : '';
        $author->fill([
            'name' => $validated['name'],
            'icon' => ($validated['icon'] ?? '') !== '' ? $validated['icon'] : 'fa fa-archive',
            'is_organsiation' => ($validated['is_organsiation'] ?? '') !== '' ? $validated['is_organsiation'] : 'Yes',
            'address' => $validated['address'] ?? '',
            'telephone' => $validated['telephone'] ?? '',
            'email' => $validated['email'] ?? '',
            'orcid' => $orcid !== '' ? $orcid : null,
            'logo' => ($validated['logo'] ?? '') !== '' ? $validated['logo'] : 'author.png',
        ]);
        $saved = $author->save();
        if ($saved) {
            $this->authorsRepo->ensureSlug($author);
        }
        $data = $saved ? ['message' => 'Author saved successfully', 'status' => 'success'] : ['message' => 'Operation failed', 'status' => 'failure'];

        return back()->with($data);
    }

    public function index(Request $request){
        $data['search']  = (object) $request->all();
        $data['authors'] = $this->authorsRepo->get($request);
        $data['authorsForMerge'] = Author::query()->orderBy('name')->get(['id', 'name']);
        return view('admin.authors.index',$data);
    }

    /**
     * Author record for the admin edit modal (JSON).
     */
    public function show(Author $author): JsonResponse
    {
        if (! $this->userCanEditAuthors()) {
            abort(403);
        }

        return response()->json([
            'id' => $author->id,
            'name' => $author->name,
            'icon' => $author->getAttributes()['icon'] ?? 'fa fa-archive',
            'is_organsiation' => (string) ($author->getAttributes()['is_organsiation'] ?? 'Yes'),
            'address' => $author->address ?? '',
            'telephone' => $author->telephone ?? '',
            'email' => $author->email ?? '',
            'orcid' => $author->orcid,
            'logo' => $author->getAttributes()['logo'] ?? 'author.png',
            'created_at' => $author->created_at?->toIso8601String(),
            'updated_at' => $author->updated_at?->toIso8601String(),
        ]);
    }

    public function destroy(Request $request){
        if (! auth()->user()?->can('delete_publication_metadata')) {
            return back()->with(['message' => 'Unauthorized.', 'status' => 'failure']);
        }

        $request->validate([
            'id' => 'required|integer|exists:author,id',
        ]);

        try {
            $deleted = $this->authorsRepo->delete((int) $request->id);
        } catch (\Throwable $e) {
            report($e);

            return back()->with([
                'message' => 'Cannot delete author (it may still be linked to publications or users). Use Merge authors or reassign records first.',
                'status' => 'failure',
            ]);
        }

        return back()->with([
            'message' => $deleted ? 'Author deleted successfully' : 'Delete failed',
            'status' => $deleted ? 'success' : 'failure',
        ]);
    }

    public function merge(Request $request){
        if (! auth()->user()?->can('delete_publication_metadata')) {
            return back()->with(['message' => 'Unauthorized.', 'status' => 'failure']);
        }

        $validated = $request->validate([
            'keep_id' => ['required', 'integer', 'exists:author,id'],
            'merge_ids' => ['required', 'array', 'min:1'],
            'merge_ids.*' => ['integer', 'distinct', 'exists:author,id', Rule::notIn([(int) $request->input('keep_id')])],
        ]);

        try {
            $this->authorsRepo->mergeAuthors((int) $validated['keep_id'], $validated['merge_ids']);
        } catch (\InvalidArgumentException $e) {
            return back()->with(['message' => $e->getMessage(), 'status' => 'failure']);
        } catch (\Throwable $e) {
            report($e);

            return back()->with(['message' => 'Merge failed: '.$e->getMessage(), 'status' => 'failure']);
        }

        return back()->with(['message' => 'Authors merged successfully.', 'status' => 'success']);
    }

    /**
     * Create/update authors: metadata admins or sources editors.
     */
    private function userCanEditAuthors(): bool
    {
        $u = auth()->user();
        if (! $u) {
            return false;
        }

        return $u->can('delete_publication_metadata')
            || $u->can('update_sources')
            || $u->can('add_authors');
    }

  
}
