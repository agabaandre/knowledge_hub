<?php
namespace App\Repositories;

use App\Models\Course;
use App\Models\CourseCategory;
use Illuminate\Http\Request;

class CoursesRepository{

    public function get(Request $request){

        $rows_count = ($request->rows)?$request->rows:20;
        $qry = Course::orderBy('id','desc');

        if($request->category)
        $qry->where('category_id',$request->category);

        if($request->term)
        $qry->where(  'fullname','like',$request->term.'%');

        $courses = $qry->paginate($rows_count);

        return $courses;
    }
    

    public function categories(){

        $categories = CourseCategory::all();
        return $categories;
    }

    public function create(array $data)
    {
        return Course::create($data);
    }

    public function update($id, array $data)
    {
        $course = Course::findOrFail($id);
        $course->update($data);
        return $course;
    }

    public function import(array $rows)
    {
        $imported = [];
        foreach ($rows as $row) {
            //dd($row);
            $imported[] = Course::create($row);
        }

        return $imported;
    }
    
    public function find($id)
    {
        return Course::find($id);
    }

    public function delete($id)
    {
        $course = Course::find($id);
        if ($course) {
            return $course->delete();
        }
        return false;
    }
}