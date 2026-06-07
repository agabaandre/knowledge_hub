<?php

namespace App\Support;

use Illuminate\Support\Facades\Schema;

class AiConfig
{
    private static ?object $dbSettings = null;

    public static function clearCache(): void
    {
        self::$dbSettings = null;
    }

    public static function dbSettings(): ?object
    {
        if (self::$dbSettings !== null) {
            return self::$dbSettings;
        }

        if (! Schema::hasTable('setting')) {
            return null;
        }

        self::$dbSettings = \DB::table('setting')->where('status', 'active')->first()
            ?: \DB::table('setting')->first();

        return self::$dbSettings;
    }

    /**
     * @param  mixed  $default
     * @return mixed
     */
    public static function resolve(string $envKey, ?string $dbColumn = null, $default = null)
    {
        $db = self::dbSettings();
        $column = $dbColumn ?? strtolower($envKey);
        if ($db && $column && property_exists($db, $column)) {
            $dbValue = $db->{$column};
            if ($dbValue !== null && $dbValue !== '') {
                return $dbValue;
            }
        }

        $envValue = env($envKey);
        if ($envValue !== null && $envValue !== '') {
            return $envValue;
        }

        return $default;
    }

    /**
     * @return list<string>
     */
    public static function providers(): array
    {
        return array_keys(config('ai.providers', []));
    }

    public static function providerEnabled(string $provider): bool
    {
        if (! in_array($provider, self::providers(), true)) {
            return false;
        }

        $db = self::dbSettings();
        $column = 'ai_'.$provider.'_enabled';
        if ($db && Schema::hasColumn('setting', $column) && $db->{$column} !== null) {
            return (bool) $db->{$column};
        }

        return (bool) config('ai.providers.'.$provider.'.default_enabled', false);
    }

    public static function providerConfigured(string $provider): bool
    {
        return match ($provider) {
            'openai' => trim((string) self::openaiApiKey()) !== '',
            'chatpdf' => trim((string) self::chatPdfApiKey()) !== '',
            'gemini' => trim((string) self::geminiApiKey()) !== '',
            'deepseek' => trim((string) self::deepseekApiKey()) !== '',
            'custom' => trim((string) self::customBaseUrl()) !== ''
                && trim((string) self::customApiKey()) !== ''
                && trim((string) self::customModel()) !== '',
            default => false,
        };
    }

    public static function providerAvailable(string $provider): bool
    {
        return self::providerEnabled($provider) && self::providerConfigured($provider);
    }

    /**
     * First enabled + configured chat provider, or null.
     */
    public static function primaryChatProvider(): ?string
    {
        $preferred = trim((string) self::resolve('', 'ai_primary_provider', config('ai.default_primary_provider', 'openai')));
        $chatProviders = ['openai', 'gemini', 'deepseek', 'custom'];

        if (in_array($preferred, $chatProviders, true) && self::providerAvailable($preferred)) {
            return $preferred;
        }

        foreach ($chatProviders as $provider) {
            if (self::providerAvailable($provider)) {
                return $provider;
            }
        }

        return null;
    }

    public static function openaiApiKey(): string
    {
        return (string) self::resolve('OPEN_API_KEY', 'ai_openai_api_key', '');
    }

    public static function openaiModel(): string
    {
        return (string) self::resolve('OPENAI_MODEL', 'ai_openai_model', 'gpt-3.5-turbo');
    }

    public static function chatPdfApiKey(): string
    {
        return (string) self::resolve('CHAT_PDF_API_KEY', 'ai_chatpdf_api_key', '');
    }

    public static function geminiApiKey(): string
    {
        return (string) self::resolve('GEMINI_API_KEY', 'ai_gemini_api_key', '');
    }

    public static function geminiModel(): string
    {
        return (string) self::resolve('GEMINI_MODEL', 'ai_gemini_model', 'gemini-1.5-flash');
    }

    public static function deepseekApiKey(): string
    {
        return (string) self::resolve('DEEPSEEK_API_KEY', 'ai_deepseek_api_key', '');
    }

    public static function deepseekModel(): string
    {
        return (string) self::resolve('DEEPSEEK_MODEL', 'ai_deepseek_model', 'deepseek-chat');
    }

    public static function customBaseUrl(): string
    {
        return rtrim((string) self::resolve('AI_CUSTOM_BASE_URL', 'ai_custom_base_url', ''), '/');
    }

    public static function customApiKey(): string
    {
        return (string) self::resolve('AI_CUSTOM_API_KEY', 'ai_custom_api_key', '');
    }

    public static function customModel(): string
    {
        return (string) self::resolve('AI_CUSTOM_MODEL', 'ai_custom_model', '');
    }

