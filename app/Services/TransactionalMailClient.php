<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * HTTP API mail providers (SendGrid, Mailgun, Postmark, Mailjet) — same shapes as Staff Portal.
 */
class TransactionalMailClient
{
    /**
     * @param  string|array<int, string>  $to
     */
    public function send(string $driver, string|array $to, string $subject, string $htmlBody): void
    {
        $recipients = is_array($to) ? array_values(array_filter($to)) : [trim($to)];
        if ($recipients === []) {
            throw new RuntimeException('No recipients provided.');
        }

        $fromAddress = (string) (config('emails.from_address') ?: config('emails.username') ?: '');
        $fromName = (string) (config('emails.sender') ?: config('app.name', 'Knowledge Hub'));
        if ($fromAddress === '') {
            throw new RuntimeException('From address is required for '.$driver.' mail.');
        }

        match ($driver) {
            'sendgrid' => $this->sendSendgrid($recipients, $subject, $htmlBody, $fromAddress, $fromName),
            'mailgun' => $this->sendMailgun($recipients, $subject, $htmlBody, $fromAddress, $fromName),
            'postmark' => $this->sendPostmark($recipients, $subject, $htmlBody, $fromAddress, $fromName),
            'mailjet' => $this->sendMailjet($recipients, $subject, $htmlBody, $fromAddress, $fromName),
            'log' => $this->sendLog($recipients, $subject, $htmlBody),
            default => throw new RuntimeException("Unsupported transactional mail driver: {$driver}"),
        };
    }

    public function isConfigured(string $driver): bool
    {
        return match ($driver) {
            'sendgrid', 'postmark' => trim((string) config('emails.api.key', '')) !== '',
            'mailgun' => trim((string) config('emails.api.key', '')) !== ''
                && trim((string) config('emails.api.domain', '')) !== '',
            'mailjet' => trim((string) config('emails.api.key', '')) !== ''
                && trim((string) config('emails.api.secret', '')) !== '',
            'log' => true,
            default => false,
        };
    }

    /**
     * Lightweight connectivity check (credential presence + optional auth ping).
     *
     * @return array{ok: bool, message?: string, error?: string}
     */
    public function test(string $driver): array
    {
        if ($driver === 'log') {
            return ['ok' => true, 'message' => 'Log driver selected — emails will be written to the application log.'];
        }

        if (! $this->isConfigured($driver)) {
            return ['ok' => false, 'error' => ucfirst($driver).' credentials are incomplete.'];
        }

        return ['ok' => true, 'message' => ucfirst($driver).' credentials look complete. Send a test message to verify delivery.'];
    }

    /**
     * @param  list<string>  $to
     */
    private function sendLog(array $to, string $subject, string $htmlBody): void
    {
        Log::info('TransactionalMailClient log transport', [
            'to' => $to,
            'subject' => $subject,
            'html_length' => strlen($htmlBody),
        ]);
    }

    /**
     * @param  list<string>  $to
     */
    private function sendSendgrid(array $to, string $subject, string $htmlBody, string $fromAddress, string $fromName): void
    {
        $payload = [
            'personalizations' => [['to' => array_map(fn ($e) => ['email' => $e], $to)]],
            'from' => ['email' => $fromAddress, 'name' => $fromName],
            'subject' => $subject,
            'content' => [['type' => 'text/html', 'value' => $htmlBody]],
        ];

        $base = rtrim((string) (config('emails.api.base_url') ?: 'https://api.sendgrid.com/v3'), '/');
        $res = Http::withToken((string) config('emails.api.key'))
            ->acceptJson()
            ->asJson()
            ->timeout(60)
            ->post($base.'/mail/send', $payload);

        if (! $res->successful()) {
            throw new RuntimeException('SendGrid send failed ('.$res->status().'): '.$res->body());
        }
    }

    /**
     * @param  list<string>  $to
     */
    private function sendMailgun(array $to, string $subject, string $htmlBody, string $fromAddress, string $fromName): void
    {
        $domain = (string) config('emails.api.domain', '');
        $region = ((string) config('emails.api.region', 'us')) === 'eu' ? 'api.eu.mailgun.net' : 'api.mailgun.net';

        $res = Http::withBasicAuth('api', (string) config('emails.api.key'))
            ->asMultipart()
            ->timeout(60)
            ->attach('from', "{$fromName} <{$fromAddress}>")
            ->attach('to', implode(',', $to))
            ->attach('subject', $subject)
            ->attach('html', $htmlBody)
            ->post("https://{$region}/v3/{$domain}/messages");

        if (! $res->successful()) {
            throw new RuntimeException('Mailgun send failed ('.$res->status().'): '.$res->body());
        }
    }

    /**
     * @param  list<string>  $to
     */
    private function sendPostmark(array $to, string $subject, string $htmlBody, string $fromAddress, string $fromName): void
    {
        $payload = [
            'From' => "{$fromName} <{$fromAddress}>",
            'To' => implode(',', $to),
            'Subject' => $subject,
            'HtmlBody' => $htmlBody,
            'MessageStream' => config('emails.api.message_stream') ?: 'outbound',
        ];

        $res = Http::withHeaders([
            'X-Postmark-Server-Token' => (string) config('emails.api.key'),
            'Accept' => 'application/json',
        ])->timeout(60)->post('https://api.postmarkapp.com/email', $payload);

        if (! $res->successful()) {
            throw new RuntimeException('Postmark send failed ('.$res->status().'): '.$res->body());
        }
    }

    /**
     * @param  list<string>  $to
     */
    private function sendMailjet(array $to, string $subject, string $htmlBody, string $fromAddress, string $fromName): void
    {
        $payload = [
            'Messages' => [[
                'From' => ['Email' => $fromAddress, 'Name' => $fromName],
                'To' => array_map(fn ($e) => ['Email' => $e], $to),
                'Subject' => $subject,
                'HTMLPart' => $htmlBody,
            ]],
        ];

        $res = Http::withBasicAuth((string) config('emails.api.key'), (string) config('emails.api.secret'))
            ->acceptJson()
            ->asJson()
            ->timeout(60)
            ->post('https://api.mailjet.com/v3.1/send', $payload);

        if (! $res->successful()) {
            throw new RuntimeException('Mailjet send failed ('.$res->status().'): '.$res->body());
        }
    }
}
