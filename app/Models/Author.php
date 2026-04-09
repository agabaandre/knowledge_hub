<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;
    protected $table = "author";

    protected $fillable = [
        'name',
        'icon',
        'is_organsiation',
        'address',
        'telephone',
        'email',
        'orcid',
        'logo',
    ];

    public function publications(){
        return $this->hasMany(Publication::class);
    }

    public function user(){
        return $this->hasOne(User::class, 'author_id');
    }

}
