<?php
namespace App\Repositories;

use App\Models\DataCategory;
use App\Models\Faq;
use App\Models\PublicationCategory;
use Illuminate\Http\Request;

class CommonsRepository{

  
    public function publication_categories(){

        $categories = PublicationCategory::all();
        return $categories;
    }

    public function resource_types(){

        $types = DataCategory::where('is_special',false)->get();
        return $types;
    }

}