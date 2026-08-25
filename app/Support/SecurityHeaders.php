<?php

namespace App\Support;

class SecurityHeaders
{
    /**
     * @return array<string, string>
     */
    public static function all(): array
    {
        return [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
            'Content-Security-Policy' => "default-src 'none'",
            'Referrer-Policy' => 'no-referrer',
            'Strict-Transport-Security' => 'max-age=31536000',
            'Permissions-Policy' => 'geolocation=(), microphone=(), camera=()',
        ];
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
