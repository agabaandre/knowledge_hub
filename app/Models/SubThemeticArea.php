<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubThemeticArea extends Model
{
    use HasFactory;

    protected $table="sub_thematic_area";

    public function theme(){
        return $this->belongsTo(SubjectArea::class,"thematic_area_id","id");
    }
}
