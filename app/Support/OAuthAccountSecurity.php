<?php

namespace App\Support;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Hardening for OAuth redirect/callback flows (CSRF via Socialite state, injection-safe errors, avatar URLs).
 */
final class OAuthAccountSecurity
{
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
}
