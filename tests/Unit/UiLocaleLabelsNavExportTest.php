<?php

namespace Tests\Unit;

use App\Support\UiLocaleLabels;
use Tests\TestCase;

class UiLocaleLabelsNavExportTest extends TestCase
{
    public function test_export_never_ships_unresolved_translation_keys_as_values(): void
    {
        $labels = UiLocaleLabels::exportForCurrentLocale();

        foreach ($labels as $fullKey => $value) {
            $this->assertIsString($value);
            $this->assertNotSame(
                $fullKey,
                $value,
                "Export must not use unresolved key as label for {$fullKey}"
            );
            $this->assertNotSame('', $value);
        }
    }

    public function test_export_uses_catalog_fallback_for_dynamic_category_keys(): void
    {
        $service = app(\App\Services\UiTranslationService::class);
        $catalog = $service->loadEnglishGroup('frontend_nav');

        $dynamicKeys = array_filter(
            array_keys($catalog),
            static fn ($key) => is_string($key) && str_starts_with($key, 'category_')
        );

        if ($dynamicKeys === []) {
            $this->markTestSkipped('No data_categories available in this environment.');
        }

        $labels = UiLocaleLabels::exportForCurrentLocale();

        foreach ($dynamicKeys as $key) {
            $fullKey = 'frontend_nav.'.$key;
            $catalogValue = (string) ($catalog[$key] ?? '');
            if ($catalogValue === '') {
                continue;
            }

            $this->assertArrayHasKey($fullKey, $labels);
            $this->assertSame($catalogValue, $labels[$fullKey]);
            $this->assertNotSame($fullKey, $labels[$fullKey]);
        }
    }

    public function test_nav_category_label_falls_back_to_category_name(): void
    {
        $category = (object) [
            'slug' => 'dashboards',
            'category_name' => 'Dashboards',
        ];

        $label = UiLocaleLabels::navCategoryLabel($category);

        $this->assertSame('Dashboards', $label);
        $this->assertNotSame('frontend_nav.category_dashboards', $label);
    }
}
