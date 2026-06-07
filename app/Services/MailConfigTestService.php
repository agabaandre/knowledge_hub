<?php

namespace App\Services;

use PHPMailer\PHPMailer\Exception as MailerException;
use PHPMailer\PHPMailer\PHPMailer;

class MailConfigTestService
{
    /**
     * @param  array<string, mixed>  $payload
     * @return array{ok: bool, message?: string, error?: string}
     */
    public function test(array $payload): array
    {
        $driver = (string) ($payload['mail_mailer'] ?? $payload['email_driver'] ?? 'exchange');

        if ($driver === 'log') {
            return ['ok' => true, 'message' => 'Log driver selected — emails will be written to the application log.'];
        }

        if ($driver === 'exchange') {
            return $this->testExchange($payload);
        }

        return $this->testSmtp($payload);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array{ok: bool, message?: string, error?: string}
     */
    public function testAndSend(array $payload, ?string $recipient = null): array
    {
        $connection = $this->test($payload);
        if (! ($connection['ok'] ?? false)) {
            return $connection;
        }

        $driver = (string) ($payload['mail_mailer'] ?? $payload['email_driver'] ?? 'exchange');
        if ($driver === 'log' || $recipient === null || trim($recipient) === '') {
            return $connection;
        }

        $this->applyTemporaryConfig($payload);

        $result = send_email((object) [
            'email' => trim($recipient),
            'subject' => 'Knowledge Hub mail test — '.now()->format('Y-m-d H:i:s'),
            'body' => '<p>This is a test message from the Knowledge Hub installer.</p>',
        ]);

        if (is_array($result) && ($result['success'] ?? false)) {
            return [
                'ok' => true,
                'message' => ($connection['message'] ?? 'Connection successful.').' Test email sent to '.$recipient.'.',
            ];
        }

        $message = is_array($result) ? ($result['message'] ?? 'Unknown error') : 'Unknown error';

        return ['ok' => false, 'error' => 'Connection succeeded but sending failed: '.$message];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array{ok: bool, message?: string, error?: string}
     */
    private function testSmtp(array $payload): array
    {
        $host = trim((string) ($payload['mail_host'] ?? ''));
        if ($host === '') {
            return ['ok' => false, 'error' => 'SMTP host is required.'];
        }

        $mail = new PHPMailer(true);
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = $host;
        $mail->Port = (int) ($payload['mail_port'] ?? 587);
        $mail->SMTPAuth = true;
        $mail->Username = (string) ($payload['mail_username'] ?? '');
        $mail->Password = (string) ($payload['mail_password'] ?? '');
        $encryption = (string) ($payload['mail_encryption'] ?? 'tls');
        $mail->SMTPSecure = $encryption === 'none' ? '' : $encryption;

        try {
            $mail->smtpConnect();
            $mail->smtpClose();

            return ['ok' => true, 'message' => 'SMTP connection successful.'];
        } catch (MailerException $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array{ok: bool, message?: string, error?: string}
     */
    private function testExchange(array $payload): array
    {
        $tenantId = trim((string) ($payload['exchange_tenant_id'] ?? ''));
        $clientId = trim((string) ($payload['exchange_client_id'] ?? ''));
        $clientSecret = (string) ($payload['exchange_client_secret'] ?? '');

        if ($tenantId === '' || $clientId === '' || $clientSecret === '') {
            return ['ok' => false, 'error' => 'Tenant ID, client ID, and client secret are required.'];
        }

        $authMethod = (string) ($payload['exchange_auth_method'] ?? 'client_credentials');
        $redirectUri = (string) ($payload['exchange_redirect_uri'] ?? rtrim((string) config('app.url'), '/').'/auth/microsoft/callback');
        $scope = (string) ($payload['exchange_scope'] ?? 'https://graph.microsoft.com/.default');

        try {
            $service = new ExchangeEmailService(
                $tenantId,
                $clientId,
                $clientSecret,
                $redirectUri,
                $scope,
                $authMethod
            );

            if (! $service->isConfigured()) {
                return ['ok' => false, 'error' => 'Exchange credentials are incomplete.'];
            }

            if ($authMethod === ExchangeEmailService::AUTH_CLIENT_CREDENTIALS) {
                $service->getClientCredentialsToken();
            } elseif (! $service->hasValidToken()) {
                return [
                    'ok' => false,
                    'error' => 'Authorization code flow requires an authenticated Microsoft account. Use client credentials for unattended sending.',
                ];
            }

            return ['ok' => true, 'message' => 'Microsoft Graph authentication successful.'];
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function applyTemporaryConfig(array $payload): void
    {
        $driver = (string) ($payload['mail_mailer'] ?? $payload['email_driver'] ?? 'exchange');
        $fromName = (string) ($payload['mail_from_name'] ?? config('app.name', 'Knowledge Hub'));

        config([
            'emails.driver' => $driver === 'log' ? 'smtp' : $driver,
            'emails.host' => (string) ($payload['mail_host'] ?? ''),
            'emails.port' => (string) ($payload['mail_port'] ?? '587'),
            'emails.username' => (string) ($payload['mail_username'] ?? ''),
            'emails.password' => (string) ($payload['mail_password'] ?? ''),
            'emails.smtp_secure' => (string) ($payload['mail_encryption'] ?? 'tls') ?: 'tls',
            'emails.sender' => $fromName,
            'exchange-email.tenant_id' => (string) ($payload['exchange_tenant_id'] ?? ''),
            'exchange-email.client_id' => (string) ($payload['exchange_client_id'] ?? ''),
            'exchange-email.client_secret' => (string) ($payload['exchange_client_secret'] ?? ''),
            'exchange-email.redirect_uri' => (string) ($payload['exchange_redirect_uri'] ?? rtrim((string) config('app.url'), '/').'/auth/microsoft/callback'),
            'exchange-email.scope' => (string) ($payload['exchange_scope'] ?? 'https://graph.microsoft.com/.default'),
            'exchange-email.auth_method' => (string) ($payload['exchange_auth_method'] ?? 'client_credentials'),
        ]);
    }
}
