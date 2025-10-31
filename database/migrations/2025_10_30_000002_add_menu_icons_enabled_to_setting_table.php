<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMenuIconsEnabledToSettingTable extends Migration
{
    public function up()
    {
        Schema::table('setting', function (Blueprint $table) {
            if (!Schema::hasColumn('setting', 'menu_icons_enabled')) {
                $table->boolean('menu_icons_enabled')->default(false)->after('gradient_end_color');
            }
        });
    }

    public function down()
    {
        Schema::table('setting', function (Blueprint $table) {
            if (Schema::hasColumn('setting', 'menu_icons_enabled')) {
                $table->dropColumn('menu_icons_enabled');
            }
        });
    }
}


