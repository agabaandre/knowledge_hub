<?php

namespace Tests\Unit;

use Tests\TestCase;

class AdminUnitDetailsViewTest extends TestCase
{
    public function test_admin_unit_details_uses_the_same_publication_cards_as_records_search(): void
    {
        $details = file_get_contents(resource_path('views/adminunits/details.blade.php'));
        $search = file_get_contents(resource_path('views/publications/search.blade.php'));
        $searchBody = file_get_contents(resource_path('views/publications/partials/search_main_body.blade.php'));
        $controller = file_get_contents(app_path('Http/Controllers/AdminUnitFrontEndController.php'));

        $this->assertIsString($details);
        $this->assertIsString($search);

        $this->assertStringContainsString("include('partials.publications.publication_feed_card_styles')", $search);
        $this->assertStringContainsString("include('partials.publications.publication_feed_card_scripts')", $search);
        $this->assertStringContainsString("include('publications.partials.publications')", $searchBody);

        $this->assertStringContainsString("include('partials.publications.publication_feed_card_styles')", $details);
        $this->assertStringContainsString("include('publications.partials.preview_modal_styles')", $details);
        $this->assertStringContainsString("include('publications.partials.publications')", $details);
        $this->assertStringContainsString("include('partials.publications.publication_feed_card_scripts')", $details);
        $this->assertStringContainsString("include('publications.partials.preview_modal')", $details);
        $this->assertStringNotContainsString("include('partials.federation.publication_card'", $details);

        $this->assertStringContainsString("'search_listing'", $controller);
        $this->assertStringContainsString("'skip_random_order'", $controller);
    }
}
