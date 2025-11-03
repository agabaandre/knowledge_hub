<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommunityOfPracticeTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('community_of_practice_tags')) {
            // Check column types dynamically
            $tagsIdType = 'integer';
            $copIdType = 'unsignedBigInteger';
            
            if (Schema::hasTable('tags')) {
                try {
                    $columnInfo = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM tags WHERE Field = 'id'");
                    if (!empty($columnInfo)) {
                        $type = strtolower($columnInfo[0]->Type);
                        if (strpos($type, 'bigint') !== false) {
                            $tagsIdType = 'unsignedBigInteger';
                        } else {
                            $tagsIdType = 'integer'; // tags.id is signed integer based on event_tags migration
                        }
                    }
                } catch (\Exception $e) {
                    // Default to integer if we can't check
                }
            }
            
            if (Schema::hasTable('community_of_practices')) {
                try {
                    $columnInfo = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM community_of_practices WHERE Field = 'id'");
                    if (!empty($columnInfo)) {
                        $type = strtolower($columnInfo[0]->Type);
                        if (strpos($type, 'bigint') !== false) {
                            $copIdType = 'unsignedBigInteger';
                        } else {
                            $copIdType = 'unsignedInteger';
                        }
                    }
                } catch (\Exception $e) {
                    // Default to unsignedBigInteger if we can't check
                }
            }
            
            Schema::create('community_of_practice_tags', function (Blueprint $table) use ($tagsIdType, $copIdType) {
                $table->id();
                
                // Use matching type for foreign keys
                if ($copIdType === 'unsignedInteger') {
                    $table->unsignedInteger('community_of_practice_id');
                } else {
                    $table->unsignedBigInteger('community_of_practice_id');
                }
                
                if ($tagsIdType === 'unsignedBigInteger') {
                    $table->unsignedBigInteger('tag_id');
                } else {
                    $table->integer('tag_id'); // tags.id is signed integer
                }
                
                $table->timestamps();
            });
            
            // Add foreign keys separately to handle potential mismatches
            try {
                \Illuminate\Support\Facades\DB::statement('
                    ALTER TABLE community_of_practice_tags 
                    ADD CONSTRAINT community_of_practice_tags_community_of_practice_id_foreign 
                    FOREIGN KEY (community_of_practice_id) REFERENCES community_of_practices(id) ON DELETE CASCADE
                ');
            } catch (\Exception $e) {
                // Foreign key might already exist - skip
            }
            
            try {
                \Illuminate\Support\Facades\DB::statement('
                    ALTER TABLE community_of_practice_tags 
                    ADD CONSTRAINT community_of_practice_tags_tag_id_foreign 
                    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
                ');
            } catch (\Exception $e) {
                // Foreign key might already exist - skip
            }
            
            // Add unique constraint
            try {
                \Illuminate\Support\Facades\DB::statement('
                    ALTER TABLE community_of_practice_tags 
                    ADD UNIQUE KEY cop_tag_unique (community_of_practice_id, tag_id)
                ');
            } catch (\Exception $e) {
                // Unique constraint might already exist - skip
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
        Schema::dropIfExists('community_of_practice_tags');
    }
}
