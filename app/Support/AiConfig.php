<?php

namespace App\Support;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

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

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function features(): array
    {
        return config('ai.features', []);
    }

    /**
     * @return array<string, string>
     */
    public static function defaultFeatureRouting(): array
    {
        return config('ai.default_feature_routing', []);
    }

    /**
     * @return array<string, string>
     */
    public static function featureRouting(): array
    {
        $defaults = self::defaultFeatureRouting();
        $db = self::dbSettings();

        if ($db && Schema::hasColumn('setting', 'ai_feature_routing') && ! empty($db->ai_feature_routing)) {
            $stored = json_decode((string) $db->ai_feature_routing, true);
            if (is_array($stored)) {
                return array_merge($defaults, array_filter($stored, fn ($v) => is_string($v) && $v !== ''));
            }
        }

        return $defaults;
    }

    public static function featureProvider(string $feature): string
    {
        $routing = self::featureRouting();
        if (isset($routing[$feature]) && $routing[$feature] !== '') {
            return (string) $routing[$feature];
        }

        return (string) (config('ai.features.'.$feature.'.default_provider') ?? 'openai');
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function customIntegrations(): array
    {
        $db = self::dbSettings();
        if (! $db || ! Schema::hasColumn('setting', 'ai_custom_integrations') || empty($db->ai_custom_integrations)) {
            return [];
        }

        $decoded = json_decode((string) $db->ai_custom_integrations, true);
        if (! is_array($decoded)) {
            return [];
        }

        $integrations = [];
        foreach ($decoded as $row) {
            if (! is_array($row)) {
                continue;
            }
            $id = trim((string) ($row['id'] ?? ''));
            if ($id === '') {
                continue;
            }
            $integrations[] = [
                'id' => $id,
                'name' => trim((string) ($row['name'] ?? 'Custom integration')),
                'driver' => in_array($row['driver'] ?? '', ['openai_compatible', 'gemini'], true)
                    ? $row['driver']
                    : 'openai_compatible',
                'base_url' => rtrim(trim((string) ($row['base_url'] ?? '')), '/'),
                'api_key' => (string) ($row['api_key'] ?? ''),
                'model' => trim((string) ($row['model'] ?? '')),
                'enabled' => filter_var($row['enabled'] ?? false, FILTER_VALIDATE_BOOLEAN),
            ];
        }

        return $integrations;
    }

    public static function integrationProviderId(string $integrationId): string
    {
        return 'integration_'.$integrationId;
    }

    public static function isIntegrationProvider(string $providerId): bool
    {
        return str_starts_with($providerId, 'integration_');
    }

    public static function integrationIdFromProvider(string $providerId): ?string
    {
        if (! self::isIntegrationProvider($providerId)) {
            return null;
        }

        return substr($providerId, strlen('integration_'));
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function customIntegrationById(string $integrationId): ?array
    {
        foreach (self::customIntegrations() as $integration) {
            if ($integration['id'] === $integrationId) {
                return $integration;
            }
        }

        return null;
    }

    public static function providerEnabled(string $provider): bool
    {
        if (self::isIntegrationProvider($provider)) {
            $integration = self::customIntegrationById((string) self::integrationIdFromProvider($provider));

            return $integration !== null && $integration['enabled'];
        }

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
        if (self::isIntegrationProvider($provider)) {
            $integration = self::customIntegrationById((string) self::integrationIdFromProvider($provider));
            if ($integration === null) {
                return false;
            }
            if ($integration['driver'] === 'gemini') {
                return trim($integration['api_key']) !== '' && trim($integration['model']) !== '';
            }

            return trim($integration['base_url']) !== ''
                && trim($integration['api_key']) !== ''
                && trim($integration['model']) !== '';
        }

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
     * @return list<string>
     */
    public static function chatProviderIds(): array
    {
        $ids = [];
        foreach (self::providers() as $provider) {
            if (config('ai.providers.'.$provider.'.chat', false)) {
                $ids[] = $provider;
            }
        }
        foreach (self::customIntegrations() as $integration) {
            $ids[] = self::integrationProviderId($integration['id']);
        }

        return $ids;
    }

    /**
     * First enabled + configured chat provider for a feature, with fallback chain.
     */
    public static function resolveChatProviderForFeature(string $feature): ?string
    {
        $preferred = self::featureProvider($feature);
        if (self::providerAvailable($preferred)) {
            return $preferred;
        }

        if (self::providerAvailable('openai')) {
            return 'openai';
        }

        foreach (self::chatProviderIds() as $provider) {
            if (self::providerAvailable($provider)) {
                return $provider;
            }
        }

        return null;
    }

    public static function primaryChatProvider(): ?string
    {
        return self::resolveChatProviderForFeature('chat');
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

    /**
     * @return array{driver: string, api_key: string, model: string, base_url: string, label: string}|null
     */
    public static function providerCredentials(string $providerId): ?array
    {
        if (self::isIntegrationProvider($providerId)) {
            $integration = self::customIntegrationById((string) self::integrationIdFromProvider($providerId));
            if ($integration === null) {
                return null;
            }

            return [
                'driver' => $integration['driver'],
                'api_key' => $integration['api_key'],
                'model' => $integration['model'],
                'base_url' => $integration['base_url'],
                'label' => $integration['name'],
            ];
        }

        return match ($providerId) {
            'openai' => [
                'driver' => 'openai_compatible',
                'api_key' => self::openaiApiKey(),
                'model' => self::openaiModel(),
                'base_url' => 'https://api.openai.com/v1',
                'label' => 'OpenAI',
            ],
            'deepseek' => [
                'driver' => 'openai_compatible',
                'api_key' => self::deepseekApiKey(),
                'model' => self::deepseekModel(),
                'base_url' => 'https://api.deepseek.com/v1',
                'label' => 'DeepSeek',
            ],
            'custom' => [
                'driver' => 'openai_compatible',
                'api_key' => self::customApiKey(),
                'model' => self::customModel(),
                'base_url' => self::customBaseUrl(),
                'label' => 'Custom endpoint',
            ],
            'gemini' => [
                'driver' => 'gemini',
                'api_key' => self::geminiApiKey(),
                'model' => self::geminiModel(),
                'base_url' => '',
                'label' => 'Google Gemini',
            ],
            default => null,
        };
    }

    /**
     * @return array<string, string>
     */
    public static function providerOptionsForFeature(string $feature): array
    {
        $featureMeta = config('ai.features.'.$feature, []);
        $types = $featureMeta['provider_types'] ?? ['chat'];
        $options = [];

        if (in_array('document', $types, true)) {
            $options['chatpdf'] = (string) config('ai.providers.chatpdf.label', 'ChatPDF');
        }

        if (in_array('chat', $types, true)) {
            foreach (self::providers() as $provider) {
                if (config('ai.providers.'.$provider.'.chat', false)) {
                    $options[$provider] = (string) config('ai.providers.'.$provider.'.label', $provider);
                }
            }
            foreach (self::customIntegrations() as $integration) {
                $pid = self::integrationProviderId($integration['id']);
                $options[$pid] = $integration['name'];
            }
        }

        return $options;
    }

    /**
     * @return array<string, string>
     */
    public static function allProviderLabels(): array
    {
        $labels = [];
        foreach (config('ai.providers', []) as $id => $meta) {
            $labels[$id] = (string) ($meta['label'] ?? $id);
        }
        foreach (self::customIntegrations() as $integration) {
            $labels[self::integrationProviderId($integration['id'])] = $integration['name'];
        }

        return $labels;
    }

    public static function providerStatus(string $providerId): string
    {
        if (! self::providerEnabled($providerId)) {
            return 'disabled';
        }

        return self::providerConfigured($providerId) ? 'ready' : 'incomplete';
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
            'ai.feature_routing' => self::featureRouting(),
            'ai.custom_integrations' => self::customIntegrations(),
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

    /**
     * @return array<string, mixed>
     */
    public static function adminPageData(): array
    {
        $fields = self::fieldsForAdmin();
        $routing = self::featureRouting();
        $integrations = self::customIntegrations();
        $providerLabels = self::allProviderLabels();

        $builtin = [];
        foreach (config('ai.providers', []) as $id => $meta) {
            $builtin[] = [
                'id' => $id,
                'label' => (string) ($meta['label'] ?? $id),
                'description' => (string) ($meta['description'] ?? ''),
                'icon' => (string) ($meta['icon'] ?? 'fa-robot'),
                'color' => (string) ($meta['color'] ?? '#6c757d'),
                'capabilities' => $meta['capabilities'] ?? [],
                'chat' => (bool) ($meta['chat'] ?? false),
                'status' => self::providerStatus($id),
                'enabled' => self::providerEnabled($id),
                'configured' => self::providerConfigured($id),
            ];
        }

        $features = [];
        foreach (self::features() as $key => $meta) {
            $assigned = $routing[$key] ?? ($meta['default_provider'] ?? 'openai');
            $features[] = [
                'key' => $key,
                'label' => (string) ($meta['label'] ?? $key),
                'description' => (string) ($meta['description'] ?? ''),
                'assigned_provider' => $assigned,
                'assigned_label' => $providerLabels[$assigned] ?? $assigned,
                'provider_options' => self::providerOptionsForFeature($key),
                'status' => self::providerAvailable($assigned) ? 'ready' : 'unavailable',
            ];
        }

        $readyCount = 0;
        foreach (array_merge(array_column($builtin, 'id'), array_map(fn ($i) => self::integrationProviderId($i['id']), $integrations)) as $pid) {
            if (self::providerAvailable($pid)) {
                $readyCount++;
            }
        }

        return [
            'fields' => $fields,
            'features' => $features,
            'builtin_providers' => $builtin,
            'custom_integrations' => array_map(function (array $integration) {
                $pid = self::integrationProviderId($integration['id']);

                return array_merge($integration, [
                    'provider_id' => $pid,
                    'status' => self::providerStatus($pid),
                    'has_stored_key' => trim($integration['api_key']) !== '',
                ]);
            }, $integrations),
            'stats' => [
                'ready_providers' => $readyCount,
                'total_features' => count($features),
                'features_ready' => count(array_filter($features, fn ($f) => $f['status'] === 'ready')),
                'primary_chat' => self::primaryChatProvider(),
                'primary_chat_label' => $providerLabels[self::primaryChatProvider() ?? ''] ?? 'Not configured',
            ],
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $submitted
     * @param  list<array<string, mixed>>  $existing
     * @return list<array<string, mixed>>
     */
    public static function normalizeCustomIntegrationsInput(array $submitted, array $existing = []): array
    {
        $existingById = [];
        foreach ($existing as $row) {
            $existingById[$row['id']] = $row;
        }

        $normalized = [];
        $usedIds = [];

        foreach ($submitted as $index => $row) {
            if (! is_array($row)) {
                continue;
            }

            $name = trim((string) ($row['name'] ?? ''));
            if ($name === '') {
                continue;
            }

            $id = trim((string) ($row['id'] ?? ''));
            if ($id === '') {
                $id = Str::slug($name);
            }
            $baseId = $id;
            $suffix = 2;
            while (in_array($id, $usedIds, true)) {
                $id = $baseId.'-'.$suffix;
                $suffix++;
            }
            $usedIds[] = $id;

            $apiKey = trim((string) ($row['api_key'] ?? ''));
            if ($apiKey === '' && isset($existingById[$id])) {
                $apiKey = (string) ($existingById[$id]['api_key'] ?? '');
            }

            $normalized[] = [
                'id' => $id,
                'name' => $name,
                'driver' => in_array($row['driver'] ?? '', ['openai_compatible', 'gemini'], true)
                    ? $row['driver']
                    : 'openai_compatible',
                'base_url' => rtrim(trim((string) ($row['base_url'] ?? '')), '/'),
                'api_key' => $apiKey,
                'model' => trim((string) ($row['model'] ?? '')),
                'enabled' => filter_var($row['enabled'] ?? false, FILTER_VALIDATE_BOOLEAN),
            ];
        }

        return $normalized;
    }
}
