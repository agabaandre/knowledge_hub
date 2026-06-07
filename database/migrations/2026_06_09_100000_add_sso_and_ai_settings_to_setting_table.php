<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('setting')) {
            return;
        }

        Schema::table('setting', function (Blueprint $table) {
            $ssoColumns = [
                'microsoft_client_id' => 'string:255',
                'microsoft_client_secret' => 'text',
                'microsoft_redirect_uri' => 'string:500',
                'microsoft_tenant_id' => 'string:100',
                'google_client_id' => 'string:255',
                'google_client_secret' => 'text',
                'google_redirect_uri' => 'string:500',
                'linkedin_client_id' => 'string:255',
                'linkedin_client_secret' => 'text',
                'linkedin_redirect_uri' => 'string:500',
            ];

            foreach ($ssoColumns as $column => $type) {
                if (Schema::hasColumn('setting', $column)) {
                    continue;
                }
                if (str_starts_with($type, 'text')) {
                    $table->text($column)->nullable();
                } elseif (preg_match('/string:(\d+)/', $type, $m)) {
                    $table->string($column, (int) $m[1])->nullable();
                }
            }

            $aiColumns = [
                'ai_primary_provider' => ['string', 40],
                'ai_openai_api_key' => ['text', null],
                'ai_openai_model' => ['string', 80],
                'ai_openai_enabled' => ['boolean', null],
                'ai_chatpdf_api_key' => ['text', null],
                'ai_chatpdf_enabled' => ['boolean', null],
                'ai_gemini_api_key' => ['text', null],
                'ai_gemini_model' => ['string', 80],
                'ai_gemini_enabled' => ['boolean', null],
                'ai_deepseek_api_key' => ['text', null],
                'ai_deepseek_model' => ['string', 80],
                'ai_deepseek_enabled' => ['boolean', null],
                'ai_custom_base_url' => ['string', 500],
                'ai_custom_api_key' => ['text', null],
                'ai_custom_model' => ['string', 120],
                'ai_custom_enabled' => ['boolean', null],
            ];

            foreach ($aiColumns as $column => [$kind, $size]) {
                if (Schema::hasColumn('setting', $column)) {
                    continue;
                }
                if ($kind === 'text') {
                    $table->text($column)->nullable();
                } elseif ($kind === 'boolean') {
                    $table->boolean($column)->default(false);
                } else {
                    $table->string($column, $size)->nullable();
                }
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('setting')) {
            return;
        }

        Schema::table('setting', function (Blueprint $table) {
            foreach ([
                'ai_custom_enabled', 'ai_custom_model', 'ai_custom_api_key', 'ai_custom_base_url',
                'ai_deepseek_enabled', 'ai_deepseek_model', 'ai_deepseek_api_key',
                'ai_gemini_enabled', 'ai_gemini_model', 'ai_gemini_api_key',
                'ai_chatpdf_enabled', 'ai_chatpdf_api_key',
                'ai_openai_enabled', 'ai_openai_model', 'ai_openai_api_key', 'ai_primary_provider',
                'linkedin_redirect_uri', 'linkedin_client_secret', 'linkedin_client_id',
                'google_redirect_uri', 'google_client_secret', 'google_client_id',
                'microsoft_tenant_id', 'microsoft_redirect_uri', 'microsoft_client_secret', 'microsoft_client_id',
            ] as $column) {
                if (Schema::hasColumn('setting', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
