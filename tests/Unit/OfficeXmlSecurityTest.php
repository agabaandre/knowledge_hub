<?php

namespace Tests\Unit;

use App\Support\OfficeXmlSecurity;
use Tests\TestCase;
use ZipArchive;

class OfficeXmlSecurityTest extends TestCase
{
    private function makeDocx(string $documentXml): string
    {
        $path = tempnam(sys_get_temp_dir(), 'docx').'.docx';
        $zip = new ZipArchive();
        $zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"/>');
        $zip->addFromString('_rels/.rels', '<?xml version="1.0"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"/>');
        $zip->addFromString('word/document.xml', $documentXml);
        $zip->close();

        return $path;
    }

    public function test_xxe_docx_with_external_entity_is_rejected(): void
    {
        $path = $this->makeDocx(
            '<?xml version="1.0"?><!DOCTYPE doc [<!ENTITY xxe SYSTEM "file:///etc/passwd">]>'
            .'<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"><w:body><w:p>&xxe;</w:p></w:body></w:document>'
        );

        $this->assertFalse(OfficeXmlSecurity::isSafe($path, 'docx'));
        @unlink($path);
    }

    public function test_clean_docx_passes(): void
    {
        $path = $this->makeDocx(
            '<?xml version="1.0"?><w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"><w:body><w:p>hello</w:p></w:body></w:document>'
        );

        $this->assertTrue(OfficeXmlSecurity::isSafe($path, 'docx'));
        @unlink($path);
    }

    public function test_corrupt_archive_fails_closed(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'docx').'.docx';
        file_put_contents($path, 'this is not a zip at all');

        $this->assertFalse(OfficeXmlSecurity::isSafe($path, 'docx'));
        @unlink($path);
    }

    public function test_conversion_service_refuses_xxe_documents(): void
    {
        $path = $this->makeDocx(
            '<?xml version="1.0"?><!DOCTYPE doc [<!ENTITY xxe SYSTEM "file:///etc/passwd">]>'
            .'<w:document><w:body><w:p>&xxe;</w:p></w:body></w:document>'
        );

        $this->assertNull(app(\App\Services\OfficeDocumentToPdfService::class)->convertToPdf($path));
        @unlink($path);
    }
}
