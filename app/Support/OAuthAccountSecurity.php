<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Hardening for OAuth redirect/callback flows (CSRF via Socialite state, injection-safe errors, avatar URLs).
 */
final class OAuthAccountSecurity
{
    /**
     * Whether password-registered accounts are allowed to switch to social sign-in.
     * Defaults to true when setting is unavailable.
     */
    public static function allowPasswordAccountsToUseSocialLogin(): bool
    {
        try {
            $settings = settings();
            if (! $settings || ! isset($settings->allow_email_password_accounts_social_login)) {
                return true;
            }

            return (bool) $settings->allow_email_password_accounts_social_login;
        } catch (\Throwable $e) {
            return true;
        }
    }

    /**
     * If the IdP returned an OAuth error, redirect to login with a generic message (no reflected provider text).
     */
    public static function redirectIfOAuthDenied(Request $request, string $provider): ?RedirectResponse
    {
        if (! $request->filled('error')) {
            return null;
        }

        $code = (string) $request->query('error', '');
        $desc = (string) $request->query('error_description', '');

        Log::warning('OAuth provider returned error', [
            'provider' => $provider,
            'error' => mb_substr($code, 0, 200),
            'error_description' => mb_substr(preg_replace('/\s+/', ' ', strip_tags($desc)), 0, 500),
            'ip' => $request->ip(),
        ]);

        return redirect('/login')
            ->with('alert_class', 'danger')
            ->with('alert', 'Sign-in was cancelled or could not be completed. Please try again.');
    }

    /**
     * Normalize and validate email from an OAuth provider before DB use.
     */
    public static function normalizedProviderEmail(?string $email): ?string
    {
        if ($email === null) {
            return null;
        }
        $email = trim(strtolower($email));
        if ($email === '' || strlen($email) > 254) {
            return null;
        }
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        return $email;
    }

    /**
     * Allow only http(s) avatar URLs for storage; blocks javascript:, data:, etc.
     */
    public static function sanitizeStoredAvatarUrl(?string $url): ?string
    {
        if ($url === null || $url === '') {
            return null;
        }
        $url = trim($url);
        if (strlen($url) > 2048) {
            return null;
        }
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return null;
        }
        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        if (! in_array($scheme, ['https', 'http'], true)) {
            return null;
        }
        if (config('app.env') === 'production' && $scheme !== 'https') {
            return null;
        }
        $host = parse_url($url, PHP_URL_HOST);
        if ($host === null || $host === '') {
            return null;
        }
        $hostLower = strtolower($host);
        foreach (['localhost', '127.0.0.1', '0.0.0.0', '::1'] as $blocked) {
            if ($hostLower === $blocked) {
                return null;
            }
        }

        return $url;
    }

    /**
     * Normalized provider key stored on users.social_provider: google | microsoft | linkedin.
     */
    public static function canonicalOAuthProvider(string $driver): string
    {
        $d = strtolower(trim($driver));
        if (str_contains($d, 'linkedin')) {
            return 'linkedin';
        }

        return match ($d) {
            'google' => 'google',
            'microsoft' => 'microsoft',
            default => $d,
        };
    }

    public static function providerDisplayName(string $canonical): string
    {
        return match (self::canonicalOAuthProvider($canonical)) {
            'google' => 'Google',
            'microsoft' => 'Microsoft',
            'linkedin' => 'LinkedIn',
            default => 'your original sign-in method',
        };
    }

    /**
     * If the account must not accept this OAuth provider, return a user-safe message; otherwise null.
     * Same email may sign in with any configured OAuth provider (Google, Microsoft, LinkedIn).
     * Password-only accounts are allowed when admin setting permits linking social login.
     */
    public static function oauthLoginDeniedMessage(?User $user, string $attemptedCanonicalProvider): ?string
    {
        if ($user === null) {
            return null;
        }

        if (! $user->is_social_login) {
            if (self::allowPasswordAccountsToUseSocialLogin()) {
                return null;
            }

            return 'This email is registered with email and password. Please sign in using your password instead of social sign-in.';
        }

        return null;
    }

    public static function allowsSameEmailCrossProviderLogin(): bool
    {
        return true;
    }
}
