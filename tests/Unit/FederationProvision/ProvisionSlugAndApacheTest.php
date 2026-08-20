<?php

namespace Tests\Unit\FederationProvision;

use App\Services\FederationProvision\ProvisionApache;
use App\Services\FederationProvision\ProvisionFilesystem;
use App\Services\FederationProvision\ProvisionSlug;
use App\Services\FederationProvision\SudoRunner;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ProvisionSlugTest extends TestCase
{
    public function test_normalizes_slug(): void
    {
        $this->assertSame('uganda', ProvisionSlug::normalize(' Uganda '));
        $this->assertSame('south-sudan', ProvisionSlug::normalize('South Sudan'));
    }

    public function test_rejects_reserved_slug(): void
    {
        $this->expectException(RuntimeException::class);
        ProvisionSlug::assertAllowed('admin', ['admin', 'api']);
    }

    public function test_database_names_are_safe(): void
    {
        $this->assertSame('khub_uganda', ProvisionSlug::databaseName('uganda'));
        $this->assertSame('khub_uganda', ProvisionSlug::databaseUsername('uganda'));
    }
}

class ProvisionApacheSnippetTest extends TestCase
{
    public function test_builds_and_strips_snippet(): void
    {
        $filesystem = $this->createMock(ProvisionFilesystem::class);
        $filesystem->method('targetPath')->willReturn('/var/www/uganda');
        $sudo = $this->createMock(SudoRunner::class);
        $apache = new ProvisionApache($sudo, $filesystem);

        $snippet = $apache->buildSnippet('uganda', '/var/www/uganda/public');
        $this->assertStringContainsString('Alias /uganda /var/www/uganda/public', $snippet);
        $this->assertStringContainsString('# BEGIN KHUB_FEDERATION_HUB /uganda', $snippet);

        $vhost = "<VirtualHost *:443>\n    ServerName khub.africacdc.org\n</VirtualHost>\n";
        $withAlias = $apache->insertSnippet($vhost, $snippet);
        $this->assertTrue($apache->aliasExists($withAlias, 'uganda'));

        $stripped = $apache->stripSnippet($withAlias, 'uganda');
        $this->assertFalse($apache->aliasExists($stripped, 'uganda'));
        $this->assertStringContainsString('</VirtualHost>', $stripped);
    }
}
