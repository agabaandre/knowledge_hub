<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicationAttachment extends Model
{
    use HasFactory;

    public function getFileAttribute($value){
        return storage_link('uploads/publications/'.$value);
    }

}
