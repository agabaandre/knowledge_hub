<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Repositories\AdminUnitsRepository;
use App\Http\Controllers\Controller;
use App\Repositories\CoursesRepository;

class AdminCoursesController extends Controller
{
    private $coursesRepository;

    public function __construct(CoursesRepository $coursesRepository)
    {
        $this->coursesRepository = $coursesRepository;
    }

    public function index(Request $request){

        $data['courses'] = $this->coursesRepository->get($request);
        $data['categories'] = [];
        $data['search']    = (Object) $request->all();
        return view('admin.courses.index',$data);
    }

  

  
}
