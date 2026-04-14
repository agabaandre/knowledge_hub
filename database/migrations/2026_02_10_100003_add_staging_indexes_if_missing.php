<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('publications_staging')) {
            return;
        }
        try {
            Schema::table('publications_staging', function (Blueprint $table) {
                $table->index('rss_feed_id');
            });
        } catch (\Throwable $e) {
            if (strpos($e->getMessage(), 'Duplicate') === false) {
                throw $e;
            }
        }
        try {
            Schema::table('publications_staging', function (Blueprint $table) {
                $table->index('processed_status');
            });
        } catch (\Throwable $e) {
            if (strpos($e->getMessage(), 'Duplicate') === false) {
                throw $e;
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('publications_staging')) {
            return;
        }
        Schema::table('publications_staging', function (Blueprint $table) {
            $table->dropIndex(['rss_feed_id']);
            $table->dropIndex(['processed_status']);
        });
    }
};
