<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\AuthorsRepository;
use App\Services\UITableService;
use App\Models\Author;

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
        return view('admin.authors.index',$data);
    }

    public function destroy(Request $request){
        $deleted = $this->authorsRepo->delete($request->id);
        return back()->with([
            'message' => $deleted ? 'Author deleted successfully' : 'Delete failed',
            'status' => $deleted ? 'success' : 'failure'
        ]);
    }

  
}
