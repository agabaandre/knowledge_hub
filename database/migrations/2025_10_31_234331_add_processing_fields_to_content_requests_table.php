<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProcessingFieldsToContentRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('content_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('content_requests', 'processed_at')) {
                $table->timestamp('processed_at')->nullable()->after('updated_at');
            }
            if (!Schema::hasColumn('content_requests', 'processed_by')) {
                $table->foreignId('processed_by')->nullable()->after('processed_at')->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('content_requests', 'content_links')) {
                $table->text('content_links')->nullable()->after('processed_by');
            }
            if (!Schema::hasColumn('content_requests', 'admin_comments')) {
                $table->text('admin_comments')->nullable()->after('content_links');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('content_requests', function (Blueprint $table) {
            if (Schema::hasColumn('content_requests', 'admin_comments')) {
                $table->dropColumn('admin_comments');
            }
            if (Schema::hasColumn('content_requests', 'content_links')) {
                $table->dropColumn('content_links');
            }
            if (Schema::hasColumn('content_requests', 'processed_by')) {
                $table->dropForeign(['processed_by']);
                $table->dropColumn('processed_by');
            }
            if (Schema::hasColumn('content_requests', 'processed_at')) {
                $table->dropColumn('processed_at');
            }
        });
    }
}
