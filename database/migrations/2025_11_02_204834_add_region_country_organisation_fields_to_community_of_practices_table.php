<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRegionCountryOrganisationFieldsToCommunityOfPracticesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('community_of_practices', function (Blueprint $table) {
            if (!Schema::hasColumn('community_of_practices', 'region_id')) {
                // Check the actual column type of regions.id first
                $regionIdType = 'unsignedBigInteger';
                if (Schema::hasTable('regions')) {
                    try {
                        $columnInfo = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM regions WHERE Field = 'id'");
                        if (!empty($columnInfo)) {
                            $type = strtolower($columnInfo[0]->Type);
                            // If it's int (not bigint), use unsignedInteger
                            if (strpos($type, 'int') !== false && strpos($type, 'bigint') === false) {
                                $regionIdType = 'unsignedInteger';
                            }
                        }
                    } catch (\Exception $e) {
                        // Default to unsignedBigInteger if we can't check
                    }
                }
                
                if ($regionIdType === 'unsignedInteger') {
                    $table->unsignedInteger('region_id')->nullable()->after('description');
                } else {
                    $table->unsignedBigInteger('region_id')->nullable()->after('description');
                }
            }
            
            if (!Schema::hasColumn('community_of_practices', 'country_id')) {
                // Check the actual column type of country.id first
                $countryIdType = 'unsignedBigInteger';
                if (Schema::hasTable('country')) {
                    try {
                        $columnInfo = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM country WHERE Field = 'id'");
                        if (!empty($columnInfo)) {
                            $type = strtolower($columnInfo[0]->Type);
                            // If it's int (not bigint), use unsignedInteger
                            if (strpos($type, 'int') !== false && strpos($type, 'bigint') === false) {
                                $countryIdType = 'unsignedInteger';
                            }
                        }
                    } catch (\Exception $e) {
                        // Default to unsignedBigInteger if we can't check
                    }
                }
                
                if ($countryIdType === 'unsignedInteger') {
                    $table->unsignedInteger('country_id')->nullable()->after('region_id');
                } else {
                    $table->unsignedBigInteger('country_id')->nullable()->after('region_id');
                }
            }
            
            if (!Schema::hasColumn('community_of_practices', 'organisation')) {
                $table->string('organisation')->nullable()->after('country_id');
            }
            
            if (!Schema::hasColumn('community_of_practices', 'department')) {
                $table->string('department')->nullable()->after('organisation');
            }
            
            if (!Schema::hasColumn('community_of_practices', 'is_public')) {
                $table->boolean('is_public')->default(1)->after('department');
            }
        });
        
        // Add foreign key constraints separately to handle type mismatches gracefully
        if (Schema::hasTable('regions') && Schema::hasColumn('community_of_practices', 'region_id')) {
            try {
                \Illuminate\Support\Facades\DB::statement('ALTER TABLE community_of_practices ADD CONSTRAINT community_of_practices_region_id_foreign FOREIGN KEY (region_id) REFERENCES regions(id) ON DELETE SET NULL');
            } catch (\Exception $e) {
                // Foreign key might already exist or column types don't match
            }
        }
        
        if (Schema::hasTable('country') && Schema::hasColumn('community_of_practices', 'country_id')) {
            try {
                \Illuminate\Support\Facades\DB::statement('ALTER TABLE community_of_practices ADD CONSTRAINT community_of_practices_country_id_foreign FOREIGN KEY (country_id) REFERENCES country(id) ON DELETE SET NULL');
            } catch (\Exception $e) {
                // Foreign key might already exist or column types don't match
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('community_of_practices', function (Blueprint $table) {
            if (Schema::hasColumn('community_of_practices', 'is_public')) {
                $table->dropColumn('is_public');
            }
            
            if (Schema::hasColumn('community_of_practices', 'department')) {
                $table->dropColumn('department');
            }
            
            if (Schema::hasColumn('community_of_practices', 'organisation')) {
                $table->dropColumn('organisation');
            }
            
            if (Schema::hasColumn('community_of_practices', 'country_id')) {
                $table->dropForeign(['country_id']);
                $table->dropColumn('country_id');
            }
            
            if (Schema::hasColumn('community_of_practices', 'region_id')) {
                $table->dropForeign(['region_id']);
                $table->dropColumn('region_id');
            }
        });
    }
}
