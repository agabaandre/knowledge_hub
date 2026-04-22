<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('setting_key_groups')) {
            Schema::create('setting_key_groups', function (Blueprint $table) {
                $table->id();
                $table->string('setting_key', 120)->unique();
                $table->string('group_name', 80);
                $table->string('subgroup_name', 80)->nullable();
                $table->unsignedInteger('sort_order')->default(100);
                $table->timestamps();
            });
        }

        $rows = [
            ['site_name', 'General', 'Brand & Identity', 10],
            ['title', 'General', 'Brand & Identity', 11],
            ['slogan', 'General', 'Brand & Identity', 12],
            ['site_description', 'General', 'SEO', 20],
            ['seo_keywords', 'General', 'SEO', 21],

            ['logo', 'Appearance', 'Brand Assets', 30],
            ['favicon', 'Appearance', 'Brand Assets', 31],
            ['spotlight_banner', 'Appearance', 'Homepage Hero', 32],
            ['gradient_start_color', 'Appearance', 'Homepage Hero', 33],
            ['gradient_end_color', 'Appearance', 'Homepage Hero', 34],
            ['spotlight_overlay_color', 'Appearance', 'Homepage Hero', 35],
            ['spotlight_overlay_opacity', 'Appearance', 'Homepage Hero', 36],
            ['primary_color', 'Appearance', 'Theme Colors', 40],
            ['secondary_color', 'Appearance', 'Theme Colors', 41],
            ['primary_text_color', 'Appearance', 'Theme Colors', 42],
            ['links_active_color', 'Appearance', 'Theme Colors', 43],
            ['icon_font_color', 'Appearance', 'Theme Colors', 44],

            ['address', 'Contact', 'Organization', 50],
            ['phone', 'Contact', 'Organization', 51],
            ['email', 'Contact', 'Organization', 52],
            ['timezone', 'Contact', 'Localization', 53],

            ['analytics_script', 'Advanced', 'Tracking', 60],
            ['content_disclaimer', 'Advanced', 'Content Governance', 61],
            ['publication_min_words', 'Advanced', 'Publication Workflow', 62],
            ['publication_required_fields', 'Advanced', 'Publication Workflow', 63],
            ['enable_version_submission', 'Advanced', 'Publication Workflow', 64],
            ['enable_microsoft_login', 'Advanced', 'Authentication', 70],
            ['enable_google_login', 'Advanced', 'Authentication', 71],
            ['enable_linkedin_login', 'Advanced', 'Authentication', 72],
            ['allow_email_password_accounts_social_login', 'Advanced', 'Authentication', 73],
        ];

        foreach ($rows as [$key, $group, $subgroup, $order]) {
            DB::table('setting_key_groups')->updateOrInsert(
                ['setting_key' => $key],
                [
                    'group_name' => $group,
                    'subgroup_name' => $subgroup,
                    'sort_order' => $order,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('setting_key_groups')) {
            Schema::dropIfExists('setting_key_groups');
        }
    }
};
