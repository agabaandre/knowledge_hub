<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;
use PhpOffice\PhpWord\Settings as WordSettings;
use Symfony\Component\Process\Process;

class OfficeDocumentToPdfService
{
    /** Office formats converted to PDF when the feature is enabled. */
    public const CONVERTIBLE_EXTENSIONS = [
        'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
        'odt', 'ods', 'odp', 'rtf',
    ];

    public function isEnabled(): bool
    {
        if (! function_exists('office_documents_to_pdf_enabled')) {
            return true;
        }

        return office_documents_to_pdf_enabled();
    }

    public function isConvertibleExtension(string $extension): bool
    {
        return in_array(strtolower($extension), self::CONVERTIBLE_EXTENSIONS, true);
    }

    /**
     * When enabled, convert a stored office file to PDF and remove the original.
     *
     * @return array{absolute_path: string, extension: string, display_filename: string}|null
     */
    public function replaceStoredFileWithPdfIfEnabled(
        string $absolutePath,
        string $extension,
        string $displayFilename
    ): ?array {
        try {
            if (! $this->isEnabled()) {
                return null;
            }

            $extension = strtolower($extension);
            if (! $this->isConvertibleExtension($extension)) {
                return null;
            }

            $pdfPath = $this->convertToPdf($absolutePath);
            if (! $pdfPath || ! is_file($pdfPath) || filesize($pdfPath) === 0) {
                return null;
            }

            if (is_file($absolutePath) && $absolutePath !== $pdfPath) {
                @unlink($absolutePath);
            }

            $stem = pathinfo($displayFilename, PATHINFO_FILENAME);
            if ($stem === '' || $stem === '.') {
                $stem = pathinfo($absolutePath, PATHINFO_FILENAME);
            }

            return [
                'absolute_path' => $pdfPath,
                'extension' => 'pdf',
                'display_filename' => $stem.'.pdf',
            ];
        } catch (\Throwable $e) {
            Log::warning('OfficeDocumentToPdfService: replaceStoredFileWithPdfIfEnabled failed', [
                'source' => $absolutePath,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Convert an office document to PDF next to the source file (same basename, .pdf).
     *
     * @return string|null Absolute path to the PDF, or null on failure
     */
    public function convertToPdf(string $sourceAbsolutePath): ?string
    {
        try {
            if (!is_file($sourceAbsolutePath) || !is_readable($sourceAbsolutePath)) {
                return null;
            }

            $ext = strtolower(pathinfo($sourceAbsolutePath, PATHINFO_EXTENSION));
            if (!$this->isConvertibleExtension($ext)) {
                return null;
            }

            // XXE guard: refuse any office archive whose XML parts declare a DTD or entity.
            if (! \App\Support\OfficeXmlSecurity::isSafe($sourceAbsolutePath, $ext)) {
                Log::warning('OfficeDocumentToPdfService: document rejected by XML security scan', [
                    'source' => $sourceAbsolutePath,
                ]);

                return null;
            }

            $outDir = dirname($sourceAbsolutePath);
            $stem = pathinfo($sourceAbsolutePath, PATHINFO_FILENAME);
            $targetPdf = $outDir . DIRECTORY_SEPARATOR . $stem . '.pdf';

            if ($this->convertUsingLibreOffice($sourceAbsolutePath, $outDir) && is_file($targetPdf) && filesize($targetPdf) > 0) {
                return $targetPdf;
            }

            if ($ext === 'docx') {
                if ($this->convertDocxUsingPhpWord($sourceAbsolutePath, $targetPdf) && is_file($targetPdf) && filesize($targetPdf) > 0) {
                    return $targetPdf;
                }
            }

            Log::info('OfficeDocumentToPdfService: conversion failed (original file kept)', [
                'source' => $sourceAbsolutePath,
                'extension' => $ext,
            ]);

            return null;
        } catch (\Throwable $e) {
            Log::warning('OfficeDocumentToPdfService: convertToPdf failed', [
                'source' => $sourceAbsolutePath,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function convertUsingLibreOffice(string $sourcePath, string $outputDirectory): bool
    {
        $binary = $this->resolveLibreOfficeBinary();
        if ($binary === null) {
            Log::debug('OfficeDocumentToPdfService: LibreOffice (soffice) not found; skipping LO conversion');

            return false;
        }

        if (!is_dir($outputDirectory) || !is_writable($outputDirectory)) {
            return false;
        }

        // Fresh throwaway profile per conversion: no shared state, no macros,
        // and LibreOffice cannot read another conversion's documents. Network
        // egress for soffice should additionally be blocked at deploy level
        // (systemd IPAddressDeny / container without network).
        $profileDir = rtrim(sys_get_temp_dir(), '/').'/lo_profile_'.bin2hex(random_bytes(8));

        try {
            $process = new Process([
                $binary,
                '--headless',
                '--nologo',
                '--norestore',
                '--nolockcheck',
                '--nofirststartwizard',
                '-env:UserInstallation=file://'.$profileDir,
                '--convert-to',
                'pdf',
                '--outdir',
                $outputDirectory,
                $sourcePath,
            ]);
            $process->setTimeout(120);
            $process->run();

            if (!$process->isSuccessful()) {
                Log::debug('LibreOffice convert stderr', [
                    'error' => $process->getErrorOutput(),
                    'output' => $process->getOutput(),
                ]);
            }

            return $process->isSuccessful();
        } catch (\Throwable $e) {
            Log::warning('LibreOffice process failed: ' . $e->getMessage());

            return false;
        } finally {
            $this->removeThrowawayProfile($profileDir);
        }
    }

    private function removeThrowawayProfile(string $profileDir): void
    {
        if (! str_contains($profileDir, '/lo_profile_') || ! is_dir($profileDir)) {
            return;
        }

        try {
            $items = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($profileDir, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($items as $item) {
                $item->isDir() ? @rmdir($item->getPathname()) : @unlink($item->getPathname());
            }
            @rmdir($profileDir);
        } catch (\Throwable $e) {
            Log::debug('Could not remove LibreOffice throwaway profile', ['dir' => $profileDir]);
        }
    }

    private function resolveLibreOfficeBinary(): ?string
    {
        $configured = config('services.libreoffice.binary');
        if (!empty($configured) && is_executable($configured)) {
            return $configured;
        }

        $candidates = [
            '/usr/bin/soffice',
            '/usr/local/bin/soffice',
            '/opt/homebrew/bin/soffice',
        ];

        if (PHP_OS_FAMILY === 'Darwin') {
            $candidates[] = '/Applications/LibreOffice.app/Contents/MacOS/soffice';
        }

        foreach ($candidates as $c) {
            if (is_executable($c)) {
                return $c;
            }
        }

        $out = @shell_exec('command -v soffice 2>/dev/null');
        if ($out) {
            $p = trim($out);
            if ($p !== '' && is_executable($p)) {
                return $p;
            }
        }

        return null;
    }

    private function convertDocxUsingPhpWord(string $sourcePath, string $targetPdf): bool
    {
        try {
            if (!class_exists(WordIOFactory::class)) {
                return false;
            }

            $mpdfBase = base_path('vendor/mpdf/mpdf');
            if (!is_dir($mpdfBase)) {
                return false;
            }

            WordSettings::setPdfRendererPath($mpdfBase);
            WordSettings::setPdfRendererName(WordSettings::PDF_RENDERER_MPDF);

            $phpWord = WordIOFactory::load($sourcePath);
            $writer = WordIOFactory::createWriter($phpWord, 'PDF');
            $writer->save($targetPdf);

            return true;
        } catch (\Throwable $e) {
            Log::warning('PhpWord docx to PDF failed: ' . $e->getMessage());

            return false;
        }
    }
}
