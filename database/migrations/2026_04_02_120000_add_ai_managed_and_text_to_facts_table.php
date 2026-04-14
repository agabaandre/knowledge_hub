<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facts', function (Blueprint $table) {
            $table->boolean('is_ai_managed')->default(false)->after('resource_id');
        });

        Schema::table('facts', function (Blueprint $table) {
            $table->text('fact_summary')->change();
            $table->text('fact_description')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('facts', function (Blueprint $table) {
            $table->dropColumn('is_ai_managed');
        });

        Schema::table('facts', function (Blueprint $table) {
            $table->string('fact_summary')->change();
            $table->string('fact_description')->nullable()->change();
        });
    }
};
