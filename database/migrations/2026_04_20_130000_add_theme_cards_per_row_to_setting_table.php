<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('setting')) {
            Schema::table('setting', function (Blueprint $t) {
                if (! Schema::hasColumn('setting', 'theme_cards_per_row')) {
                    $t->unsignedTinyInteger('theme_cards_per_row')
                        ->default(4)
                        ->after('theme_card_opacity');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('setting')) {
            Schema::table('setting', function (Blueprint $t) {
                if (Schema::hasColumn('setting', 'theme_cards_per_row')) {
                    $t->dropColumn('theme_cards_per_row');
                }
            });
        }
    }
};

