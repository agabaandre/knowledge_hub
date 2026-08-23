<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataCategory extends Model
{
    use HasFactory;

    protected $casts = [
        'show_on_menu' => 'boolean',
        'is_special' => 'boolean',
        'is_dashboard' => 'boolean',
        'is_restricted' => 'boolean',
    ];

    public function menuUrlPath(): string
    {
        return (string) ($this->url_path ?? $this->url_path ?? '');
    }

    public function showsOnMenu(): bool
    {
        return (bool) ($this->show_on_menu ?? $this->show_on_menu ?? false);
    }

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
