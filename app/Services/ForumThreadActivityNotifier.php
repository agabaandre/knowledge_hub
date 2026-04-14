<?php

namespace App\Services;

use App\Jobs\SendMailJob;
use App\Models\Forum;
use App\Models\ForumComment;
use App\Models\ForumCommentLike;
use App\Models\ForumLike;
use App\Models\ForumSubscription;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Notifies the forum creator and other participants (commenters, likers, subscribers)
 * when there is new activity on a thread (comment, like, or view milestones).
 */
class ForumThreadActivityNotifier
{
    /** Total view counts at which we send a single notification (avoids spam on every page view). */
    private const VIEW_MILESTONES = [5, 10, 25, 50, 100, 250, 500, 1000];

    /**
     * User IDs to notify: creator, subscribers, users who commented, liked the thread, or liked a comment.
     */
    public static function participatingUserIds(int $forumId): Collection
    {
        $ids = collect();

        $forum = Forum::query()->find($forumId);
        if ($forum && $forum->created_by) {
            $ids->push((int) $forum->created_by);
        }

        $ids = $ids->merge(
            ForumSubscription::query()->where('forum_id', $forumId)->pluck('user_id')
        );

        $ids = $ids->merge(
            ForumComment::query()
                ->where('forum_id', $forumId)
                ->where(function ($q) {
                    $q->where('status', 'approved')
                        ->orWhereNull('status');
                })
                ->whereNotNull('created_by')
                ->distinct()
                ->pluck('created_by')
        );

        $ids = $ids->merge(
            ForumLike::query()->where('forum_id', $forumId)->pluck('user_id')
        );

        $commentIds = ForumComment::query()->where('forum_id', $forumId)->pluck('id');
        if ($commentIds->isNotEmpty()) {
            $ids = $ids->merge(
                ForumCommentLike::query()
                    ->whereIn('forum_comment_id', $commentIds)
                    ->pluck('user_id')
            );
        }

        return $ids->filter()->map(fn ($id) => (int) $id)->unique()->values();
    }

    /**
     * @return int[] User IDs to receive email/push (excludes actor).
     */
    public static function recipientUserIds(int $forumId, ?int $actorUserId): array
    {
        $participants = self::participatingUserIds($forumId);
        if ($actorUserId !== null && $actorUserId > 0) {
            $participants = $participants->reject(fn ($id) => (int) $id === (int) $actorUserId);
        }

        return $participants->unique()->values()->all();
    }

    public static function notifyNewComment(Forum $forum, ForumComment $comment): void
    {
        if (! self::forumIsPublic($forum)) {
            return;
        }
        $status = (string) ($comment->status ?? '');
        if ($status !== '' && strcasecmp($status, 'approved') !== 0) {
            return;
        }

        $actorId = (int) ($comment->created_by ?? 0);
        $actor = $actorId ? User::query()->find($actorId) : null;
        $actorName = $actor->name ?? 'Someone';

        $snippet = self::snippet(strip_tags((string) $comment->comment), 180);

        self::dispatchToRecipients(
            $forum,
            self::recipientUserIds((int) $forum->id, $actorId ?: null),
            'New comment on: '.self::title($forum),
            'New comment on a discussion you follow',
            sprintf('%s commented on <strong>%s</strong>.', e($actorName), e(self::title($forum))),
            $snippet !== '' ? '<p style="margin:12px 0;color:#334155;">'.e($snippet).'</p>' : '',
            $actorName
        );
    }

    public static function notifyForumLiked(Forum $forum, int $likerUserId): void
    {
        if (! self::forumIsPublic($forum)) {
            return;
        }

        $liker = User::query()->find($likerUserId);
        $actorName = $liker->name ?? 'Someone';

        self::dispatchToRecipients(
            $forum,
            self::recipientUserIds((int) $forum->id, $likerUserId),
            'Someone liked your discussion: '.self::title($forum),
            'New like on a forum thread',
            sprintf('%s liked the discussion <strong>%s</strong>.', e($actorName), e(self::title($forum))),
            '',
            $actorName
        );
    }

