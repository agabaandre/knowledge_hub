<?php

namespace App\Services\Purge;

use App\Models\BannedEmail;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Admin incident tool: remove a spam/malicious user with all their content,
 * files and sessions in one transaction, after snapshotting everything to
 * storage/incidents/ (never web-served). Optionally soft-bans instead of
 * deleting: status=0 plus a banned_emails row that blocks re-registration.
 */
class UserPurgeService
{
    /** User-owned child tables keyed by their FK column. */
    private const USER_CHILDREN = [
        'publication_comments' => 'user_id',
        'forum_comments' => 'created_by',
        'forum_likes' => 'user_id',
        'forum_comment_likes' => 'user_id',
        'forum_subscriptions' => 'user_id',
        'forum_engagements' => 'user_id',
        'community_of_practice_members' => 'user_id',
        'user_badges' => 'user_id',
        'user_lifetime_badges' => 'user_id',
        'search_logs' => 'user_id',
        'pdf_chat_sessions' => 'user_id',
        'favourites' => 'user_id',
        'social_accounts' => 'user_id',
    ];

    public function __construct(private readonly PublicationPurger $publications)
    {
    }

    /**
     * @param  array{ban?: bool, dryRun?: bool, actorId?: int|null, reason?: string}  $options
     * @return array<string, mixed> summary of what was (or would be) removed
     */
    public function purge(User $user, array $options = []): array
    {
        $ban = (bool) ($options['ban'] ?? false);
        $dryRun = (bool) ($options['dryRun'] ?? false);
        $actorId = $options['actorId'] ?? null;
        $reason = (string) ($options['reason'] ?? 'Purged as spam/malicious');

        $publicationIds = DB::table('publication')->where('user_id', $user->id)->pluck('id')->all();
        $forumIds = DB::table('forums')->where('created_by', $user->id)->pluck('id')->all();

        $summary = [
            'user_id' => $user->id,
            'email' => (string) $user->email,
            'mode' => $ban ? 'ban' : 'delete',
            'dry_run' => $dryRun,
            'publications' => count($publicationIds),
            'forums' => count($forumIds),
            'rows' => [],
            'files' => [],
        ];

        if (! $dryRun) {
            $summary['snapshot'] = $this->writeSnapshot($user, $publicationIds, $forumIds, $actorId, $reason);
        }

        DB::transaction(function () use ($user, $publicationIds, $forumIds, $ban, $dryRun, $reason, $actorId, &$summary) {
            $this->revokeAccess($user, $dryRun, $summary);

            foreach ($publicationIds as $publicationId) {
                $result = $this->publications->purge((int) $publicationId, $dryRun);
                $summary['files'] = array_merge($summary['files'], $result['files']);
                foreach ($result['rows'] as $table => $count) {
                    $summary['rows'][$table] = ($summary['rows'][$table] ?? 0) + $count;
                }
            }

            $this->purgeForums($forumIds, $dryRun, $summary);
            $this->purgeUserChildren($user, $dryRun, $summary);

            if ($ban) {
                if (! $dryRun) {
                    $user->forceFill(['status' => 0])->save();
                    BannedEmail::firstOrCreate(
                        ['email' => strtolower((string) $user->email)],
                        ['reason' => $reason, 'banned_by' => $actorId]
                    );
                }
                $summary['rows']['banned_emails'] = 1;
            } elseif (! $dryRun) {
                $user->delete();
            }
        });

        if (! $dryRun) {
            $this->audit($user, $summary, $actorId, $reason);
        }

        return $summary;
    }

    private function revokeAccess(User $user, bool $dryRun, array &$summary): void
    {
        $tokens = DB::table('oauth_access_tokens')->where('user_id', $user->id);
        $summary['rows']['oauth_access_tokens'] = $tokens->count();
        if (! $dryRun) {
            $tokenIds = (clone $tokens)->pluck('id')->all();
            if ($tokenIds !== []) {
                DB::table('oauth_refresh_tokens')->whereIn('access_token_id', $tokenIds)->delete();
            }
            $tokens->delete();
            DB::table('oauth_auth_codes')->where('user_id', $user->id)->delete();

            if (Schema::hasTable('sessions')) {
                DB::table('sessions')->where('user_id', $user->id)->delete();
            }
            if (! empty($user->remember_token)) {
                $user->forceFill(['remember_token' => null])->save();
            }
        }
    }

