<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendMailJob;
use Illuminate\Support\Str;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test verification email';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $email = $this->argument('email') ?? 'agabaandre@gmail.com';
        $token = Str::random(10);
        
        $this->info("Sending verification email to: {$email}");
        $this->info("Verification token: {$token}");
        
        // Clear cache before rendering to avoid permission issues
        try {
            \Artisan::call('view:clear');
        } catch (\Exception $e) {
            // Ignore cache clear errors
        }
        
        // Build verification URL
        $verifyUrl = route('account_verify') . "?t={$token}";
        
        // Render the email view (with fallback to direct HTML if cache fails)
        try {
            $body = view('emails.email_verification', ['token' => $token])->render();
        } catch (\Exception $e) {
            // If view rendering fails due to cache, build HTML directly
            $this->warn("View rendering failed, using direct HTML email body: " . $e->getMessage());
            
            // Build complete email HTML matching the layout template
            $siteUrl = url('/');
            $logoUrl = 'https://africacdc.org/wp-content/uploads/2020/02/AfricaCDC_Logo.png';
            $fromEmail = config('emails.username', 'support@africacdc.org');
            
            $body = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 0; background-color: #f4f6f9; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Arial, sans-serif;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f4f6f9;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="max-width: 600px; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #119A48 0%, #0e7a3a 100%); padding: 30px 40px; border-radius: 8px 8px 0 0;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td align="center">
                                        <a href="' . $siteUrl . '" style="text-decoration: none;">
                                            <img src="' . $logoUrl . '" alt="Africa CDC Knowledge Hub" style="max-width: 200px; height: auto;" />
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="padding-bottom: 30px;">
                                        <h1 style="margin: 0; font-size: 28px; font-weight: 700; color: #1e293b; line-height: 1.3;">
                                            Verify Your Email Address
                                        </h1>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 25px;">
                                        <p style="margin: 0; font-size: 16px; color: #475569; line-height: 1.6;">
                                            Thank you for registering with Africa CDC Knowledge Hub! To complete your registration and start accessing our resources, please verify your email address by clicking the button below.
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 30px;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td align="center">
                                                    <a href="' . $verifyUrl . '" style="display: inline-block; padding: 14px 32px; background-color: #119A48; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 16px; line-height: 1.5;">
                                                        Verify Email Address
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 20px;">
                                        <p style="margin: 0; font-size: 14px; color: #64748b; line-height: 1.6;">
                                            If the button doesn\'t work, you can copy and paste this link into your browser:
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 30px;">
                                        <p style="margin: 0; padding: 12px; background-color: #f1f5f9; border-radius: 4px; border-left: 3px solid #119A48;">
                                            <a href="' . $verifyUrl . '" style="color: #119A48; text-decoration: none; word-break: break-all; font-size: 13px; line-height: 1.5;">
                                                ' . $verifyUrl . '
                                            </a>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-top: 20px; border-top: 1px solid #e2e8f0;">
                                        <p style="margin: 0; font-size: 13px; color: #94a3b8; line-height: 1.6;">
                                            <strong>Important:</strong> This verification link will expire in 24 hours. If you didn\'t create an account with us, please ignore this email.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 30px 40px; border-radius: 0 0 8px 8px; border-top: 1px solid #e2e8f0;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td align="center" style="padding-bottom: 20px;">
                                        <p style="margin: 0; font-size: 14px; color: #64748b; line-height: 1.6;">
                                            <strong style="color: #119A48;">Africa CDC Knowledge Hub</strong><br>
                                            Comprehensive knowledge repository for public health resources
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="border-top: 1px solid #e2e8f0; padding-top: 20px;">
                                        <p style="margin: 0; font-size: 12px; color: #94a3b8; line-height: 1.5;">
                                            This email was sent by Africa CDC Knowledge Hub<br>
                                            © ' . date('Y') . ' Africa CDC. All rights reserved.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
        }
        
        $mail = [
            'email' => $email,
            'subject' => 'Account Verification - Africa CDC Knowledge Hub',
            'body' => $body
        ];
        
        // Try to send synchronously first (for testing)
        try {
            $request = (object) $mail;
            $result = send_email($request);
            
            if (is_array($result) && isset($result['success']) && $result['success']) {
                $this->info("✅ Email sent successfully!");
            } else {
                $errorMsg = is_array($result) ? ($result['message'] ?? 'Unknown error') : 'Unknown error';
                $this->error("❌ Email sending failed: " . $errorMsg);
                // Try queue as fallback
                $this->info("Attempting to queue email...");
                SendMailJob::dispatch($mail)->onQueue('default');
                $this->info("✅ Email queued successfully! (Check queue worker)");
            }
            
            $this->info("\nVerification URL: " . route('account_verify') . "?t={$token}");
            return 0;
        } catch (\Exception $e) {
            $this->error("❌ Failed to send email: " . $e->getMessage());
            return 1;
        }
    }
}
