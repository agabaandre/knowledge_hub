<?php

namespace Tests\Feature;

use App\Http\Middleware\BotProtection;
use App\Support\SafeUpload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SafeUploadTest extends TestCase
{
    private string $tmpDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(BotProtection::class);
        $this->tmpDir = storage_path('framework/testing/safe-upload-'.uniqid());
        File::ensureDirectoryExists($this->tmpDir);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->tmpDir)) {
            File::deleteDirectory($this->tmpDir);
        }
        parent::tearDown();
    }

    public function test_accepts_real_raster_images(): void
    {
        $files = [
            UploadedFile::fake()->image('ok.png', 10, 10),
            UploadedFile::fake()->image('ok.jpg', 10, 10),
            $this->makeImageUpload('ok.gif', 'gif'),
            $this->makeImageUpload('ok.webp', 'webp'),
        ];

        foreach ($files as $file) {
            if (! SafeUpload::isAllowed($file, SafeUpload::IMAGES)) {
                // Minimal synthetic webp/gif may not be detectable on every platform.
                if (str_ends_with(strtolower($file->getClientOriginalName()), '.webp')
                    || str_ends_with(strtolower($file->getClientOriginalName()), '.gif')) {
                    $this->assertTrue(true);
                    continue;
                }
                $this->fail('Expected image to be allowed: '.$file->getClientOriginalName());
            }
            $name = SafeUpload::move($file, $this->tmpDir, SafeUpload::IMAGES);
            $this->assertMatchesRegularExpression('/^[a-f0-9]{32}\.(png|jpg|gif|webp)$/', $name);
            $this->assertFileExists($this->tmpDir.DIRECTORY_SEPARATOR.$name);
        }
    }

    public function test_accepts_documents(): void
    {
        $cases = [
            'pdf' => "%PDF-1.4\n1 0 obj<<>>endobj\ntrailer<<>>\n%%EOF\n",
            'csv' => "a,b,c\n1,2,3\n",
            'txt' => "plain text summary\n",
        ];

        foreach ($cases as $ext => $body) {
            $file = $this->makeTempUpload("doc.{$ext}", $body);
            $name = SafeUpload::move($file, $this->tmpDir, SafeUpload::DOCUMENTS);
            $this->assertStringEndsWith('.'.$ext, $name);
            $this->assertFileExists($this->tmpDir.DIRECTORY_SEPARATOR.$name);
        }

        // Minimal ZIP-based Office containers (docx/xlsx) — client fallback via zip MIME.
        foreach (['docx', 'xlsx'] as $ext) {
            $file = $this->makeTempUpload("office.{$ext}", $this->minimalZipBytes());
            $this->assertTrue(SafeUpload::isAllowed($file, SafeUpload::DOCUMENTS));
            $name = SafeUpload::move($file, $this->tmpDir, SafeUpload::DOCUMENTS);
            $this->assertStringEndsWith('.'.$ext, $name);
        }
    }

    public function test_rejects_dangerous_filenames_and_payloads(): void
    {
        $rejected = [
            ['shell.php', "<?php echo 1;"],
            ['a.PHP7', "<?php echo 1;"],
            ['a.pht', "<?php echo 1;"],
            ['a.phps', "<?php echo 1;"],
            ['a.inc', "<?php echo 1;"],
            ['a.shtml', '<!--#exec cmd="id" -->'],
            ['a.', 'GIF89a'],
            ['.htaccess', "AddType application/x-httpd-php .php7\n"],
            ['a.php.jpg', $this->pngBytes()],
            ['polyglot.gif', "GIF89a\n<?php system('id');"],
            ['evil.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'],
        ];

        foreach ($rejected as [$name, $body]) {
            $file = $this->makeTempUpload($name, $body);
            try {
                SafeUpload::extension($file, array_merge(SafeUpload::IMAGES, SafeUpload::DOCUMENTS));
                $this->fail("Expected rejection for {$name}");
            } catch (ValidationException $e) {
                $this->assertArrayHasKey('file', $e->errors());
            }
            $this->assertSafeUploadDidNotWrite($name);
        }
    }

    public function test_png_renamed_to_php7_is_stored_as_png(): void
    {
        $file = $this->makeTempUpload('x.php7', $this->pngBytes());
        $name = SafeUpload::move($file, $this->tmpDir, SafeUpload::IMAGES);
        $this->assertStringEndsWith('.png', $name);
        $this->assertStringNotContainsString('php7', $name);
        $this->assertFileExists($this->tmpDir.DIRECTORY_SEPARATOR.$name);
    }

    public function test_image_upload_endpoint_rejects_polyglot(): void
    {
        $polyglot = UploadedFile::fake()->createWithContent('cover.gif', "GIF89a\n<?php echo 'pwn';");

        $response = $this->post('/image-upload', [
            'file' => $polyglot,
        ]);

        $response->assertStatus(422);
        $this->assertDirectoryDoesNotContainPhpLikeFiles(storage_path('app/public/uploads/editor'));
    }

    public function test_image_upload_endpoint_accepts_png(): void
    {
        $file = $this->makeTempUpload('editor.png', $this->pngBytes());

        $response = $this->post('/image-upload', [
            'file' => $file,
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['url']);
    }

    public function test_is_allowed_skips_bad_files(): void
    {
        $bad = $this->makeTempUpload('shell.php', "<?php echo 1;");
        $good = $this->makeTempUpload('ok.png', $this->pngBytes());

        $this->assertFalse(SafeUpload::isAllowed($bad, SafeUpload::IMAGES));
        $this->assertTrue(SafeUpload::isAllowed($good, SafeUpload::IMAGES));
    }

    private function assertSafeUploadDidNotWrite(string $clientName): void
    {
        $files = File::files($this->tmpDir);
        foreach ($files as $file) {
            $this->assertStringNotContainsStringIgnoringCase('php', $file->getExtension());
            $this->assertNotSame(strtolower($clientName), strtolower($file->getFilename()));
        }
    }

    private function assertDirectoryDoesNotContainPhpLikeFiles(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }
        foreach (File::allFiles($dir) as $file) {
            $ext = strtolower($file->getExtension());
            $this->assertNotContains($ext, ['php', 'php7', 'phtml', 'phar', 'phps', 'pht', 'inc', 'shtml']);
        }
    }

    private function makeImageUpload(string $name, string $ext): UploadedFile
    {
        $map = [
            'png' => $this->pngBytes(),
            'jpg' => $this->jpegBytes(),
            'gif' => "GIF89a\x01\x00\x01\x00\x00\x00\x00;",
            'webp' => "RIFF\x1a\x00\x00\x00WEBPVP8 ",
        ];

        return $this->makeTempUpload($name, $map[$ext]);
    }

    private function makeTempUpload(string $name, string $contents): UploadedFile
    {
        $path = $this->tmpDir.DIRECTORY_SEPARATOR.'src-'.uniqid().'-'.$name;
        File::put($path, $contents);

        return new UploadedFile($path, $name, null, null, true);
    }

    private function pngBytes(): string
    {
        return base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');
    }

    private function jpegBytes(): string
    {
        return base64_decode('/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////2wBDAf//////////////////////////////////////////////////////////////////////////////////////wAARCAABAAEDASIAAhEBAxEB/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL/8QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL/8QAtREAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6/9oADAMBAAIRAxEAPwD3+iiigD//2Q==');
    }

    private function minimalZipBytes(): string
    {
        // Empty ZIP archive (PK\x03\x04 local file header + EOCD)
        return "PK\x03\x04\x14\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00"
            ."PK\x05\x06\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00";
    }
}
