<?php

use App\Models\CommunityOfPractice;
use App\Models\Tag;
use App\Support\SeoSlugger;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('tags', 'slug')) {
            Tag::query()
                ->where(function ($q) {
                    $q->whereNull('slug')->orWhere('slug', '');
                })
                ->orderBy('id')
                ->chunkById(200, function ($rows) {
                    foreach ($rows as $tag) {
                        $tag->slug = SeoSlugger::forTag(
                            (string) ($tag->tag_text ?? ''),
                            (int) $tag->id
                        );
                        $tag->saveQuietly();
                    }
                });
        }

        if (Schema::hasColumn('community_of_practices', 'slug')) {
            CommunityOfPractice::query()
                ->where(function ($q) {
                    $q->whereNull('slug')->orWhere('slug', '');
                })
                ->orderBy('id')
                ->chunkById(200, function ($rows) {
                    foreach ($rows as $community) {
                        $community->slug = SeoSlugger::forCommunity(
                            (string) ($community->community_name ?? ''),
                            (int) $community->id
                        );
                        $community->saveQuietly();
                    }
                });
        }
    }

    public function down(): void
    {
        // Slugs are regenerated from names if needed; no destructive rollback.
    }
};
