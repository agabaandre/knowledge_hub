<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdminAndActiveToCommunityMembersTable extends Migration
{
    public function up()
    {
        Schema::table('community_of_practice_members', function (Blueprint $table) {
            if (!Schema::hasColumn('community_of_practice_members', 'is_admin')) {
                $table->boolean('is_admin')->default(false)->after('is_approved');
            }
            if (!Schema::hasColumn('community_of_practice_members', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('is_admin');
            }
        });
    }

    public function down()
    {
        Schema::table('community_of_practice_members', function (Blueprint $table) {
            if (Schema::hasColumn('community_of_practice_members', 'is_active')) {
                $table->dropColumn('is_active');
            }
            if (Schema::hasColumn('community_of_practice_members', 'is_admin')) {
                $table->dropColumn('is_admin');
            }
        });
    }
}
