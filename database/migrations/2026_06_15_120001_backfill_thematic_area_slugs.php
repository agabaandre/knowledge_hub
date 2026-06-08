<?php

use App\Models\SubThemeticArea;
use App\Models\ThemeticArea;
use App\Support\SeoSlugger;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('thematic_area', 'slug')) {
            ThemeticArea::query()
                ->where(function ($q) {
                    $q->whereNull('slug')->orWhere('slug', '');
                })
                ->orderBy('id')
                ->chunkById(200, function ($rows) {
                    foreach ($rows as $theme) {
                        $theme->slug = SeoSlugger::forThematicArea(
                            (string) ($theme->description ?? ''),
                            (int) $theme->id
                        );
                        $theme->saveQuietly();
                    }
                });
        }

        if (Schema::hasColumn('sub_thematic_area', 'slug')) {
            SubThemeticArea::query()
                ->where(function ($q) {
                    $q->whereNull('slug')->orWhere('slug', '');
                })
                ->orderBy('id')
                ->chunkById(200, function ($rows) {
                    foreach ($rows as $subTheme) {
                        $subTheme->slug = SeoSlugger::forSubThematicArea(
                            (string) ($subTheme->description ?? ''),
                            (int) $subTheme->id
                        );
                        $subTheme->saveQuietly();
                    }
                });
        }
    }

    public function down(): void
    {
        // Slugs are regenerated from names if needed; no destructive rollback.
    }
};
