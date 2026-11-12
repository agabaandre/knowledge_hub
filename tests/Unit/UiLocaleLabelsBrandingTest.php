<?php

namespace Tests\Unit;

use App\Support\UiLocaleLabels;
use Tests\TestCase;

class UiLocaleLabelsBrandingTest extends TestCase
{
    public function test_english_uses_admin_setting_when_present(): void
    {
        $resolved = UiLocaleLabels::resolveSiteBranding(
            'site_title',
            'Namibia Health Knowledge Hub',
            'Africa Health Knowledge Hub',
            'Africa Health Knowledge Hub',
            'en'
        );

        $this->assertSame('Namibia Health Knowledge Hub', $resolved);
    }

    public function test_english_falls_back_to_packaged_default_when_setting_empty(): void
    {
        $resolved = UiLocaleLabels::resolveSiteBranding(
            'site_title',
            null,
            'Africa Health Knowledge Hub',
            'Africa Health Knowledge Hub',
            'en'
        );

        $this->assertSame('Africa Health Knowledge Hub', $resolved);
    }

    public function test_english_tagline_is_empty_when_slogan_unset(): void
    {
        $resolved = UiLocaleLabels::resolveSiteBranding(
            'site_tagline',
            null,
            'Informing Health Decisions and Actions',
            '',
            'en'
        );

        $this->assertSame('', $resolved);
    }

    public function test_non_english_uses_real_translation_over_setting(): void
    {
        $englishDefault = UiLocaleLabels::packagedEnglishUiBody('site_title');
        $frPackaged = UiLocaleLabels::localeUiBodyValue('fr', 'site_title');
        $this->assertNotSame('', $frPackaged);
        $this->assertNotSame($englishDefault, $frPackaged);

        $resolved = UiLocaleLabels::resolveSiteBranding(
            'site_title',
            'Namibia Health Knowledge Hub',
            $englishDefault,
            $englishDefault,
            'fr'
        );

        $this->assertSame($frPackaged, $resolved);
    }

    public function test_non_english_falls_back_to_setting_when_no_real_translation(): void
    {
        $englishDefault = 'Africa Health Knowledge Hub';

        // Unknown locale → no packaged/storage translation → use admin setting.
        $resolved = UiLocaleLabels::resolveSiteBranding(
            'site_title',
            'Namibia Health Knowledge Hub',
            $englishDefault,
            $englishDefault,
            'zz'
        );
        $this->assertSame('Namibia Health Knowledge Hub', $resolved);
    }

    public function test_packaged_english_defaults_match_lang_file(): void
    {
        $this->assertSame('Africa Health Knowledge Hub', UiLocaleLabels::packagedEnglishUiBody('site_title'));
        $this->assertSame('Informing Health Decisions and Actions', UiLocaleLabels::packagedEnglishUiBody('site_tagline'));
    }

    public function test_french_packaged_site_title_differs_from_english_default(): void
    {
        $en = UiLocaleLabels::packagedEnglishUiBody('site_title');
        $fr = UiLocaleLabels::localeUiBodyValue('fr', 'site_title');

        $this->assertNotSame('', $fr);
        $this->assertNotSame($en, $fr);
    }
}
