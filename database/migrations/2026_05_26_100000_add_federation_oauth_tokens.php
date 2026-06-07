<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('federation_hub_credentials')) {
            Schema::create('federation_hub_credentials', function (Blueprint $table) {
                $table->id();
                $table->string('child_site_id', 128)->unique();
                $table->string('child_name', 255)->nullable();
                $table->string('access_token_hash', 64);
                $table->string('refresh_token_hash', 64);
                $table->timestamp('access_token_expires_at');
                $table->timestamp('refresh_token_expires_at')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamp('last_used_at')->nullable();
                $table->timestamps();

                $table->index('access_token_hash');
                $table->index('refresh_token_hash');
                $table->index('is_active');
            });
        }

        if (Schema::hasTable('setting')) {
            Schema::table('setting', function (Blueprint $table) {
                if (! Schema::hasColumn('setting', 'central_hub_refresh_token')) {
                    $table->text('central_hub_refresh_token')->nullable()->after('central_hub_api_token');
                }
                if (! Schema::hasColumn('setting', 'central_hub_token_expires_at')) {
                    $table->timestamp('central_hub_token_expires_at')->nullable()->after('central_hub_refresh_token');
                }
            });
        }

        if (Schema::hasTable('federated_knowledge_hubs')) {
            Schema::table('federated_knowledge_hubs', function (Blueprint $table) {
                if (! Schema::hasColumn('federated_knowledge_hubs', 'api_refresh_token')) {
                    $table->text('api_refresh_token')->nullable()->after('api_token');
                }
                if (! Schema::hasColumn('federated_knowledge_hubs', 'api_token_expires_at')) {
                    $table->timestamp('api_token_expires_at')->nullable()->after('api_refresh_token');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('federated_knowledge_hubs')) {
            Schema::table('federated_knowledge_hubs', function (Blueprint $table) {
                foreach (['api_token_expires_at', 'api_refresh_token'] as $column) {
                    if (Schema::hasColumn('federated_knowledge_hubs', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('setting')) {
            Schema::table('setting', function (Blueprint $table) {
                foreach (['central_hub_token_expires_at', 'central_hub_refresh_token'] as $column) {
                    if (Schema::hasColumn('setting', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        Schema::dropIfExists('federation_hub_credentials');
    }
};
