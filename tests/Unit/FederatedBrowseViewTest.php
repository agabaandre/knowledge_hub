<?php

namespace Tests\Unit;

use Tests\TestCase;

class FederatedBrowseViewTest extends TestCase
{
    public function test_linked_partner_hubs_sit_above_content_in_a_rtl_carousel(): void
    {
        $view = file_get_contents(resource_path('views/federation/browse.blade.php'));

        $this->assertIsString($view);

        $hubsPos = strpos($view, 'Linked partner hubs');
        $cardsPos = strpos($view, "include('partials.federation.publication_card'");

        $this->assertNotFalse($hubsPos);
        $this->assertNotFalse($cardsPos);
        $this->assertLessThan($cardsPos, $hubsPos, 'Partner hub cards should render above publication listings');
        $this->assertStringContainsString('fed-hubs-carousel', $view);
        $this->assertStringContainsString('fed-hubs-track', $view);
        $this->assertStringContainsString('fed-rtl-scroll', $view);
    }

    public function test_federated_publication_cards_use_a_shorter_cover_and_a_longer_excerpt(): void
    {
        $browse = file_get_contents(resource_path('views/federation/browse.blade.php'));
        $card = file_get_contents(resource_path('views/partials/federation/publication_card.blade.php'));

        $this->assertStringContainsString("'excerptWords' => 140", $browse);
        $this->assertStringContainsString('excerptWords', $card);
        $this->assertMatchesRegularExpression('/Str::words\([^,]+,\s*\$excerptWords/', $card);
        $this->assertStringContainsString('min-height: 320px', $browse);
        $this->assertStringContainsString('.federation-publication-card', $browse);
    }
}
