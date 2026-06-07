<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('federated_content_items')) {
            return;
        }

        Schema::create('federated_content_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('federated_knowledge_hub_id');
            $table->string('content_type', 20);
            $table->unsignedBigInteger('remote_id');
            $table->string('title', 500)->nullable();
            $table->json('payload');
            $table->boolean('central_approved')->default(false);
            $table->boolean('central_rejected')->default(false);
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('remote_updated_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['federated_knowledge_hub_id', 'content_type', 'remote_id'], 'fed_content_hub_type_remote');
            $table->index(['central_approved', 'central_rejected', 'is_active']);
            $table->foreign('federated_knowledge_hub_id')
                ->references('id')
                ->on('federated_knowledge_hubs')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('federated_content_items');
    }
};
