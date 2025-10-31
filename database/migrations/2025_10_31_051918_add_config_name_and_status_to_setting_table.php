<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('setting', function (Blueprint $table) {
            // Add config_name column if it doesn't exist
            if (!Schema::hasColumn('setting', 'config_name')) {
                $table->string('config_name', 100)->nullable()->after('id');
            }
            
            // Add status column if it doesn't exist
            if (!Schema::hasColumn('setting', 'status')) {
                $table->enum('status', ['active', 'inactive'])->default('active')->after('config_name');
                
                // Add index for faster queries
                $table->index('status');
            }
        });

        // Migrate existing data: set default config_name and ensure at least one is active
        $existingSettings = DB::table('setting')->get();
        
        if ($existingSettings->count() > 0) {
            // If no config_name exists, set default
            DB::table('setting')
                ->whereNull('config_name')
                ->update(['config_name' => 'Default Configuration']);
            
            // If no active status exists, set the first one (or ID 1) as active
            $hasActive = DB::table('setting')->where('status', 'active')->exists();
            
            if (!$hasActive) {
                // Set the first record (or ID 1) as active
                $firstSetting = DB::table('setting')->orderBy('id')->first();
                if ($firstSetting) {
                    DB::table('setting')
                        ->where('id', $firstSetting->id)
                        ->update(['status' => 'active']);
                }
            }
        } else {
            // If no settings exist, create a default one
            DB::table('setting')->insert([
                'config_name' => 'Default Configuration',
                'status' => 'active',
                'id' => 1
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('setting', function (Blueprint $table) {
            if (Schema::hasColumn('setting', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('setting', 'config_name')) {
                $table->dropColumn('config_name');
            }
        });
    }
};
