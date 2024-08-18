<?php
namespace App\Repositories;

use App\Models\Course;
use Illuminate\Http\Request;

class CoursesRepository{

    public function get(Request $request){

        $rows_count = ($request->rows)?$request->rows:20;
        $courses = Course::paginate($rows_count);

        return $courses;
    }
    



}