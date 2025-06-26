<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicationSummary extends Model
{
    use HasFactory;

    public function author(){

        return $this->belongsTo(Author::class);
    }

    public function user(){

        return $this->belongsTo(User::class);
    }

    public function publication(){

        return $this->belongsTo(Publication::class,"resource_id");
    }
}
