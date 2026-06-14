<?php

namespace Tests\Feature;

use App\Http\Middleware\BotProtection;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class RecordsSearchSecurityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['app.url' => 'http://localhost']);
        URL::forceRootUrl('http://localhost');
        $this->withoutMiddleware(BotProtection::class);
    }
    /**
     * Fragment endpoint uses JSON validation responses (422) for invalid filter IDs.
     */
    public function test_rejects_invalid_country_id_on_fragment(): void
    {
        $response = $this->getJson('/records/search/fragment?country_id=abc');

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['country_id']);
    }

    public function test_rejects_sql_injection_style_country_id_on_fragment(): void
    {
        $response = $this->getJson('/records/search/fragment?country_id=1 OR 1=1');

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['country_id']);
    }

    public function test_rejects_sql_injection_style_rcc_on_fragment(): void
    {
        $response = $this->getJson('/records/search/fragment?rcc=1 OR 1=1');

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['rcc']);
    }

    public function test_rejects_invalid_author_id_on_fragment(): void
    {
        $response = $this->getJson('/records/search/fragment?author_id=1\' OR \'1\'=\'1');

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['author_id']);
    }

    public function test_rejects_invalid_file_type_id_on_fragment(): void
    {
        $response = $this->getJson('/records/search/fragment?file_type_id=1; DROP TABLE users--');

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['file_type_id']);
    }

    public function test_rejects_invalid_tag_on_fragment(): void
    {
        $response = $this->getJson('/records/search/fragment?tag=1 OR 1=1');

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['tag']);
    }

    /**
     * Search term probes are accepted but must not expose database errors in the response.
     */
    public function test_term_sql_probe_does_not_leak_database_errors_on_fragment(): void
    {
        $response = $this->getJson('/records/search/fragment?term=test\' OR 1=1--');

        $response->assertStatus(200);
        $this->assertStringNotContainsStringIgnoringCase('SQLSTATE', $response->getContent());
        $this->assertStringNotContainsStringIgnoringCase('PDOException', $response->getContent());
        $this->assertStringNotContainsStringIgnoringCase('QueryException', $response->getContent());
    }

    /**
     * Full-page search redirects invalid integer filters instead of executing them (web validation).
     */
    public function test_rejects_invalid_country_id_on_full_search(): void
    {
        $response = $this->get('/records/search?country_id=abc');

        $response->assertStatus(302);
    }

    public function test_rejects_sql_injection_style_rcc_on_full_search(): void
    {
        $response = $this->get('/records/search?rcc=1 OR 1=1');

        $response->assertStatus(302);
    }
}
