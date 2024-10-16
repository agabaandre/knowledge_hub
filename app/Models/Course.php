<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    protected $guarded=[];
    public $appends = ['course_link'];

    public function getCourseLinkAttribute()
    {
        return env('MOODLE_URL') . "/elearning/course/view.php?id=" . $this->moodle_id;
    }


    public function category(){
        return $this->belongsTo(CourseCategory::class,'course_id','moodle_id');
    }

    public function getCoverImageAttribute($value){
        return asset('storage/uploads/users/avatar.jpg');
    }
}
