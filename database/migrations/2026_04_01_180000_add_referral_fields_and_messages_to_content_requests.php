<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('content_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('content_requests', 'referral_type')) {
                $table->string('referral_type', 32)->nullable()->after('admin_comments');
            }
            if (! Schema::hasColumn('content_requests', 'referred_to_user_id')) {
                $table->unsignedBigInteger('referred_to_user_id')->nullable()->after('referral_type');
            }
            if (! Schema::hasColumn('content_requests', 'referred_to_community_id')) {
                $table->unsignedBigInteger('referred_to_community_id')->nullable()->after('referred_to_user_id');
            }
            if (! Schema::hasColumn('content_requests', 'referred_at')) {
                $table->timestamp('referred_at')->nullable()->after('referred_to_community_id');
            }
            if (! Schema::hasColumn('content_requests', 'referred_by')) {
                $table->unsignedBigInteger('referred_by')->nullable()->after('referred_at');
            }
            if (! Schema::hasColumn('content_requests', 'referral_notes')) {
                $table->text('referral_notes')->nullable()->after('referred_by');
            }
            if (! Schema::hasColumn('content_requests', 'requestor_track_token')) {
                $table->string('requestor_track_token', 64)->nullable()->unique()->after('referral_notes');
            }
        });

        if (! Schema::hasTable('content_request_referral_messages')) {
            Schema::create('content_request_referral_messages', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('content_request_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->boolean('posted_via_track')->default(false);
                $table->text('body');
                $table->timestamps();

                $table->index('content_request_id');
                $table->index('user_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('content_request_referral_messages');

        Schema::table('content_requests', function (Blueprint $table) {
            foreach ([
                'requestor_track_token',
                'referral_notes',
                'referred_by',
                'referred_at',
                'referred_to_community_id',
                'referred_to_user_id',
                'referral_type',
            ] as $col) {
                if (Schema::hasColumn('content_requests', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
