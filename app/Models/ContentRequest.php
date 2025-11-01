<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject',
        'description',
        'country_id',
        'email',
        'processed_at',
        'processed_by',
        'content_links',
        'admin_comments',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    /**
     * Get the country that owns the content request.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the user who processed the content request.
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Check if the content request is processed.
     */
    public function isProcessed(): bool
    {
        return !is_null($this->processed_at);
    }
}
