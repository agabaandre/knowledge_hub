<?php

namespace App\Support;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Str;

final class FederationApiToken
{
    public const LENGTH = 80;

    /**
     * Shared secret other hubs send as Authorization: Bearer when calling /api/federation/*.
     * Length stays within typical VARCHAR(128) columns used by remote hub records.
     */
    public static function random(): string
    {
        return Str::random(self::LENGTH);
    }

    /**
     * Mint a token while acting as the signed-in admin. Passport personal-access
     * tokens are skipped because JWTs exceed federation VARCHAR fields on other hubs.
     */
    public static function forAdmin(?Authenticatable $user = null): string
    {
        return self::random();
    }
}
