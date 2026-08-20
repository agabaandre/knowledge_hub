<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class ContentWipeService
{
    /**
     * @return array{tables: array<string, int>, resets: list<string>}
     */
    public function wipe(bool $includeCommunities = true): array
    {
        if (! config('hub_content.allow_content_wipe')) {
            throw new RuntimeException('Content wipe is disabled. Set HUB_ALLOW_CONTENT_WIPE=true in .env.');
        }

        $deleted = [];
        $resets = [];

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            $childTables = [
                // Access / visit logs tied to publications (and related browsing)
                'access_logs',
                'odm_access_log',
                'odm_log',
                'search_logs',

                // Forum / community / publication children
                'forum_comment_likes',
                'community_comment_likes',
                'forum_comments',
                'community_comments',
                'publication_comments',
                'forum_likes',
                'forum_subscriptions',
                'forum_tags',
                'forum_approval_logs',
                'forum_community_of_practices',
                'forum_engagements',
                'publication_attachments',
                'publication_tags',
                'publication_views',
                'publication_approval_logs',
                'publication_community_of_practices',
                'publication_countries',
                'publication_access_groups',
                'publication_summaries',
                'favourites',
                'geographical_scope_publication',
                'publications_staging',
                'pdf_chat_messages',
                'pdf_chat_sessions',
            ];

            foreach ($childTables as $table) {
                $deleted[$table] = $this->deleteAll($table);
            }

            if (Schema::hasTable('custom_attachments')) {
                $deleted['custom_attachments'] = DB::table('custom_attachments')
                    ->whereIn('model', ['forum_comments', 'community_comments', 'forums', 'publication'])
                    ->delete();
            }

            $this->nullOutReferences();

            $deleted['forums'] = $this->deleteAll('forums');
            $deleted['publication'] = $this->deleteAll('publication');

            // Authors exist mainly as publication sources; clear links then wipe authors.
            if (Schema::hasTable('users') && Schema::hasColumn('users', 'author_id')) {
                DB::table('users')->whereNotNull('author_id')->update(['author_id' => null]);
            }
            $deleted['author'] = $this->deleteAll('author');

            if ($includeCommunities) {
                $communityTables = [
                    'user_community_monthly_contributions',
                    'user_badges',
                    'community_invitations',
                    'community_of_practice_members',
                    'community_of_practice_tags',
                    'community_of_practices',
                ];
                foreach ($communityTables as $table) {
                    $deleted[$table] = $this->deleteAll($table);
                }
            }
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        foreach (array_keys($deleted) as $table) {
            if (Schema::hasTable($table)) {
                try {
                    DB::statement('ALTER TABLE `'.$table.'` AUTO_INCREMENT = 1');
                    $resets[] = $table;
                } catch (\Throwable $e) {
                    Log::warning('AUTO_INCREMENT reset failed for '.$table.': '.$e->getMessage());
                }
            }
        }

        return ['tables' => $deleted, 'resets' => $resets];
    }

    protected function deleteAll(string $table): int
    {
        if (! Schema::hasTable($table)) {
            return 0;
        }

        return (int) DB::table($table)->delete();
    }

    protected function nullOutReferences(): void
    {
        if (Schema::hasTable('content_requests') && Schema::hasColumn('content_requests', 'referral_forum_id')) {
            DB::table('content_requests')->whereNotNull('referral_forum_id')->update(['referral_forum_id' => null]);
        }

        if (Schema::hasTable('content_request_referral_targets')) {
            $updates = [];
            if (Schema::hasColumn('content_request_referral_targets', 'referral_forum_id')) {
                $updates['referral_forum_id'] = null;
            }
            if (Schema::hasColumn('content_request_referral_targets', 'community_of_practice_id')) {
                $updates['community_of_practice_id'] = null;
            }
            if ($updates !== []) {
                DB::table('content_request_referral_targets')->update($updates);
            }
        }

        if (Schema::hasTable('events') && Schema::hasColumn('events', 'community_of_practice_id')) {
            DB::table('events')->whereNotNull('community_of_practice_id')->update(['community_of_practice_id' => null]);
        }
    }
}
