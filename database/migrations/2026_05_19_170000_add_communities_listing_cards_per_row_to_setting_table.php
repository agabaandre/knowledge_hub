<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('setting') && ! Schema::hasColumn('setting', 'communities_listing_cards_per_row')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->unsignedTinyInteger('communities_listing_cards_per_row')
                    ->default(2)
                    ->after('communities_listing_max_faces');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('setting') && Schema::hasColumn('setting', 'communities_listing_cards_per_row')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->dropColumn('communities_listing_cards_per_row');
            });
        }
    }
};
