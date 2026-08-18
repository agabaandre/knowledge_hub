<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLifetimeContributorBadgesTables extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('user_lifetime_badges')) {
            Schema::create('user_lifetime_badges', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
                $table->foreignId('badge_type_id')->nullable()->constrained('badge_types')->nullOnDelete();
                $table->unsignedInteger('lifetime_contributions')->default(0);
                $table->timestamp('last_upgraded_at')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('user_community_monthly_contributions')) {
            return;
        }

        Schema::create('user_community_monthly_contributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('community_of_practice_id');
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->unsignedInteger('contributions_count')->default(0);
            $table->timestamps();

            $table->foreign('community_of_practice_id', 'ucmc_community_id_foreign')
                ->references('id')
                ->on('community_of_practices')
                ->onDelete('cascade');

            $table->unique(
                ['user_id', 'community_of_practice_id', 'year', 'month'],
                'user_community_month_unique'
            );
            $table->index(['user_id', 'year', 'month'], 'ucmc_user_year_month_index');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_community_monthly_contributions');
        Schema::dropIfExists('user_lifetime_badges');
    }
}
