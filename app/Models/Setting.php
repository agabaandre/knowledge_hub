<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $table="setting";
    protected $hidden =["default_password"];
    public $timestamps =false;

    public function getLogoAttribute($photo){
        return storage_link('uploads/config/'.$photo);
    }

}
