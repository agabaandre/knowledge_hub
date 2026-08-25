<?php

namespace App\Support;

class SecurityHeaders
{
    /**
     * HTML / Blade / Next static pages — allow first-party assets and HTTPS CDNs
     * the portal already uses (jQuery, Font Awesome, Google Translate, Recaptcha).
     */
    public const WEB_CSP = "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https:; style-src 'self' 'unsafe-inline' https:; img-src 'self' data: blob: https:; font-src 'self' data: https:; connect-src 'self' https:; frame-src 'self' https:; media-src 'self' https: blob:; worker-src 'self' blob:; frame-ancestors 'none'; base-uri 'self'; form-action 'self'";

    /**
     * JSON API responses have no document to execute; lock down by default.
     */
    public const API_CSP = "default-src 'none'";

    /**
     * @return array<string, string>
     */
    public static function shared(): array
    {
        return [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
            'Referrer-Policy' => 'no-referrer',
            'Strict-Transport-Security' => 'max-age=31536000',
            'Permissions-Policy' => 'geolocation=(), microphone=(), camera=()',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function web(): array
    {
        return array_merge(self::shared(), [
            'Content-Security-Policy' => self::WEB_CSP,
        ]);
    }

    /**
     * @return array<string, string>
     */
    public static function api(): array
    {
        return array_merge(self::shared(), [
            'Content-Security-Policy' => self::API_CSP,
        ]);
    }

    /**
     * Default for PHP front-end proxy and callers that do not distinguish API vs HTML.
     *
     * @return array<string, string>
     */
    public static function all(): array
    {
        return self::web();
    }

    /**
     * @param  callable(string, string): void  $setHeader
     */
    public static function apply(callable $setHeader): void
    {
        foreach (self::all() as $name => $value) {
            $setHeader($name, $value);
        }
    }
}
