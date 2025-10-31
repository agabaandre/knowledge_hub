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
            // DOI, ISSN, ISBN
            if (!Schema::hasColumn('publication', 'doi')) {
                $table->string('doi', 255)->nullable()->after('citation_authors');
            }
            if (!Schema::hasColumn('publication', 'issn')) {
                $table->string('issn', 50)->nullable()->after('doi');
            }
            if (!Schema::hasColumn('publication', 'isbn')) {
                $table->string('isbn', 50)->nullable()->after('issn');
            }
            
            // License/Copyright
            // Only add foreign key if licenses table exists
            if (!Schema::hasColumn('publication', 'license_id')) {
                if (Schema::hasTable('licenses')) {
                    $table->foreignId('license_id')->nullable()->after('isbn');
                    $table->foreign('license_id')->references('id')->on('licenses')->onDelete('set null');
                } else {
                    // If licenses table doesn't exist, just add the column without foreign key
                    $table->unsignedBigInteger('license_id')->nullable()->after('isbn');
                }
            }
            if (!Schema::hasColumn('publication', 'copyright_info')) {
                $table->text('copyright_info')->nullable()->after('license_id');
            }
            
            // Funder
            if (!Schema::hasColumn('publication', 'funder')) {
                $table->string('funder', 500)->nullable()->after('copyright_info');
            }
            
            // Journal fields (only for Journal Articles)
            if (!Schema::hasColumn('publication', 'journal_name')) {
                $table->string('journal_name', 500)->nullable()->after('funder');
            }
            if (!Schema::hasColumn('publication', 'journal_volume')) {
                $table->string('journal_volume', 50)->nullable()->after('journal_name');
            }
            if (!Schema::hasColumn('publication', 'journal_issue')) {
                $table->string('journal_issue', 50)->nullable()->after('journal_volume');
            }
            if (!Schema::hasColumn('publication', 'journal_pages')) {
                $table->string('journal_pages', 50)->nullable()->after('journal_issue');
            }
        });
        
        // If licenses table exists and license_id column was added without foreign key, add it now
        if (Schema::hasTable('licenses') && Schema::hasColumn('publication', 'license_id')) {
            // Check if foreign key already exists
            $foreignKeys = Schema::getConnection()->getDoctrineSchemaManager()->listTableForeignKeys('publication');
            $hasForeignKey = false;
            foreach ($foreignKeys as $foreignKey) {
                if ($foreignKey->getName() === 'publication_license_id_foreign' || 
                    in_array('license_id', $foreignKey->getLocalColumns())) {
                    $hasForeignKey = true;
                    break;
                }
            }
            
            if (!$hasForeignKey) {
                Schema::table('publication', function (Blueprint $table) {
                    $table->foreign('license_id')->references('id')->on('licenses')->onDelete('set null');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('publication', function (Blueprint $table) {
            if (Schema::hasColumn('publication', 'journal_pages')) {
                $table->dropColumn('journal_pages');
            }
            if (Schema::hasColumn('publication', 'journal_issue')) {
                $table->dropColumn('journal_issue');
            }
            if (Schema::hasColumn('publication', 'journal_volume')) {
                $table->dropColumn('journal_volume');
            }
            if (Schema::hasColumn('publication', 'journal_name')) {
                $table->dropColumn('journal_name');
            }
            if (Schema::hasColumn('publication', 'funder')) {
                $table->dropColumn('funder');
            }
            if (Schema::hasColumn('publication', 'copyright_info')) {
                $table->dropColumn('copyright_info');
            }
            if (Schema::hasColumn('publication', 'license_id')) {
                // Try to drop foreign key if it exists
                try {
                    $table->dropForeign(['license_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist, continue
                }
                $table->dropColumn('license_id');
            }
            if (Schema::hasColumn('publication', 'isbn')) {
                $table->dropColumn('isbn');
            }
            if (Schema::hasColumn('publication', 'issn')) {
                $table->dropColumn('issn');
            }
            if (Schema::hasColumn('publication', 'doi')) {
                $table->dropColumn('doi');
            }
        });
    }
};

