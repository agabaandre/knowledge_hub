<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FederatedContentItem extends Model
{
    protected $fillable = [
        'federated_knowledge_hub_id',
        'content_type',
        'remote_id',
        'title',
        'payload',
        'central_approved',
        'central_rejected',
        'reviewed_by',
        'reviewed_at',
        'remote_updated_at',
        'is_active',
    ];

    protected $casts = [
        'payload' => 'array',
        'central_approved' => 'boolean',
        'central_rejected' => 'boolean',
        'is_active' => 'boolean',
        'reviewed_at' => 'datetime',
        'remote_updated_at' => 'datetime',
    ];

    public function hub(): BelongsTo
    {
        return $this->belongsTo(FederatedKnowledgeHub::class, 'federated_knowledge_hub_id');
    }

    public function scopePendingReview($query)
    {
        return $query->where('is_active', true)
            ->where('central_approved', false)
            ->where('central_rejected', false);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_active', true)->where('central_approved', true);
    }
}
