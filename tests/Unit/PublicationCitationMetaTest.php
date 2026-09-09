<?php

namespace Tests\Unit;

use App\Support\PublicationCitationMeta;
use Tests\TestCase;

class PublicationCitationMetaTest extends TestCase
{
    public function test_highwire_tags_include_required_fields_and_doi(): void
    {
        $pub = (object) [
            'title' => 'Malaria surveillance in East Africa',
            'associated_authors' => 'Ada Lovelace, Alan Turing',
            'author' => (object) ['name' => 'Africa CDC', 'is_organsiation' => 'yes'],
            'year_published' => 2024,
            'doi' => 'https://doi.org/10.1234/abcd',
            'issn' => '1234-5678',
            'isbn' => '',
            'journal_name' => 'Lancet Global Health',
            'journal_volume' => '12',
            'journal_issue' => '3',
            'journal_pages' => '45-52',
            'publisher' => 'Elsevier',
            'publication_pdf_url' => 'https://example.test/file.pdf',
            'id' => 1,
            'slug' => 'malaria-surveillance',
            'created_at' => null,
        ];

        $tags = PublicationCitationMeta::highwireTags($pub);
        $byName = collect($tags)->groupBy('name')->map(fn ($g) => $g->pluck('content')->all());

        $this->assertSame(['Malaria surveillance in East Africa'], $byName['citation_title']);
        $this->assertContains('Lovelace, Ada', $byName['citation_author']);
        $this->assertContains('Turing, Alan', $byName['citation_author']);
        $this->assertContains('Africa CDC', $byName['citation_author']);
        $this->assertSame(['2024'], $byName['citation_publication_date']);
        $this->assertSame(['10.1234/abcd'], $byName['citation_doi']);
        $this->assertSame(['1234-5678'], $byName['citation_issn']);
        $this->assertSame(['Lancet Global Health'], $byName['citation_journal_title']);
        $this->assertSame(['12'], $byName['citation_volume']);
        $this->assertSame(['3'], $byName['citation_issue']);
        $this->assertSame(['45'], $byName['citation_firstpage']);
        $this->assertSame(['52'], $byName['citation_lastpage']);
        $this->assertSame(['Elsevier'], $byName['citation_publisher']);
        $this->assertSame(['https://example.test/file.pdf'], $byName['citation_pdf_url']);
        $this->assertArrayHasKey('citation_abstract_html_url', $byName->all());
        $this->assertArrayNotHasKey('citation_isbn', $byName->all());
    }

    public function test_highwire_tags_empty_without_title_or_authors(): void
    {
        $this->assertSame([], PublicationCitationMeta::highwireTags((object) [
            'title' => '',
            'associated_authors' => '',
            'author' => null,
        ]));
        $this->assertSame([], PublicationCitationMeta::highwireTags((object) [
            'title' => 'Only title',
            'associated_authors' => '',
            'author' => null,
        ]));
    }

    public function test_citation_links_prefer_doi_and_fallback_to_scholar_title(): void
    {
        $withDoi = PublicationCitationMeta::citationLinks((object) [
            'title' => 'Test paper',
            'doi' => '10.5555/xyz',
        ]);
        $ids = array_column($withDoi, 'id');
        $this->assertSame(['google_scholar', 'crossref', 'semantic_scholar', 'openalex', 'dimensions', 'doi'], $ids);
        $scholarUrl = collect($withDoi)->firstWhere('id', 'google_scholar')['url'];
        $this->assertStringContainsString(rawurlencode('10.5555/xyz'), $scholarUrl);

        $noDoi = PublicationCitationMeta::citationLinks((object) [
            'title' => 'Untitled study on cholera',
            'doi' => null,
        ]);
        $this->assertCount(1, $noDoi);
        $this->assertSame('google_scholar', $noDoi[0]['id']);
        $this->assertStringContainsString(rawurlencode('Untitled study on cholera'), $noDoi[0]['url']);
    }

    public function test_show_and_meta_partials_wire_citation_tags(): void
    {
        $show = file_get_contents(resource_path('views/publications/show.blade.php'));
        $meta = file_get_contents(resource_path('views/partials/seo/meta.blade.php'));

        $this->assertStringContainsString('PublicationCitationMeta::highwireTags', $show);
        $this->assertStringContainsString('PublicationCitationMeta::citationLinks', $show);
        $this->assertStringContainsString('publicationCitationTags', $meta);
        $this->assertStringContainsString('Highwire Press', $meta);
        $this->assertStringContainsString('Track citations', $show);
    }
}
