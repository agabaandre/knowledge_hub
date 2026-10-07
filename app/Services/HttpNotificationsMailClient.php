<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Africa CDC Email Server (notifications.africacdc.org) — client-credentials JWT + /integrations/send.
 *
 * Same transport used by Staff Portal ("Africa CDC SERVER" / HTTP mail).
 *
 * @see https://notifications.africacdc.org/api/documentation
 */
class HttpNotificationsMailClient
{
    private const MAX_FILES = 10;

    private const MAX_BYTES_EACH = 5 * 1024 * 1024;

    private const MAX_BYTES_TOTAL = 15 * 1024 * 1024;

    /**
     * @param  string|array<int, string>  $to
     * @param  array<int, string>  $cc
     * @param  array<int, string>  $bcc
     * @param  list<array<string, mixed>|string>  $attachments
     */
    public function send(
        string|array $to,
        string $subject,
        string $htmlBody,
        array $cc = [],
        array $bcc = [],
        array $attachments = [],
    ): void {
        $base = rtrim((string) config(
            'emails.http.base_url',
            env('MAIL_HTTP_BASE_URL', 'https://notifications.africacdc.org/api/v1')
        ), '/');
        $token = $this->bearerToken($base);
        $recipients = is_array($to) ? $to : [$to];
        $apiAttachments = $this->normalizeAttachments($attachments);

        foreach ($recipients as $recipient) {
            $payload = [
                'to' => $recipient,
                'subject' => $subject,
                'body' => $htmlBody,
                'is_html' => true,
            ];
            if ($cc !== []) {
                $payload['cc'] = array_values($cc);
            }
            if ($bcc !== []) {
                $payload['bcc'] = array_values($bcc);
            }
            if ($apiAttachments !== []) {
                $payload['attachments'] = $apiAttachments;
            }

            $res = Http::withToken($token)
                ->acceptJson()
                ->asJson()
                ->timeout(60)
                ->post($base.'/integrations/send', $payload);

            if (! $res->successful()) {
                throw new RuntimeException(
                    'HTTP notifications send failed ('.$res->status().'): '.$res->body()
                );
            }
        }
    }

    public function isConfigured(): bool
    {
        $clientId = (string) config('emails.http.client_id', env('MAIL_HTTP_CLIENT_ID', ''));
        $clientSecret = (string) config('emails.http.client_secret', env('MAIL_HTTP_CLIENT_SECRET', ''));

        return $clientId !== '' && $clientSecret !== '';
    }

    /**
     * Authenticate only (used by admin/installer connection tests).
     */
    public function authenticate(): string
    {
        $base = rtrim((string) config(
            'emails.http.base_url',
            env('MAIL_HTTP_BASE_URL', 'https://notifications.africacdc.org/api/v1')
        ), '/');

        return $this->bearerToken($base);
    }

    /**
     * @param  list<array<string, mixed>|string>  $attachments
     * @return list<array{filename: string, content: string, content_type: string}>
     */
    public function normalizeAttachments(array $attachments): array
    {
        $out = [];
        $total = 0;

        foreach ($attachments as $row) {
            if (count($out) >= self::MAX_FILES) {
                break;
            }

            $normalized = $this->normalizeOneAttachment($row);
            if ($normalized === null) {
                continue;
            }

            $raw = base64_decode($normalized['content'], true);
            $rawLen = is_string($raw) ? strlen($raw) : 0;
            if ($rawLen > self::MAX_BYTES_EACH) {
                throw new RuntimeException(
                    'Attachment "'.$normalized['filename'].'" exceeds 5 MB limit.'
                );
            }
            $total += $rawLen;
            if ($total > self::MAX_BYTES_TOTAL) {
                throw new RuntimeException('Attachments exceed 15 MB total limit.');
            }

            $out[] = $normalized;
        }

        return $out;
    }

    /**
     * @param  array<string, mixed>|string  $row
     * @return array{filename: string, content: string, content_type: string}|null
     */
    private function normalizeOneAttachment(array|string $row): ?array
    {
        if (is_string($row)) {
            return $this->fromPath($row, basename($row), null);
        }

        $filename = (string) ($row['filename'] ?? $row['name'] ?? 'attachment');
        $contentType = (string) ($row['content_type'] ?? $row['mime'] ?? 'application/octet-stream');
        if ($contentType === '') {
            $contentType = 'application/octet-stream';
        }

        if (isset($row['content_base64']) && is_string($row['content_base64']) && $row['content_base64'] !== '') {
            return [
                'filename' => $filename,
                'content' => preg_replace('/\s+/', '', $row['content_base64']) ?? $row['content_base64'],
                'content_type' => $contentType,
            ];
        }

        if (isset($row['filename'], $row['content']) && is_string($row['content']) && ! isset($row['name'])) {
            $b64 = preg_replace('/\s+/', '', $row['content']) ?? $row['content'];
            $decoded = base64_decode($b64, true);
            if ($decoded !== false) {
                return [
                    'filename' => $filename,
                    'content' => $b64,
                    'content_type' => $contentType,
                ];
            }
        }

        if (isset($row['content']) && is_string($row['content']) && $row['content'] !== '') {
            return [
                'filename' => $filename,
                'content' => base64_encode($row['content']),
                'content_type' => $contentType,
            ];
        }

        $path = (string) ($row['path'] ?? '');
        if ($path !== '') {
            return $this->fromPath($path, $filename, $contentType);
        }

        return null;
    }

    /**
     * @return array{filename: string, content: string, content_type: string}|null
     */
    private function fromPath(string $path, string $filename, ?string $contentType): ?array
    {
        if ($path === '') {
            return null;
        }
        if (! is_readable($path) && is_readable(storage_path('app/public/'.$path))) {
            $path = storage_path('app/public/'.$path);
        }
        if (! is_readable($path)) {
            return null;
        }
        $bytes = file_get_contents($path);
        if ($bytes === false) {
            return null;
        }
        $mime = $contentType && $contentType !== 'application/octet-stream'
            ? $contentType
            : (mime_content_type($path) ?: 'application/octet-stream');

        return [
            'filename' => $filename !== '' && $filename !== 'attachment' ? $filename : basename($path),
            'content' => base64_encode($bytes),
            'content_type' => $mime,
        ];
    }

    private function bearerToken(string $base): string
    {
        $clientId = (string) config('emails.http.client_id', env('MAIL_HTTP_CLIENT_ID', ''));
        $clientSecret = (string) config('emails.http.client_secret', env('MAIL_HTTP_CLIENT_SECRET', ''));
        if ($clientId === '' || $clientSecret === '') {
            throw new RuntimeException(
                'HTTP mail is not configured. Set MAIL_HTTP_CLIENT_ID and MAIL_HTTP_CLIENT_SECRET (or Admin → Configure → Email).'
            );
        }

        $cacheKey = 'mail_http_jwt:'.hash('sha256', $base.'|'.$clientId);

        $cached = Cache::get($cacheKey);
        if (is_string($cached) && $cached !== '') {
            return $cached;
        }

        $res = Http::acceptJson()
            ->asJson()
            ->timeout(30)
            ->post($base.'/integrations/auth/token', [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
            ]);

        if (! $res->successful()) {
            throw new RuntimeException(
                'HTTP notifications auth failed ('.$res->status().'): '.$res->body()
            );
        }

        $token = (string) ($res->json('token') ?? $res->json('access_token') ?? '');
        if ($token === '') {
            throw new RuntimeException('HTTP notifications auth returned no token.');
        }

        $ttl = (int) ($res->json('expires_in') ?? 86400);
        Cache::put($cacheKey, $token, max(60, $ttl - 120));

        return $token;
    }
}
