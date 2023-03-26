<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubjectArea extends Model
{
    use HasFactory;

    protected $appends = ['description'];

    public function getDescriptionAttribute(){
        return $this->name;
    }
}
