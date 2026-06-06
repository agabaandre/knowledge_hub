<?php

namespace App\Support;

use App\Models\Forum;
use App\Models\ForumApprovalLog;
use App\Models\Publication;
use App\Models\PublicationApprovalLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LegacyApprovalLogBackfill
{
    public static function backfillPublications(): int
    {
        $inserted = 0;

        Publication::query()
            ->where('is_version', 0)
            ->whereDoesntHave('approvalLogs')
            ->with(['user', 'approver', 'rejector'])
            ->orderBy('id')
            ->chunkById(200, function ($publications) use (&$inserted) {
                foreach ($publications as $publication) {
                    $inserted += self::insertPublicationRows($publication);
                }
            });

        return $inserted;
    }

    public static function backfillForums(): int
    {
        $inserted = 0;

        Forum::query()
            ->whereDoesntHave('approvalLogs')
            ->with(['user', 'approver', 'rejector'])
            ->orderBy('id')
            ->chunkById(200, function ($forums) use (&$inserted) {
                foreach ($forums as $forum) {
                    $inserted += self::insertForumRows($forum);
                }
            });

        return $inserted;
    }

    public static function insertPublicationRows(Publication $publication): int
    {
        $rows = self::legacyRowsForRecord(
            submitterId: $publication->user_id,
            submitterName: $publication->user?->name,
            createdAt: $publication->created_at,
            isApproved: (int) ($publication->is_approved ?? 0) === 1,
            isRejected: (int) ($publication->is_rejected ?? 0) === 1,
            approvedBy: $publication->approved_by,
            approverName: $publication->approver?->name,
            rejectedBy: $publication->rejected_by,
            rejectorName: $publication->rejector?->name,
            rejectedReason: $publication->rejected_reason,
            rejectedAt: $publication->rejected_at ?? null,
            terminalAt: $publication->updated_at ?? $publication->created_at,
        );

        $count = 0;
        foreach ($rows as $row) {
            PublicationApprovalLog::create(array_merge($row, [
                'publication_id' => $publication->id,
            ]));
            $count++;
        }

        return $count;
    }

    public static function insertForumRows(Forum $forum): int
    {
        $rows = self::legacyRowsForRecord(
            submitterId: $forum->created_by,
            submitterName: $forum->user?->name,
            createdAt: $forum->created_at,
            isApproved: (int) ($forum->is_approved ?? 0) === 1 && (int) ($forum->status ?? 0) === 1,
            isRejected: (int) ($forum->is_rejected ?? 0) === 1,
            approvedBy: $forum->approved_by,
            approverName: $forum->approver?->name,
            rejectedBy: $forum->rejected_by,
            rejectorName: $forum->rejector?->name,
            rejectedReason: $forum->rejected_reason,
            rejectedAt: $forum->updated_at ?? null,
            terminalAt: $forum->updated_at ?? $forum->created_at,
        );

        $count = 0;
        foreach ($rows as $row) {
            ForumApprovalLog::create(array_merge($row, [
                'forum_id' => $forum->id,
            ]));
            $count++;
        }

        return $count;
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected static function legacyRowsForRecord(
        ?int $submitterId,
        ?string $submitterName,
        $createdAt,
        bool $isApproved,
        bool $isRejected,
        $approvedBy,
        ?string $approverName,
        $rejectedBy,
        ?string $rejectorName,
        ?string $rejectedReason,
        $rejectedAt,
        $terminalAt,
    ): array {
        $metadata = ['backfilled' => true, 'source' => 'legacy_columns'];
        $rows = [];

        if ($submitterId) {
            $rows[] = [
                'action' => 'submitted',
                'performed_by' => $submitterId,
                'performed_by_name' => $submitterName ?: User::query()->whereKey($submitterId)->value('name'),
                'reason' => null,
                'metadata' => $metadata,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];
        }

        if ($isRejected) {
            $when = $rejectedAt ?? $terminalAt;
            $rows[] = [
                'action' => 'rejected',
                'performed_by' => $rejectedBy,
                'performed_by_name' => $rejectorName
                    ?: ($rejectedBy ? User::query()->whereKey($rejectedBy)->value('name') : null),
                'reason' => $rejectedReason,
                'metadata' => $metadata,
                'created_at' => $when,
                'updated_at' => $when,
            ];
        } elseif ($isApproved) {
            $when = $terminalAt ?? $createdAt;
            $hasApprover = !empty($approvedBy);
            $rows[] = [
                'action' => $hasApprover ? 'approved' : 'legacy_approved',
                'performed_by' => $approvedBy,
                'performed_by_name' => $approverName
                    ?: ($approvedBy ? User::query()->whereKey($approvedBy)->value('name') : null),
                'reason' => null,
                'metadata' => array_merge($metadata, [
                    'moderator_not_recorded' => !$hasApprover,
                ]),
                'created_at' => $when,
                'updated_at' => $when,
            ];
        }

        return $rows;
    }
}