    /** @param  list<int|string>  $forumIds */
    private function purgeForums(array $forumIds, bool $dryRun, array &$summary): void
    {
        if ($forumIds === []) {
            return;
        }

        // Likes on any comment inside these threads go before the comments.
        if (Schema::hasTable('forum_comment_likes')) {
            $threadCommentIds = DB::table('forum_comments')->whereIn('forum_id', $forumIds)->pluck('id')->all();
            if ($threadCommentIds !== []) {
                $likes = DB::table('forum_comment_likes')->whereIn('forum_comment_id', $threadCommentIds);
                $count = $likes->count();
                if ($count > 0) {
                    $summary['rows']['forum_comment_likes'] = ($summary['rows']['forum_comment_likes'] ?? 0) + $count;
                    if (! $dryRun) {
                        $likes->delete();
                    }
                }
            }
        }

        $children = [
            'forum_comments' => 'forum_id',
            'forum_likes' => 'forum_id',
            'forum_tags' => 'forum_id',
            'forum_subscriptions' => 'forum_id',
            'forum_approval_logs' => 'forum_id',
            'forum_community_of_practices' => 'forum_id',
        ];

        foreach ($children as $table => $column) {
            if (! Schema::hasTable($table)) {
                continue;
            }
            $query = DB::table($table)->whereIn($column, $forumIds);
            $count = $query->count();
            if ($count > 0) {
                $summary['rows'][$table] = ($summary['rows'][$table] ?? 0) + $count;
                if (! $dryRun) {
                    $query->delete();
                }
            }
        }

        $summary['rows']['forums'] = ($summary['rows']['forums'] ?? 0) + count($forumIds);
        if (! $dryRun) {
            DB::table('forums')->whereIn('id', $forumIds)->delete();
        }
    }

    private function purgeUserChildren(User $user, bool $dryRun, array &$summary): void
    {
        // Likes on this user's comments (left by anyone) go before the comments.
        if (Schema::hasTable('forum_comment_likes')) {
            $commentIds = DB::table('forum_comments')->where('created_by', $user->id)->pluck('id')->all();
            if ($commentIds !== []) {
                $likes = DB::table('forum_comment_likes')->whereIn('forum_comment_id', $commentIds);
                $count = $likes->count();
                if ($count > 0) {
                    $summary['rows']['forum_comment_likes'] = ($summary['rows']['forum_comment_likes'] ?? 0) + $count;
                    if (! $dryRun) {
                        $likes->delete();
                    }
                }
            }
        }

        foreach (self::USER_CHILDREN as $table => $column) {
            if (! Schema::hasTable($table)) {
                continue;
            }
            $query = DB::table($table)->where($column, $user->id);
            $count = $query->count();
            if ($count > 0) {
                $summary['rows'][$table] = ($summary['rows'][$table] ?? 0) + $count;
                if (! $dryRun) {
                    $query->delete();
                }
            }
        }
    }

    /** @param  list<int|string>  $publicationIds */
    private function writeSnapshot(User $user, array $publicationIds, array $forumIds, ?int $actorId, string $reason): string
    {
        $dir = storage_path('incidents');
        File::ensureDirectoryExists($dir, 0750, true);

        $snapshot = [
            'taken_at' => Carbon::now()->toIso8601String(),
            'actor_id' => $actorId,
            'reason' => $reason,
            'user' => $user->getAttributes(),
            'social_accounts' => DB::table('social_accounts')->where('user_id', $user->id)->get(),
            'publications' => DB::table('publication')->whereIn('id', $publicationIds)->get(),
            'publication_attachments' => DB::table('publication_attachments')->whereIn('publication_id', $publicationIds)->get(),
            'forums' => DB::table('forums')->whereIn('id', $forumIds)->get(),
            'forum_comments' => DB::table('forum_comments')->where('created_by', $user->id)->get(),
            'memberships' => DB::table('community_of_practice_members')->where('user_id', $user->id)->get(),
        ];

        $path = $dir.'/purge-user-'.$user->id.'-'.Carbon::now()->format('Ymd-His').'.json';
        File::put($path, json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        @chmod($path, 0640);

        return $path;
    }

    private function audit(User $user, array $summary, ?int $actorId, string $reason): void
    {
        try {
            $trail = new \App\Models\AuditTrail();
            $trail->action = 'admin.purge_user';
            $trail->user_id = $actorId;
            $trail->description = json_encode([
                'purged_user_id' => $user->id,
                'purged_email' => (string) $user->email,
                'reason' => $reason,
                'mode' => $summary['mode'],
                'rows' => $summary['rows'],
                'files' => count($summary['files']),
                'snapshot' => $summary['snapshot'] ?? null,
            ], JSON_UNESCAPED_SLASHES);
            $trail->save();
        } catch (\Throwable $e) {
            Log::warning('Could not audit user purge', ['user_id' => $user->id, 'error' => $e->getMessage()]);
        }
    }
}
