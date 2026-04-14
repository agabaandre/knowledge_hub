<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('content_request_referral_targets')) {
            Schema::create('content_request_referral_targets', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('content_request_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('community_of_practice_id')->nullable();
                $table->unsignedBigInteger('referral_forum_id')->nullable();
                $table->timestamps();

                $table->index('content_request_id', 'crrt_content_request_id');
                $table->index('referral_forum_id', 'crrt_referral_forum_id');
                $table->index(['content_request_id', 'user_id'], 'crrt_cr_user');
                $table->index(['content_request_id', 'community_of_practice_id'], 'crrt_cr_cop');
            });
        }

        // Backfill from legacy single user / community columns
        if (Schema::hasTable('content_requests') && Schema::hasTable('content_request_referral_targets')) {
            $rows = DB::table('content_requests')
                ->whereNotNull('referred_at')
                ->get(['id', 'referred_to_user_id', 'referred_to_community_id', 'referral_forum_id']);

            foreach ($rows as $cr) {
                $hasUser = ! empty($cr->referred_to_user_id);
                $hasCommunity = ! empty($cr->referred_to_community_id);
                if (! $hasUser && ! $hasCommunity) {
                    continue;
                }

                $exists = DB::table('content_request_referral_targets')
                    ->where('content_request_id', $cr->id)
                    ->exists();
                if ($exists) {
                    continue;
                }

                if ($hasUser) {
                    DB::table('content_request_referral_targets')->insert([
                        'content_request_id' => $cr->id,
                        'user_id' => $cr->referred_to_user_id,
                        'community_of_practice_id' => null,
                        'referral_forum_id' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                if ($hasCommunity) {
                    $forumId = ($hasCommunity && (int) $cr->referred_to_community_id)
                        ? $cr->referral_forum_id
                        : null;
                    DB::table('content_request_referral_targets')->insert([
                        'content_request_id' => $cr->id,
                        'user_id' => null,
                        'community_of_practice_id' => $cr->referred_to_community_id,
                        'referral_forum_id' => $forumId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('content_request_referral_targets');
    }
};
