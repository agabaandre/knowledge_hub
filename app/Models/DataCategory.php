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

    public function publication_categories()
    {
        return $this->belongsToMany(
            PublicationCategory::class,
            'data_category_publication_category',
            'data_category_id',
            'publication_category_id'
        );
    }
}