    public static function applyRuntimeConfig(): void
    {
        if (! Schema::hasTable('setting')) {
            return;
        }

        config([
            'ai.open_api_key' => self::openaiApiKey(),
            'ai.openai_model' => self::openaiModel(),
            'ai.chat_pdf_key' => self::chatPdfApiKey(),
            'ai.gemini_api_key' => self::geminiApiKey(),
            'ai.gemini_model' => self::geminiModel(),
            'ai.deepseek_api_key' => self::deepseekApiKey(),
            'ai.deepseek_model' => self::deepseekModel(),
            'ai.custom_base_url' => self::customBaseUrl(),
            'ai.custom_api_key' => self::customApiKey(),
            'ai.custom_model' => self::customModel(),
            'ai.primary_provider' => self::primaryChatProvider(),
        ]);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function fieldsForAdmin(): array
    {
        $db = self::dbSettings();
        $map = [
            'ai_primary_provider' => ['db_column' => 'ai_primary_provider', 'env_key' => 'AI_PRIMARY_PROVIDER', 'default' => 'openai'],
            'ai_openai_api_key' => ['db_column' => 'ai_openai_api_key', 'env_key' => 'OPEN_API_KEY', 'default' => '', 'secret' => true],
            'ai_openai_model' => ['db_column' => 'ai_openai_model', 'env_key' => 'OPENAI_MODEL', 'default' => 'gpt-3.5-turbo'],
            'ai_openai_enabled' => ['db_column' => 'ai_openai_enabled', 'env_key' => '', 'default' => true, 'boolean' => true],
            'ai_chatpdf_api_key' => ['db_column' => 'ai_chatpdf_api_key', 'env_key' => 'CHAT_PDF_API_KEY', 'default' => '', 'secret' => true],
            'ai_chatpdf_enabled' => ['db_column' => 'ai_chatpdf_enabled', 'env_key' => '', 'default' => true, 'boolean' => true],
            'ai_gemini_api_key' => ['db_column' => 'ai_gemini_api_key', 'env_key' => 'GEMINI_API_KEY', 'default' => '', 'secret' => true],
            'ai_gemini_model' => ['db_column' => 'ai_gemini_model', 'env_key' => 'GEMINI_MODEL', 'default' => 'gemini-1.5-flash'],
            'ai_gemini_enabled' => ['db_column' => 'ai_gemini_enabled', 'env_key' => '', 'default' => false, 'boolean' => true],
            'ai_deepseek_api_key' => ['db_column' => 'ai_deepseek_api_key', 'env_key' => 'DEEPSEEK_API_KEY', 'default' => '', 'secret' => true],
            'ai_deepseek_model' => ['db_column' => 'ai_deepseek_model', 'env_key' => 'DEEPSEEK_MODEL', 'default' => 'deepseek-chat'],
            'ai_deepseek_enabled' => ['db_column' => 'ai_deepseek_enabled', 'env_key' => '', 'default' => false, 'boolean' => true],
            'ai_custom_base_url' => ['db_column' => 'ai_custom_base_url', 'env_key' => 'AI_CUSTOM_BASE_URL', 'default' => ''],
            'ai_custom_api_key' => ['db_column' => 'ai_custom_api_key', 'env_key' => 'AI_CUSTOM_API_KEY', 'default' => '', 'secret' => true],
            'ai_custom_model' => ['db_column' => 'ai_custom_model', 'env_key' => 'AI_CUSTOM_MODEL', 'default' => ''],
            'ai_custom_enabled' => ['db_column' => 'ai_custom_enabled', 'env_key' => '', 'default' => false, 'boolean' => true],
        ];

        $fields = [];
        foreach ($map as $key => $meta) {
            $dbValue = ($db && property_exists($db, $meta['db_column'])) ? $db->{$meta['db_column']} : null;
            $effective = $meta['env_key'] !== ''
                ? self::resolve($meta['env_key'], $meta['db_column'], $meta['default'])
                : ($dbValue !== null ? $dbValue : $meta['default']);

            if (! empty($meta['boolean'])) {
                $hasDbValue = $dbValue !== null;
                $formValue = filter_var($hasDbValue ? $dbValue : $effective, FILTER_VALIDATE_BOOLEAN);
                $value = filter_var($effective, FILTER_VALIDATE_BOOLEAN);
            } else {
                $hasDbValue = $dbValue !== null && $dbValue !== '';
                $formValue = $hasDbValue ? $dbValue : $effective;
                $value = $effective;
            }

            if (! empty($meta['secret']) && $hasDbValue) {
                $formValue = '';
            }

            $fields[$key] = [
                'env_key' => $meta['env_key'],
                'db_column' => $meta['db_column'],
                'value' => $value,
                'form_value' => $formValue,
                'boolean' => ! empty($meta['boolean']),
                'secret' => ! empty($meta['secret']),
            ];
        }

        return $fields;
    }
}
