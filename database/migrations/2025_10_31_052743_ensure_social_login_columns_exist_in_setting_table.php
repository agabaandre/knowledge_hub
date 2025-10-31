<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration ensures all social login columns exist in the setting table.
     * Safe to rerun - will only add columns that don't exist.
     */
    public function up(): void
    {
        Schema::table('setting', function (Blueprint $table) {
            // Add Microsoft login column if it doesn't exist
            if (!Schema::hasColumn('setting', 'enable_microsoft_login')) {
                // Try to find show_quiz column for positioning
                if (Schema::hasColumn('setting', 'show_quiz')) {
                    $table->boolean('enable_microsoft_login')->default(true)->after('show_quiz');
                } else {
                    // Fallback: add at the end
                    $table->boolean('enable_microsoft_login')->default(true);
                }
            }
            
            // Add Google login column if it doesn't exist
            if (!Schema::hasColumn('setting', 'enable_google_login')) {
                if (Schema::hasColumn('setting', 'enable_microsoft_login')) {
                    $table->boolean('enable_google_login')->default(true)->after('enable_microsoft_login');
                } else {
                    $table->boolean('enable_google_login')->default(true);
                }
            }
            
            // Add LinkedIn login column if it doesn't exist
            if (!Schema::hasColumn('setting', 'enable_linkedin_login')) {
                if (Schema::hasColumn('setting', 'enable_google_login')) {
                    $table->boolean('enable_linkedin_login')->default(true)->after('enable_google_login');
                } else {
                    $table->boolean('enable_linkedin_login')->default(true);
                }
            }
        });

        // Set default values for existing records if columns were just added
        // Check which columns exist after adding them
        $hasMicrosoft = Schema::hasColumn('setting', 'enable_microsoft_login');
        $hasGoogle = Schema::hasColumn('setting', 'enable_google_login');
        $hasLinkedIn = Schema::hasColumn('setting', 'enable_linkedin_login');

        // Update records that have NULL values for any of these columns
        if ($hasMicrosoft) {
            DB::table('setting')
                ->whereNull('enable_microsoft_login')
                ->update(['enable_microsoft_login' => 1]);
        }
        
        if ($hasGoogle) {
            DB::table('setting')
                ->whereNull('enable_google_login')
                ->update(['enable_google_login' => 1]);
        }
        
        if ($hasLinkedIn) {
            DB::table('setting')
                ->whereNull('enable_linkedin_login')
                ->update(['enable_linkedin_login' => 1]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('setting', function (Blueprint $table) {
            if (Schema::hasColumn('setting', 'enable_linkedin_login')) {
                $table->dropColumn('enable_linkedin_login');
            }
            if (Schema::hasColumn('setting', 'enable_google_login')) {
                $table->dropColumn('enable_google_login');
            }
            if (Schema::hasColumn('setting', 'enable_microsoft_login')) {
                $table->dropColumn('enable_microsoft_login');
            }
        });
    }
};
