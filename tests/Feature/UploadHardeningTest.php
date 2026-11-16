<?php

namespace Tests\Feature;

use App\Support\SafeUpload;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/** Regression tests for the Nov 2026 upload probes. */
class UploadHardeningTest extends TestCase
{
    public function test_double_extension_docx_jpg_is_rejected(): void
    {
        $file = UploadedFile::fake()->createWithContent(
            'briefing.docx.jpg',
            "\xFF\xD8\xFF\xE0".str_repeat('A', 40) // tiny fake JPEG header
        );

        $this->expectException(ValidationException::class);
        SafeUpload::extension($file, array_merge(SafeUpload::IMAGES, SafeUpload::DOCUMENTS));
    }

    public function test_tiny_fake_jpeg_is_rejected(): void
    {
        $file = UploadedFile::fake()->createWithContent('cover.jpg', "\xFF\xD8\xFF\xE0 fake");

        $this->assertFalse(SafeUpload::isAllowed($file, SafeUpload::IMAGES));
    }

    public function test_fake_pdf_without_pdf_header_is_rejected(): void
    {
        $file = UploadedFile::fake()->createWithContent('report.pdf', str_repeat('not a pdf ', 20));

        $this->assertFalse(SafeUpload::isAllowed($file, SafeUpload::DOCUMENTS));
    }

    public function test_real_pdf_with_a_page_is_accepted(): void
    {
        $pdf = "%PDF-1.4\n1 0 obj<</Type /Catalog /Pages 2 0 R>>endobj\n"
            ."2 0 obj<</Type /Pages /Kids [3 0 R] /Count 1>>endobj\n"
            ."3 0 obj<</Type /Page /Parent 2 0 R /MediaBox [0 0 612 792]>>endobj\n"
            ."xref\n0 4\ntrailer<</Size 4 /Root 1 0 R>>\nstartxref\n9\n%%EOF\n";
        $file = UploadedFile::fake()->createWithContent('paper.pdf', $pdf);

        $this->assertSame('pdf', SafeUpload::extension($file, SafeUpload::DOCUMENTS));
    }

    public function test_xxe_docx_is_rejected_at_upload(): void
    {
        $zipPath = tempnam(sys_get_temp_dir(), 'up').'.docx';
        $zip = new \ZipArchive();
        $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="xml" ContentType="application/xml"/></Types>');
        $zip->addFromString(
            'word/document.xml',
            '<?xml version="1.0"?><!DOCTYPE doc [<!ENTITY xxe SYSTEM "file:///etc/passwd">]><w:document><w:body><w:p>&xxe;</w:p></w:body></w:document>'
        );
        $zip->close();

        $file = new UploadedFile($zipPath, 'innocent.docx', null, null, true);

        $this->assertFalse(SafeUpload::isAllowed($file, SafeUpload::DOCUMENTS));
        @unlink($zipPath);
    }
}
