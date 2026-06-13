<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddListingPaginationModesToSettingTable extends Migration
{
    public function up(): void
    {
        Schema::table('setting', function (Blueprint $table) {
            $after = Schema::hasColumn('setting', 'communities_pagination_mode')
                ? 'communities_pagination_mode'
                : (Schema::hasColumn('setting', 'forums_pagination_mode')
                    ? 'forums_pagination_mode'
                    : 'enable_ai_search');

            if (! Schema::hasColumn('setting', 'courses_pagination_mode')) {
                $table->string('courses_pagination_mode', 32)->default('infinite_scroll')->after($after);
                $after = 'courses_pagination_mode';
            }

            if (! Schema::hasColumn('setting', 'faqs_pagination_mode')) {
                $table->string('faqs_pagination_mode', 32)->default('infinite_scroll')->after($after);
                $after = 'faqs_pagination_mode';
            }

            if (! Schema::hasColumn('setting', 'health_topics_pagination_mode')) {
                $table->string('health_topics_pagination_mode', 32)->default('infinite_scroll')->after($after);
                $after = 'health_topics_pagination_mode';
            }

            if (! Schema::hasColumn('setting', 'home_events_pagination_mode')) {
                $table->string('home_events_pagination_mode', 32)->default('infinite_scroll')->after($after);
            }
        });
    }

    public function down(): void
    {
        Schema::table('setting', function (Blueprint $table) {
            foreach (['courses_pagination_mode', 'faqs_pagination_mode', 'health_topics_pagination_mode', 'home_events_pagination_mode'] as $column) {
                if (Schema::hasColumn('setting', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
