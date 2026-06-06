<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPublicAvailabilityToPublicationsAndForums extends Migration
{
    public function up()
    {
        if (Schema::hasTable('publication') && ! Schema::hasColumn('publication', 'public_availability')) {
            Schema::table('publication', function (Blueprint $table) {
                $table->boolean('public_availability')->default(true)->after('also_public_on_hub');
            });
        }

        if (Schema::hasTable('forums') && ! Schema::hasColumn('forums', 'public_availability')) {
            Schema::table('forums', function (Blueprint $table) {
                $table->boolean('public_availability')->default(true)->after('also_public_on_hub');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('publication') && Schema::hasColumn('publication', 'public_availability')) {
            Schema::table('publication', function (Blueprint $table) {
                $table->dropColumn('public_availability');
            });
        }

        if (Schema::hasTable('forums') && Schema::hasColumn('forums', 'public_availability')) {
            Schema::table('forums', function (Blueprint $table) {
                $table->dropColumn('public_availability');
            });
        }
    }
}
