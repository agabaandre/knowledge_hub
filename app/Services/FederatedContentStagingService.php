<?php

namespace App\Services;

use App\Models\FederatedContentItem;
use App\Models\FederatedKnowledgeHub;
use Illuminate\Support\Facades\Schema;

class FederatedContentStagingService
{
    /**
     * Upsert synced remote items for central admin review.
     *
     * @param  array<string, mixed>  $cached
     * @return array{new: int, updated: int, deactivated: int}
     */
    public function stageFromSync(FederatedKnowledgeHub $hub, array $cached): array
    {
        if (! Schema::hasTable('federated_content_items')) {
            return ['new' => 0, 'updated' => 0, 'deactivated' => 0];
        }

        $stats = ['new' => 0, 'updated' => 0, 'deactivated' => 0];
        $seen = [];

        foreach (data_get($cached, 'publications.data', []) as $row) {
            if (! is_array($row)) {
                continue;
            }
            $remoteId = (int) ($row['id'] ?? 0);
            if ($remoteId <= 0) {
                continue;
            }
            $key = 'publication:'.$remoteId;
            $seen[$key] = true;
            $result = $this->upsertItem($hub, 'publication', $remoteId, $row);
            if (isset($stats[$result])) {
                $stats[$result]++;
            }
        }

        foreach (data_get($cached, 'forums.data', []) as $row) {
            if (! is_array($row)) {
                continue;
            }
            $remoteId = (int) ($row['id'] ?? 0);
            if ($remoteId <= 0) {
                continue;
            }
            $key = 'forum:'.$remoteId;
            $seen[$key] = true;
            $result = $this->upsertItem($hub, 'forum', $remoteId, $row);
            if (isset($stats[$result])) {
                $stats[$result]++;
            }
        }

        $existing = FederatedContentItem::query()
            ->where('federated_knowledge_hub_id', $hub->id)
            ->where('is_active', true)
            ->get(['id', 'content_type', 'remote_id']);

        foreach ($existing as $item) {
            $key = $item->content_type.':'.$item->remote_id;
            if (! isset($seen[$key])) {
                $item->forceFill([
                    'is_active' => false,
                    'central_approved' => false,
                ])->save();
                $stats['deactivated']++;
            }
        }

        return $stats;
    }

    public function pendingCount(): int
    {
        if (! Schema::hasTable('federated_content_items')) {
            return 0;
        }

        return FederatedContentItem::query()->pendingReview()->count();
    }

    /**
     * @param  array<string, mixed>  $row
     */
    protected function upsertItem(FederatedKnowledgeHub $hub, string $type, int $remoteId, array $row): string
    {
        $title = $type === 'forum'
            ? (string) ($row['forum_title'] ?? $row['title'] ?? 'Untitled discussion')
            : (string) ($row['title'] ?? 'Untitled resource');

        $remoteUpdated = $row['updated_at'] ?? $row['created_at'] ?? null;
        $existing = FederatedContentItem::query()
            ->where('federated_knowledge_hub_id', $hub->id)
            ->where('content_type', $type)
            ->where('remote_id', $remoteId)
            ->first();

        if (! $existing) {
            FederatedContentItem::query()->create([
                'federated_knowledge_hub_id' => $hub->id,
                'content_type' => $type,
                'remote_id' => $remoteId,
                'title' => $title,
                'payload' => $row,
                'central_approved' => false,
                'central_rejected' => false,
                'remote_updated_at' => $remoteUpdated,
                'is_active' => true,
            ]);

            return 'new';
        }

        $contentChanged = $remoteUpdated && $existing->remote_updated_at
            && (string) $existing->remote_updated_at !== (string) $remoteUpdated;

        $updates = [
            'title' => $title,
            'payload' => $row,
            'remote_updated_at' => $remoteUpdated,
            'is_active' => true,
        ];

        if ($contentChanged) {
            $updates['central_approved'] = false;
            $updates['central_rejected'] = false;
            $updates['reviewed_by'] = null;
            $updates['reviewed_at'] = null;
        }

        $existing->forceFill($updates)->save();

        return $contentChanged ? 'updated' : 'unchanged';
    }
}
