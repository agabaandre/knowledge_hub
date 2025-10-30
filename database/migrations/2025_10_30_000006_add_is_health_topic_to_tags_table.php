<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsHealthTopicToTagsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('tags')) {
            Schema::table('tags', function (Blueprint $table) {
                if (!Schema::hasColumn('tags', 'is_health_topic')) {
                    $table->boolean('is_health_topic')->default(true)->after('tag_text');
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('tags')) {
            Schema::table('tags', function (Blueprint $table) {
                if (Schema::hasColumn('tags', 'is_health_topic')) {
                    $table->dropColumn('is_health_topic');
                }
            });
        }
    }
}


