<?php

namespace App\View\Composers;

use App\Models\PublicationCategory;
use Illuminate\View\View;

class PublicationCategoryViewComposer
{
    public function compose(View $view): void
    {
        $minutes = env('CACHE_EXPIRY_DURATION_MINUTES', 60 * 24);

        $file_categories = cache()->remember('file_categories', $minutes, function () {
            return PublicationCategory::parentOnly()->orderBy('category_name', 'asc')->get();
        });

        $publication_sub_categories = cache()->remember('publication_sub_categories', $minutes, function () {
            return PublicationCategory::subCategoriesOnly()->with('parent')->orderBy('category_name')->get();
        });

        $view->with('file_categories', $file_categories);
        $view->with('publication_sub_categories', $publication_sub_categories);
    }
}