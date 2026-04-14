<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('publication', function (Blueprint $table) {
            $table->boolean('also_public_on_hub')->default(0)->after('show_disclaimer');
        });

        Schema::table('forums', function (Blueprint $table) {
            $table->boolean('also_public_on_hub')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('publication', function (Blueprint $table) {
            $table->dropColumn('also_public_on_hub');
        });

        Schema::table('forums', function (Blueprint $table) {
            $table->dropColumn('also_public_on_hub');
        });
    }
};
