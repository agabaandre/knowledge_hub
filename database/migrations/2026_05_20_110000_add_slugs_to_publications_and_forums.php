<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('publication') && ! Schema::hasColumn('publication', 'slug')) {
            Schema::table('publication', function (Blueprint $table) {
                $table->string('slug', 191)->nullable()->after('title');
                $table->unique('slug', 'publication_slug_unique');
            });
        }

        if (Schema::hasTable('forums') && ! Schema::hasColumn('forums', 'slug')) {
            Schema::table('forums', function (Blueprint $table) {
                $table->string('slug', 191)->nullable()->after('forum_title');
                $table->unique('slug', 'forums_slug_unique');
            });
        }

        if (Schema::hasTable('setting') && ! Schema::hasColumn('setting', 'use_seo_friendly_urls')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->boolean('use_seo_friendly_urls')->default(true);
            });
        }

        if (Schema::hasTable('setting_key_groups') && Schema::hasColumn('setting', 'use_seo_friendly_urls')) {
            DB::table('setting_key_groups')->insertOrIgnore([
                'setting_key' => 'use_seo_friendly_urls',
                'group_name' => 'General',
                'subgroup_name' => 'SEO',
                'sort_order' => 22,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('publication') && Schema::hasColumn('publication', 'slug')) {
            Schema::table('publication', function (Blueprint $table) {
                $table->dropUnique('publication_slug_unique');
                $table->dropColumn('slug');
            });
        }

        if (Schema::hasTable('forums') && Schema::hasColumn('forums', 'slug')) {
            Schema::table('forums', function (Blueprint $table) {
                $table->dropUnique('forums_slug_unique');
                $table->dropColumn('slug');
            });
        }

        if (Schema::hasTable('setting') && Schema::hasColumn('setting', 'use_seo_friendly_urls')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->dropColumn('use_seo_friendly_urls');
            });
        }
    }
};
