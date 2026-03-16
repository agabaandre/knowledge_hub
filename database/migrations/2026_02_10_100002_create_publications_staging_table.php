<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publications_staging', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rss_feed_id');
            $table->string('rss_guid', 500)->nullable();
            $table->string('rss_link', 2048)->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->string('processed_status', 20)->default('pending'); // pending, approved, rejected
            $table->text('rejection_reason')->nullable();
            $table->unsignedBigInteger('publication_id')->nullable(); // set when approved (created publication id)
            $table->json('openai_metadata')->nullable(); // raw OpenAI response for fallback/debug

            // Mirror publication table columns (same as publication)
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('publication', 2048)->nullable();
            $table->unsignedBigInteger('author_id')->nullable();
            $table->string('associated_authors')->nullable();
            $table->string('author_affiliation')->nullable();
            $table->unsignedBigInteger('sub_thematic_area_id')->nullable();
            $table->unsignedBigInteger('publication_catgory_id')->nullable();
            $table->unsignedBigInteger('data_category_id')->nullable();
            $table->unsignedBigInteger('file_type_id')->nullable();
            $table->unsignedBigInteger('geographical_coverage_id')->nullable();
            $table->unsignedBigInteger('geographical_scope_id')->nullable();
            $table->integer('year_published')->nullable();
            $table->string('doi', 100)->nullable();
            $table->string('issn', 50)->nullable();
            $table->string('isbn', 50)->nullable();
            $table->string('publisher')->nullable();
            $table->unsignedBigInteger('license_id')->nullable();
            $table->text('copyright_info')->nullable();
            $table->string('funder')->nullable();
            $table->string('journal_name')->nullable();
            $table->string('journal_volume', 50)->nullable();
            $table->string('journal_issue', 50)->nullable();
            $table->string('journal_pages', 50)->nullable();
            $table->string('cover')->nullable();
            $table->tinyInteger('cover_is_exteranl')->default(0)->nullable();
            $table->string('citation_authors')->nullable();
            $table->string('citation_link', 2048)->nullable();
            $table->string('is_active', 20)->default('Active')->nullable();
            $table->tinyInteger('is_admin_only_access')->default(0)->nullable();
            $table->tinyInteger('is_approved')->default(0)->nullable();
            $table->tinyInteger('is_rejected')->default(0)->nullable();
            $table->tinyInteger('is_embedded')->default(0)->nullable();
            $table->tinyInteger('is_featured')->default(0)->nullable();
            $table->tinyInteger('is_version')->default(0)->nullable();
            $table->tinyInteger('is_video')->default(0)->nullable();
            $table->tinyInteger('show_disclaimer')->default(0)->nullable();
            $table->tinyInteger('is_default_in_category')->default(0)->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->integer('version_no')->nullable();
            $table->unsignedInteger('visits')->default(0)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->date('date_created')->nullable();
            $table->timestamps();

            $table->index(['rss_feed_id', 'rss_guid']);
            $table->index('rss_feed_id');
            $table->index('processed_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publications_staging');
    }
};
