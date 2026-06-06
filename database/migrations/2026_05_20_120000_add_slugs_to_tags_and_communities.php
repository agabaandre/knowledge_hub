<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tags') && ! Schema::hasColumn('tags', 'slug')) {
            Schema::table('tags', function (Blueprint $table) {
                $table->string('slug', 191)->nullable()->after('tag_text');
                $table->unique('slug', 'tags_slug_unique');
            });
        }

        if (Schema::hasTable('community_of_practices') && ! Schema::hasColumn('community_of_practices', 'slug')) {
            Schema::table('community_of_practices', function (Blueprint $table) {
                $table->string('slug', 191)->nullable()->after('community_name');
                $table->unique('slug', 'community_of_practices_slug_unique');
            });
        }

        if (Schema::hasTable('setting_key_groups') && Schema::hasColumn('tags', 'slug')) {
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
        if (Schema::hasTable('tags') && Schema::hasColumn('tags', 'slug')) {
            Schema::table('tags', function (Blueprint $table) {
                $table->dropUnique('tags_slug_unique');
                $table->dropColumn('slug');
            });
        }

        if (Schema::hasTable('community_of_practices') && Schema::hasColumn('community_of_practices', 'slug')) {
            Schema::table('community_of_practices', function (Blueprint $table) {
                $table->dropUnique('community_of_practices_slug_unique');
                $table->dropColumn('slug');
            });
        }
    }
};
