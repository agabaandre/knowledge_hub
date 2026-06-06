<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFederatedKnowledgeHubsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('federated_knowledge_hubs')) {
            return;
        }

        Schema::create('federated_knowledge_hubs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('base_url');
            $table->string('remote_site_id')->nullable();
            $table->string('api_token')->nullable();
            $table->unsignedBigInteger('mapped_country_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('auto_sync')->default(false);
            $table->string('connection_status', 32)->default('pending');
            $table->text('connection_error')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->json('last_manifest')->nullable();
            $table->json('cached_public_data')->nullable();
            $table->timestamps();

            $table->index('is_active');
            $table->index('mapped_country_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('federated_knowledge_hubs');
    }
}
