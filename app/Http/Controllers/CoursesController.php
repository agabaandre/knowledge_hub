<?php

namespace App\Http\Controllers;

use App\Repositories\CoursesRepository;
use Illuminate\Http\Request;
class CoursesController extends Controller
{
    private $courseRepo;

    public function __construct( CoursesRepository $courseRepo)
    {
        $this->courseRepo       = $courseRepo;
    }

    public function index(Request $request){

        $data['courses'] = $this->courseRepo->get($request);
        return view('courses.index',$data);
    }

}
