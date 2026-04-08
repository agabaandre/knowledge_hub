<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\AuthorsRepository;
use App\Services\UITableService;
use App\Models\Author;
use Illuminate\Validation\Rule;

class AuthorsAdminController extends Controller
{
    private $authorsRepo, $uiTableService;

    public function __construct(AuthorsRepository $authorsRepo, UITableService $uiTableServie)
    {
        $this->authorsRepo    = $authorsRepo;
        $this->uiTableService = $uiTableServie;
    }

    public function store(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);
        $author = ($request->id) ? Author::find($request->id) : new Author();
        if(!$author){ return back()->with(['message'=>'Author not found','status'=>'failure']); }
        $author->name = $validated['name'];
        $saved = $request->id ? $author->update() : $author->save();
        $data = $saved ? ['message'=>'Author saved successfully','status'=>'success'] : ['message'=>'Operation failed','status'=>'failure'];
        return back()->with($data);
    }

    public function index(Request $request){
        $data['search']  = (object) $request->all();
        $data['authors'] = $this->authorsRepo->get($request);
        $data['authorsForMerge'] = Author::query()->orderBy('name')->get(['id', 'name']);
        return view('admin.authors.index',$data);
    }

    public function destroy(Request $request){
        $deleted = $this->authorsRepo->delete($request->id);
        return back()->with([
            'message' => $deleted ? 'Author deleted successfully' : 'Delete failed',
            'status' => $deleted ? 'success' : 'failure'
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

  
}
