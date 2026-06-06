<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PublicationApprovalLog extends Model
{
    protected $guarded = [];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function publication(): BelongsTo
    {
        return $this->belongsTo(Publication::class);
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function actionLabel(): string
    {
        return match ($this->action) {
            'submitted' => 'Submitted for review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'auto_approved' => 'Auto-approved',
            default => ucfirst(str_replace('_', ' ', (string) $this->action)),
        };
    }
}
