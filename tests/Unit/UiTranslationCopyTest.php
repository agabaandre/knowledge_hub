<?php

namespace Tests\Unit;

use App\Services\UiTranslationService;
use Tests\TestCase;

class UiTranslationCopyTest extends TestCase
{
    public function test_copy_english_payload_fills_empty_keys_from_english(): void
    {
        $service = app(UiTranslationService::class);
        $english = $service->loadEnglishGroup('frontend_nav');

        $this->assertArrayHasKey('records', $english);
        $this->assertArrayHasKey('read_more', $english);
        $this->assertArrayHasKey('layout_ltr', $english);

        $payload = $service->copyEnglishPayload('fr', 'frontend_nav', [], true);

        $this->assertSame($english['home'], $payload['home']);
        $this->assertSame($english['records'], $payload['records']);
        $this->assertSame($english['read_more'], $payload['read_more']);
    }

    public function test_copy_english_payload_keeps_existing_translations_when_only_empty(): void
    {
        $service = app(UiTranslationService::class);
        $english = $service->loadEnglishGroup('frontend_nav');

        $payload = $service->copyEnglishPayload('fr', 'frontend_nav', [
            'home' => 'Accueil',
            'records' => '',
        ], true);

        $this->assertSame('Accueil', $payload['home']);
        $this->assertSame($english['records'], $payload['records']);
    }

    public function test_copy_english_payload_can_overwrite_when_requested(): void
    {
        $service = app(UiTranslationService::class);
        $english = $service->loadEnglishGroup('frontend_nav');

        $payload = $service->copyEnglishPayload('fr', 'frontend_nav', [
            'home' => 'Accueil',
        ], false);

        $this->assertSame($english['home'], $payload['home']);
    }

    public function test_copy_english_to_locale_writes_storage_overrides(): void
    {
        $service = app(UiTranslationService::class);
        $path = $service->storageLocalePath('sw', 'frontend_nav');
        $dir = dirname($path);

        if (is_file($path)) {
            unlink($path);
        }

        $written = $service->copyEnglishToLocale('sw', 'frontend_nav', false);

        $this->assertGreaterThan(0, $written);
        $this->assertFileExists($path);

        $loaded = require $path;
        $this->assertIsArray($loaded);
        $this->assertSame('Home', $loaded['home']);
        $this->assertSame('LTR', $loaded['layout_ltr']);

        unlink($path);
        if (is_dir($dir) && count(glob($dir.'/*.php') ?: []) === 0) {
            @rmdir($dir);
        }
    }
}
