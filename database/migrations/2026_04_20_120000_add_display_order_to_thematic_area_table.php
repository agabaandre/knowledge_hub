<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('thematic_area', function (Blueprint $table) {
            if (! Schema::hasColumn('thematic_area', 'display_order')) {
                $table->unsignedInteger('display_order')->default(0)->after('icon');
            }
        });
    }

    public function down(): void
    {
        Schema::table('thematic_area', function (Blueprint $table) {
            if (Schema::hasColumn('thematic_area', 'display_order')) {
                $table->dropColumn('display_order');
            }
        });
    }
};

