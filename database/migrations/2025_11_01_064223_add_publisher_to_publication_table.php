<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('publication', function (Blueprint $table) {
            if (!Schema::hasColumn('publication', 'publisher')) {
                $table->string('publisher', 500)->nullable()->after('isbn');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('publication', function (Blueprint $table) {
            if (Schema::hasColumn('publication', 'publisher')) {
                $table->dropColumn('publisher');
            }
        });
    }
};
