<?php

namespace Tests\Unit;

use App\Support\FooterPartners;
use Illuminate\Http\Request;
use Tests\TestCase;

class FooterPartnersTest extends TestCase
{
    public function test_decode_accepts_json_and_arrays(): void
    {
        $this->assertSame([], FooterPartners::decode(null));
        $this->assertSame([], FooterPartners::decode(''));
        $this->assertSame(
            [['file' => 'who.png', 'name' => 'WHO']],
            FooterPartners::decode('[{"file":"who.png","name":"WHO"}]')
        );
        $this->assertSame(
            [['file' => 'who.png']],
            FooterPartners::decode([['file' => 'who.png']])
        );
    }

    public function test_items_skip_rows_without_a_logo_file(): void
    {
        $items = FooterPartners::items([
            ['name' => 'No file'],
            ['file' => 'who.png', 'name' => 'WHO', 'url' => 'https://www.who.int'],
            ['file' => 'cdc.png', 'name' => 'CDC', 'url' => 'javascript:alert(1)'],
        ]);

        $this->assertCount(2, $items);
        $this->assertSame('who.png', $items[0]['file']);
        $this->assertSame('WHO', $items[0]['name']);
        $this->assertSame('https://www.who.int', $items[0]['url']);
        $this->assertNotSame('', $items[0]['image']);
        $this->assertSame('', $items[1]['url']);
    }

    public function test_from_request_keeps_existing_files_and_stores_uploads(): void
    {
        $request = Request::create('/admin/configure', 'POST', [
            'partner_logos' => [
                ['file' => 'who.png', 'name' => 'WHO', 'url' => 'https://www.who.int'],
                ['file' => '', 'name' => 'Ignored'],
                ['file' => 'old.png', 'name' => 'Replaced', 'url' => 'https://example.org'],
            ],
        ]);

        $saved = FooterPartners::fromRequest($request, function () {
            return 'uploaded-partner.png';
        });

        $this->assertCount(2, $saved);
        $this->assertSame('who.png', $saved[0]['file']);
        $this->assertSame('WHO', $saved[0]['name']);
        $this->assertSame('old.png', $saved[1]['file']);
    }

    public function test_show_names_is_disabled_by_default(): void
    {
        $this->assertFalse(FooterPartners::showNames(null));
        $this->assertFalse(FooterPartners::showNames((object) []));
        $this->assertFalse(FooterPartners::showNames((object) ['show_partner_names' => false]));
        $this->assertFalse(FooterPartners::showNames((object) ['show_partner_names' => '0']));
        $this->assertTrue(FooterPartners::showNames((object) ['show_partner_names' => true]));
        $this->assertTrue(FooterPartners::showNames((object) ['show_partner_names' => '1']));
    }

    public function test_logo_max_height_defaults_to_100_and_clamps_between_50_and_200(): void
    {
        $this->assertSame(100, FooterPartners::logoMaxHeight(null));
        $this->assertSame(100, FooterPartners::logoMaxHeight((object) []));
        $this->assertSame(100, FooterPartners::logoMaxHeight((object) ['partner_logo_max_height' => 100]));
        $this->assertSame(80, FooterPartners::logoMaxHeight((object) ['partner_logo_max_height' => '80']));
        $this->assertSame(50, FooterPartners::logoMaxHeight((object) ['partner_logo_max_height' => 10]));
        $this->assertSame(200, FooterPartners::logoMaxHeight((object) ['partner_logo_max_height' => 500]));
    }

    public function test_branding_and_footers_include_the_partners_row(): void
    {
        $branding = file_get_contents(resource_path('views/admin/settings/partials/tab_branding.blade.php'));
        $footer = file_get_contents(resource_path('views/layouts/partials/footer.blade.php'));
        $theme1 = file_get_contents(resource_path('views/layouts/theme1/partials/footer.blade.php'));
        $partial = file_get_contents(resource_path('views/layouts/partials/footer_partners.blade.php'));
        $repository = file_get_contents(app_path('Repositories/SettingsRepository.php'));

        $this->assertStringContainsString('Partner logos', $branding);
        $this->assertStringContainsString('name="partner_logos[', file_get_contents(resource_path('views/admin/settings/partials/partner_logo_row.blade.php')));
        $this->assertStringContainsString('name="show_partner_names"', $branding);
        $this->assertStringContainsString('settings()->show_partner_names', $branding);
        $this->assertStringContainsString('name="partner_logo_max_height"', $branding);
        $this->assertStringContainsString('min="50"', $branding);
        $this->assertStringContainsString('max="200"', $branding);
        $this->assertStringContainsString('layouts.partials.footer_partners', $footer);
        $this->assertStringContainsString('layouts.partials.footer_partners', $theme1);
        $this->assertLessThan(strpos($footer, '<footer'), strpos($footer, "layouts.partials.footer_partners"));
        $this->assertLessThan(strpos($theme1, '<footer'), strpos($theme1, "layouts.partials.footer_partners"));
        $this->assertStringContainsString('footer_partner_logos', $partial);
        $this->assertStringContainsString('FooterPartners::showNames', $partial);
        $this->assertStringContainsString('FooterPartners::logoMaxHeight', $partial);
        $this->assertStringContainsString('footer-partners-name', $partial);
        $this->assertStringContainsString('khub-footer-partners', $partial);
        $this->assertStringContainsString('background: #ffffff', $partial);
        $this->assertStringContainsString('.footer-partners-item img', $partial);
        $this->assertStringContainsString('max-height:', $partial);
        $this->assertStringContainsString("show_partner_names", $repository);
        $this->assertStringContainsString('partner_logo_max_height', $repository);
    }
}
