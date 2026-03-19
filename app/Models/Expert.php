<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expert extends Model
{
    use HasFactory;

    public $timestamps = false;

    /** Allow persisting attributes set directly in ExpertsRepository (avoids guarded issues on some setups). */
    protected $guarded = [];

    public function country(){
        return $this->belongsTo(Country::class);
    }

    public function type(){
        return $this->belongsTo(ExpertType::class,'expert_type_id','id');
    }

    public function jobTitle(){
        return $this->belongsTo(JobTitle::class, 'job_title_id', 'id');
    }

    public function iscoClassification(){
        return $this->belongsTo(IscoClassification::class, 'isco_classification_id', 'id');
    }
}
