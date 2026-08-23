<?php

namespace Tests\Unit;

use App\Models\Country;
use App\Models\FederatedKnowledgeHub;
use Tests\TestCase;

class FederatedKnowledgeHubFlagTest extends TestCase
{
    public function test_iso2_and_svg_flag_url_come_from_mapped_country(): void
    {
        $country = new Country();
        $country->iso_code = 'GH';
        $country->flag = null;

        $hub = new FederatedKnowledgeHub();
        $hub->setRelation('mappedCountry', $country);

        $this->assertSame('gh', $hub->countryIso2());
        $this->assertStringContainsString('assets/img/flags/gh.svg', (string) $hub->countryFlagUrl());
    }

    public function test_missing_country_has_no_flag(): void
    {
        $hub = new FederatedKnowledgeHub();
        $hub->setRelation('mappedCountry', null);

        $this->assertNull($hub->countryIso2());
        $this->assertNull($hub->countryFlagUrl());
    }
}
