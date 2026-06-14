<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGenderToUsersTable extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('users', 'gender')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('gender', 16)->nullable()->default(null)->after('country_id');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('users', 'gender')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('gender');
        });
    }
}