    public static function notifyCommentLiked(Forum $forum, ForumComment $comment, int $likerUserId): void
    {
        if (! self::forumIsPublic($forum)) {
            return;
        }

        $liker = User::query()->find($likerUserId);
        $actorName = $liker->name ?? 'Someone';

        $snippet = self::snippet(strip_tags((string) $comment->comment), 120);

        self::dispatchToRecipients(
            $forum,
            self::recipientUserIds((int) $forum->id, $likerUserId),
            'Someone liked a comment on: '.self::title($forum),
            'New like on a forum comment',
            sprintf(
                '%s liked a comment on <strong>%s</strong>.',
                e($actorName),
                e(self::title($forum))
            ),
            $snippet !== '' ? '<p style="margin:12px 0;color:#334155;font-size:14px;">'.e($snippet).'</p>' : '',
            $actorName
        );
    }

    /**
     * Call after the forums.views counter was incremented. Notifies only at milestone totals.
     */
    public static function notifyViewMilestoneIfApplicable(Forum $forum, int $viewCountAfterIncrement): void
    {
        if (! self::forumIsPublic($forum)) {
            return;
        }
        if (! in_array($viewCountAfterIncrement, self::VIEW_MILESTONES, true)) {
            return;
        }

        self::dispatchToRecipients(
            $forum,
            self::recipientUserIds((int) $forum->id, null),
            'Discussion reached '.number_format($viewCountAfterIncrement).' views: '.self::title($forum),
            'Forum thread view milestone',
            sprintf(
                'The discussion <strong>%s</strong> has reached <strong>%s</strong> views.',
                e(self::title($forum)),
                number_format($viewCountAfterIncrement)
            ),
            '',
            null
        );
    }

    private static function forumIsPublic(Forum $forum): bool
    {
        return (int) $forum->status === 1
            && (int) $forum->is_approved === 1
            && (int) ($forum->is_rejected ?? 0) === 0;
    }

    private static function title(Forum $forum): string
    {
        $t = trim((string) ($forum->forum_title ?? ''));

        return $t !== '' ? $t : 'Forum discussion';
    }

    private static function snippet(string $text, int $max): string
    {
        $text = preg_replace('/\s+/u', ' ', $text);

        return $text !== null && strlen($text) > $max ? substr($text, 0, $max).'…' : (string) $text;
    }

    /**
     * @param  string  $emailSubject
     * @param  string  $emailTitle  layout title
     * @param  string  $leadHtml  main sentence (already escaped where needed)
     * @param  string  $extraHtml  optional HTML block
     */
    private static function dispatchToRecipients(
        Forum $forum,
        array $userIds,
        string $emailSubject,
        string $emailTitle,
        string $leadHtml,
        string $extraHtml,
        ?string $actorName
    ): void {
        if ($userIds === []) {
            return;
        }

        $threadUrl = url('forums/thread?id='.$forum->id);

        $users = User::query()
            ->whereIn('id', $userIds)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->get(['id', 'name', 'email']);

        foreach ($users as $user) {
            try {
                SendMailJob::dispatch([
                    'to' => $user->email,
                    'subject' => $emailSubject,
                    'title' => $emailTitle,
                    'body' => view('emails.forum_thread_activity', [
                        'recipientName' => $user->name,
                        'forumTitle' => self::title($forum),
                        'threadUrl' => $threadUrl,
                        'leadHtml' => $leadHtml,
                        'extraHtml' => $extraHtml,
                        'actorName' => $actorName,
                    ])->render(),
                ])->onQueue('default');
            } catch (\Throwable $e) {
                Log::error('ForumThreadActivityNotifier: failed to queue mail', [
                    'forum_id' => $forum->id,
                    'user_id' => $user->id,
                    'message' => $e->getMessage(),
                ]);
            }
        }
    }
}
