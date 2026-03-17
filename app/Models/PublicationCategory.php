<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class PublicationCategory extends Model
{
    use HasFactory;

    protected $table = 'publication_categories';

    protected $fillable = [
        'category_name',
        'category_desc',
        'parent_id',
    ];

    /**
     * Parent category (for sub-categories). Null for top-level categories.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(PublicationCategory::class, 'parent_id', 'id');
    }

    /**
     * Sub-categories (children) in the same table.
     */
    public function sub_categories(): HasMany
    {
        return $this->hasMany(PublicationCategory::class, 'parent_id', 'id');
    }

    public function scopeParentOnly(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    public function scopeSubCategoriesOnly(Builder $query): Builder
    {
        return $query->whereNotNull('parent_id');
    }

    protected static function booted(): void
    {
        static::created(function () {
            \Illuminate\Support\Facades\Cache::flush();
        });
        static::updated(function () {
            \Illuminate\Support\Facades\Cache::flush();
        });
        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::flush();
        });
    }
}
