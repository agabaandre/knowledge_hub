<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ForumApprovalLog extends Model
{
    protected $guarded = [];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function forum(): BelongsTo
    {
        return $this->belongsTo(Forum::class);
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
            'resubmitted' => 'Resubmitted after rejection',
            'auto_approved' => 'Auto-approved',
            'legacy_approved' => 'Approved (moderator not recorded)',
            default => ucfirst(str_replace('_', ' ', (string) $this->action)),
        };
    }
}
