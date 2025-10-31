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
        Schema::table('setting', function (Blueprint $table) {
            // Publication form settings
            if (!Schema::hasColumn('setting', 'publication_min_words')) {
                $table->integer('publication_min_words')->default(150)->after('show_quiz');
            }
            
            // JSON field to store required fields configuration
            if (!Schema::hasColumn('setting', 'publication_required_fields')) {
                $table->json('publication_required_fields')->nullable()->after('publication_min_words');
            }
        });
        
        // Set default required fields if the column was just added
        if (Schema::hasColumn('setting', 'publication_required_fields')) {
            $defaultRequiredFields = [
                'title' => true,
                'description' => true,
                'associated_authors' => true,
                'tags' => true,
                'theme' => true,
                'sub_theme' => true,
                'data_category_id' => true,
                'year_published' => false,
                'author' => false,
                'associated_tags' => false,
                'doi' => false,
                'issn' => false,
                'isbn' => false,
                'license_id' => false,
                'copyright_info' => false,
                'funder' => false,
                'journal_name' => false,
                'journal_volume' => false,
                'journal_issue' => false,
                'journal_pages' => false,
            ];
            
            \DB::table('setting')
                ->whereNull('publication_required_fields')
                ->update(['publication_required_fields' => json_encode($defaultRequiredFields)]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('setting', function (Blueprint $table) {
            if (Schema::hasColumn('setting', 'publication_required_fields')) {
                $table->dropColumn('publication_required_fields');
            }
            if (Schema::hasColumn('setting', 'publication_min_words')) {
                $table->dropColumn('publication_min_words');
            }
        });
    }
};

