<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAuthorsAndCountryPublicationsPaginationModes extends Migration
{
    public function up(): void
    {
        Schema::table('setting', function (Blueprint $table) {
            $after = Schema::hasColumn('setting', 'home_events_pagination_mode')
                ? 'home_events_pagination_mode'
                : (Schema::hasColumn('setting', 'communities_pagination_mode')
                    ? 'communities_pagination_mode'
                    : 'enable_ai_search');

            if (! Schema::hasColumn('setting', 'authors_pagination_mode')) {
                $table->string('authors_pagination_mode', 32)->default('infinite_scroll')->after($after);
                $after = 'authors_pagination_mode';
            }

            if (! Schema::hasColumn('setting', 'country_publications_pagination_mode')) {
                $table->string('country_publications_pagination_mode', 32)->default('infinite_scroll')->after($after);
            }
        });
    }

    public function down(): void
    {
        Schema::table('setting', function (Blueprint $table) {
            foreach (['authors_pagination_mode', 'country_publications_pagination_mode'] as $column) {
                if (Schema::hasColumn('setting', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
