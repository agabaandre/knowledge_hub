<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdministrativeUnit extends Model
{
    use HasFactory;

    public function parent(){

       return $this->belongsTo(AdministrativeUnit::class,"parent_id","id");
    }
}
