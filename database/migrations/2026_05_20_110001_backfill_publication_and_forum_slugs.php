<?php

use App\Models\Forum;
use App\Models\Publication;
use App\Support\SeoSlugger;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('publication', 'slug')) {
            Publication::query()
                ->where('is_version', 0)
                ->where(function ($q) {
                    $q->whereNull('slug')->orWhere('slug', '');
                })
                ->orderBy('id')
                ->chunkById(200, function ($rows) {
                    foreach ($rows as $publication) {
                        $publication->slug = SeoSlugger::forPublication(
                            (string) ($publication->title ?? ''),
                            (int) $publication->id
                        );
                        $publication->saveQuietly();
                    }
                });
        }

        if (Schema::hasColumn('forums', 'slug')) {
            Forum::query()
                ->where(function ($q) {
                    $q->whereNull('slug')->orWhere('slug', '');
                })
                ->orderBy('id')
                ->chunkById(200, function ($rows) {
                    foreach ($rows as $forum) {
                        $forum->slug = SeoSlugger::forForum(
                            (string) ($forum->forum_title ?? ''),
                            (int) $forum->id
                        );
                        $forum->saveQuietly();
                    }
                });
        }
    }

    public function down(): void
    {
        // Slugs are regenerated from titles if needed; no destructive rollback.
    }
};
