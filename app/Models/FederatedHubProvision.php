<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FederatedHubProvision extends Model
{
    protected $table = 'federated_hub_provisions';

    public const STATUS_QUEUED = 'queued';

    public const STATUS_RUNNING = 'running';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'country_id',
        'slug',
        'site_name',
        'base_url',
        'target_path',
        'database_name',
        'database_username',
        'admin_email',
        'admin_first_name',
        'admin_last_name',
        'status',
        'current_step',
        'progress_percent',
        'message',
        'error_message',
        'steps_completed',
        'artifacts',
        'federated_hub_id',
        'requested_by',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'steps_completed' => 'array',
        'artifacts' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'progress_percent' => 'integer',
    ];

    protected $hidden = [
        // Never expose DB credentials stored in artifacts.
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function federatedHub(): BelongsTo
    {
        return $this->belongsTo(FederatedKnowledgeHub::class, 'federated_hub_id');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function markStep(string $step, string $message, int $percent, array $extraArtifacts = []): void
    {
        $completed = $this->steps_completed ?? [];
        if (! in_array($step, $completed, true)) {
            $completed[] = $step;
        }

        $artifacts = array_merge($this->artifacts ?? [], $extraArtifacts);

        $this->forceFill([
            'status' => self::STATUS_RUNNING,
            'current_step' => $step,
            'progress_percent' => min(100, max(0, $percent)),
            'message' => $message,
            'steps_completed' => $completed,
            'artifacts' => $artifacts,
            'started_at' => $this->started_at ?? now(),
        ])->save();
    }

    public function markCompleted(string $message, ?int $hubId = null): void
    {
        $this->forceFill([
            'status' => self::STATUS_COMPLETED,
            'current_step' => 'complete',
            'progress_percent' => 100,
            'message' => $message,
            'error_message' => null,
            'federated_hub_id' => $hubId ?? $this->federated_hub_id,
            'finished_at' => now(),
        ])->save();
    }

    public function markFailed(string $error): void
    {
        $this->forceFill([
            'status' => self::STATUS_FAILED,
            'error_message' => $error,
            'message' => 'Provisioning failed.',
            'finished_at' => now(),
        ])->save();
    }

    public function isTerminal(): bool
    {
        return in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_FAILED], true);
    }
}
