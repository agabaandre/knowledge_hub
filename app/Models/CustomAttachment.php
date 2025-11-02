<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'model',
        'path',
        'name',
        'record_id',
    ];

    protected $hidden = [
        'model',
        'id',
    ];

    public function getPathAttribute($path){
        return storage_link('uploads/'.$path);
    }

}
