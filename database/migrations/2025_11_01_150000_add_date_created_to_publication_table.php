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
            // Add date_created column if it doesn't exist
            // Note: created_at already exists, but we'll add date_created for explicit date tracking
            if (!Schema::hasColumn('publication', 'date_created')) {
                $table->date('date_created')->nullable()->after('created_at');
            }
        });
        
        // Populate date_created from created_at for existing records
        \DB::statement('UPDATE publication SET date_created = DATE(created_at) WHERE date_created IS NULL AND created_at IS NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('publication', function (Blueprint $table) {
            if (Schema::hasColumn('publication', 'date_created')) {
                $table->dropColumn('date_created');
            }
        });
    }
};

