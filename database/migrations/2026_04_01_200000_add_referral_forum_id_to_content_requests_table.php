<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('content_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('content_requests', 'referral_forum_id')) {
                $table->unsignedBigInteger('referral_forum_id')->nullable()->after('requestor_track_token');
                $table->index('referral_forum_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('content_requests', function (Blueprint $table) {
            if (Schema::hasColumn('content_requests', 'referral_forum_id')) {
                $table->dropColumn('referral_forum_id');
            }
        });
    }
};
