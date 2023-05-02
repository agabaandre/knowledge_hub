<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\AuthorsRepository;

class AuthorsAdminController extends Controller
{
    private $authorsRepo;

    public function __construct(AuthorsRepository $authorsRepo)
    {
        $this->authorsRepo = $authorsRepo;
    }

    public function index(Request $request){

        $data['authors'] = $this->authorsRepo->get($request);
        $data['search']       = (Object) $request->all();
        return view('admin.authors.index',$data);
    }

    public function destroy(Request $request){

        return $this->authorsRepo->delete($request->id);
    }

  
}
