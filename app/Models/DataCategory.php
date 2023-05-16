<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataCategory extends Model
{
    use HasFactory;

    public function sub_categories(){

        return $this->hasMany(DataSubCategory::class);
    }
}
