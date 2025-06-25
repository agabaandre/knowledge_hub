<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    protected $guarded=[];
    protected $fillable = [
        'moodle_id', 'fullname', 'shortname', 'cover_image', 'category_id', 'summary',
        'provider', 'content', 'course_url', 'is_moodle', 'is_active'
    ];
    public $appends = ['course_link'];

    public function getCourseLinkAttribute()
    {
        return env('MOODLE_URL') . "/elearning/course/view.php?id=" . $this->moodle_id;
    }


    public function category(){
        return $this->belongsTo(CourseCategory::class, 'category_id', 'id');
    }

    public function getCoverImageAttribute($value){
        return filter_var($value, FILTER_VALIDATE_URL) ? $value : asset('storage/uploads/courses/' . $value);
    }
}
