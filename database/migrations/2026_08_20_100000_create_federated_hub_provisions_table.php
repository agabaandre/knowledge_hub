<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('federated_hub_provisions')) {
            return;
        }

        Schema::create('federated_hub_provisions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('country_id');
            $table->string('slug', 64);
            $table->string('site_name')->nullable();
            $table->string('base_url', 500);
            $table->string('target_path', 500)->nullable();
            $table->string('database_name', 128)->nullable();
            $table->string('database_username', 128)->nullable();
            $table->string('admin_email');
            $table->string('admin_first_name')->nullable();
            $table->string('admin_last_name')->nullable();
            $table->string('status', 32)->default('queued');
            $table->string('current_step', 64)->nullable();
            $table->unsignedTinyInteger('progress_percent')->default(0);
            $table->text('message')->nullable();
            $table->text('error_message')->nullable();
            $table->json('steps_completed')->nullable();
            $table->json('artifacts')->nullable();
            $table->unsignedBigInteger('federated_hub_id')->nullable();
            $table->unsignedBigInteger('requested_by')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('federated_hub_provisions');
    }
};
