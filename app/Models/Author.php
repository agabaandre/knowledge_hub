<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;
    protected $table = "author";

    protected $fillable =['name'];

    public function publications(){
        return $this->hasMany(Publication::class);
    }

    public function getLogoAttribute($value){
        return asset('storage/uploads/users/avatar.jpg');
    }

}
