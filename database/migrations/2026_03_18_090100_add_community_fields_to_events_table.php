<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommunityFieldsToEventsTable extends Migration
{
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'community_of_practice_id')) {
                $table->unsignedBigInteger('community_of_practice_id')->nullable()->after('country_id');
            }
            if (!Schema::hasColumn('events', 'event_category')) {
                $table->string('event_category', 100)->nullable()->after('community_of_practice_id');
            }
        });
    }

    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'event_category')) {
                $table->dropColumn('event_category');
            }
            if (Schema::hasColumn('events', 'community_of_practice_id')) {
                $table->dropColumn('community_of_practice_id');
            }
        });
    }
}
