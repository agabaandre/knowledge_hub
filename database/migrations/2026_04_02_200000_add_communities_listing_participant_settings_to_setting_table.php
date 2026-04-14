<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Communities directory cards: show participant strip and how many faces (1–24).
     */
    public function up(): void
    {
        if (! Schema::hasColumn('setting', 'communities_listing_show_participants')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->boolean('communities_listing_show_participants')->default(true);
            });
        }
        if (! Schema::hasColumn('setting', 'communities_listing_max_faces')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->unsignedTinyInteger('communities_listing_max_faces')->default(8);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('setting', 'communities_listing_show_participants')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->dropColumn('communities_listing_show_participants');
            });
        }
        if (Schema::hasColumn('setting', 'communities_listing_max_faces')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->dropColumn('communities_listing_max_faces');
            });
        }
    }
};
