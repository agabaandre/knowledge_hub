<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expert extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function country(){
        return $this->belongsTo(Country::class);
    }

    public function type(){
        return $this->belongsTo(ExpertType::class,'expert_type_id','id');
    }
}
