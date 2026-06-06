<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('setting')) {
            Schema::table('setting', function (Blueprint $table) {
                if (! Schema::hasColumn('setting', 'kpi_owid_auto_fetch_enabled')) {
                    $table->boolean('kpi_owid_auto_fetch_enabled')->default(true);
                }
                if (! Schema::hasColumn('setting', 'kpi_manual_data_only')) {
                    $table->boolean('kpi_manual_data_only')->default(false);
                }
            });
        }

        if (! Schema::hasTable('kpi_sync_runs')) {
            Schema::create('kpi_sync_runs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('action', 50);
                $table->string('status', 20)->default('queued');
                $table->unsignedTinyInteger('progress')->default(0);
                $table->string('step', 191)->nullable();
                $table->text('message')->nullable();
                $table->json('payload')->nullable();
                $table->json('result')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('finished_at')->nullable();
                $table->timestamps();
                $table->index(['user_id', 'status']);
                $table->index(['status', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_sync_runs');

        if (Schema::hasTable('setting')) {
            Schema::table('setting', function (Blueprint $table) {
                foreach (['kpi_manual_data_only', 'kpi_owid_auto_fetch_enabled'] as $col) {
                    if (Schema::hasColumn('setting', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
