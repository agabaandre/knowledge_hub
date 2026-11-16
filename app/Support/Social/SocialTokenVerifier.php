<?php

namespace App\Support\Social;

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

/**
 * Verifies provider credentials server-side. Identity is derived ONLY from
 * the signature-checked id_token (Google, Microsoft) or a userinfo call with
 * the access token (Microsoft Graph, LinkedIn) — never from the request body.
 */
class SocialTokenVerifier implements SocialTokenVerifierInterface
{
    private const GOOGLE_JWKS = 'https://www.googleapis.com/oauth2/v3/certs';

    private const GOOGLE_ISSUERS = ['https://accounts.google.com', 'accounts.google.com'];

    private const JWKS_CACHE_TTL = 21600; // 6h; providers rotate keys slowly

    public function verify(string $provider, ?string $idToken, ?string $accessToken): VerifiedSocialIdentity
    {
        return match ($provider) {
            'google' => $this->verifyGoogle($this->requireToken($idToken, 'idToken')),
            'microsoft' => $idToken !== null && $idToken !== ''
                ? $this->verifyMicrosoftIdToken($idToken)
                : $this->verifyMicrosoftAccessToken($this->requireToken($accessToken, 'idToken or accessToken')),
            'linkedin' => $this->verifyLinkedIn($this->requireToken($accessToken, 'accessToken')),
            default => throw new SocialTokenVerificationException('Unsupported provider.'),
        };
    }

    private function requireToken(?string $token, string $label): string
    {
        $token = trim((string) $token);
        if ($token === '') {
            throw new SocialTokenVerificationException("A provider {$label} is required.");
        }

        return $token;
    }

    private function verifyGoogle(string $idToken): VerifiedSocialIdentity
    {
        $claims = $this->decodeAgainstJwks($idToken, self::GOOGLE_JWKS, 'google');

        if (! in_array((string) ($claims['iss'] ?? ''), self::GOOGLE_ISSUERS, true)) {
            throw new SocialTokenVerificationException('Google token issuer mismatch.');
        }
        $this->assertAudience((string) ($claims['aud'] ?? ''), $this->allowedClientIds('google'), 'Google');

        $email = strtolower(trim((string) ($claims['email'] ?? '')));
        $subject = (string) ($claims['sub'] ?? '');
        if ($email === '' || $subject === '') {
            throw new SocialTokenVerificationException('Google token is missing identity claims.');
        }

        return new VerifiedSocialIdentity(
            provider: 'google',
            subject: $subject,
            email: $email,
            emailVerified: filter_var($claims['email_verified'] ?? false, FILTER_VALIDATE_BOOLEAN),
            name: trim((string) ($claims['name'] ?? '')),
            pictureUrl: isset($claims['picture']) ? (string) $claims['picture'] : null,
        );
    }

    private function verifyMicrosoftIdToken(string $idToken): VerifiedSocialIdentity
    {
        $tenant = (string) (config('services.microsoft.tenant') ?: 'common');
        $jwksTenant = in_array($tenant, ['common', 'organizations', 'consumers'], true) ? 'common' : $tenant;
        $claims = $this->decodeAgainstJwks(
            $idToken,
            "https://login.microsoftonline.com/{$jwksTenant}/discovery/v2.0/keys",
            'microsoft:'.$jwksTenant
        );

        $tid = (string) ($claims['tid'] ?? '');
        if ($tid === '') {
            throw new SocialTokenVerificationException('Microsoft token is missing the tenant claim.');
        }
        if (! in_array($tenant, ['common', 'organizations', 'consumers'], true) && ! hash_equals($tenant, $tid)) {
            throw new SocialTokenVerificationException('Microsoft token tenant mismatch.');
        }
        $expectedIssuer = "https://login.microsoftonline.com/{$tid}/v2.0";
        if ((string) ($claims['iss'] ?? '') !== $expectedIssuer) {
            throw new SocialTokenVerificationException('Microsoft token issuer mismatch.');
        }
        $this->assertAudience((string) ($claims['aud'] ?? ''), $this->allowedClientIds('microsoft'), 'Microsoft');

        $email = strtolower(trim((string) ($claims['email'] ?? $claims['preferred_username'] ?? '')));
        $subject = (string) ($claims['oid'] ?? $claims['sub'] ?? '');
        if ($email === '' || $subject === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new SocialTokenVerificationException('Microsoft token is missing identity claims.');
        }

        return new VerifiedSocialIdentity(
            provider: 'microsoft',
            subject: $subject,
            // Entra work/school accounts have provider-verified addresses; xms_edov covers guest edge cases.
            email: $email,
            emailVerified: filter_var($claims['xms_edov'] ?? true, FILTER_VALIDATE_BOOLEAN),
            name: trim((string) ($claims['name'] ?? '')),
        );
    }

    private function verifyMicrosoftAccessToken(string $accessToken): VerifiedSocialIdentity
    {
        $user = $this->socialiteUserFromToken('microsoft', $accessToken);
        $raw = (array) $user->getRaw();

        $email = strtolower(trim((string) ($user->getEmail() ?? $raw['mail'] ?? $raw['userPrincipalName'] ?? '')));
        $subject = (string) ($user->getId() ?? $raw['id'] ?? '');
        if ($email === '' || $subject === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new SocialTokenVerificationException('Microsoft Graph did not return a usable identity.');
        }

        return new VerifiedSocialIdentity(
            provider: 'microsoft',
            subject: $subject,
            email: $email,
            emailVerified: true, // Graph /me only answers for the token's real account
            name: trim((string) ($user->getName() ?? '')),
        );
    }

    private function verifyLinkedIn(string $accessToken): VerifiedSocialIdentity
    {
        $user = $this->socialiteUserFromToken('linkedin-openid', $accessToken);
        $raw = (array) $user->getRaw();

        $email = strtolower(trim((string) ($user->getEmail() ?? $raw['email'] ?? '')));
        $subject = (string) ($user->getId() ?? $raw['sub'] ?? '');
        if ($email === '' || $subject === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new SocialTokenVerificationException('LinkedIn did not return a usable identity.');
        }

        return new VerifiedSocialIdentity(
            provider: 'linkedin',
            subject: $subject,
            email: $email,
            emailVerified: filter_var($raw['email_verified'] ?? true, FILTER_VALIDATE_BOOLEAN),
            name: trim((string) ($user->getName() ?? '')),
            pictureUrl: $user->getAvatar() ?: null,
        );
    }

    /** @return \Laravel\Socialite\Two\User */
    private function socialiteUserFromToken(string $driver, string $accessToken)
    {
        try {
            return Socialite::driver($driver)->userFromToken($accessToken);
        } catch (Throwable $e) {
            throw new SocialTokenVerificationException('Provider rejected the access token.', 0, $e);
        }
    }

    /** @return array<string, mixed> */
    private function decodeAgainstJwks(string $jwt, string $jwksUrl, string $cacheKey): array
    {
        try {
            $keys = JWK::parseKeySet($this->fetchJwks($jwksUrl, $cacheKey));
            JWT::$leeway = 60;

            return (array) JWT::decode($jwt, $keys);
        } catch (SocialTokenVerificationException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new SocialTokenVerificationException('Token signature could not be verified.', 0, $e);
        }
    }

    /** @return array{keys: array<int, array<string, mixed>>} */
    private function fetchJwks(string $url, string $cacheKey): array
    {
        $jwks = Cache::remember('social_jwks:'.$cacheKey, self::JWKS_CACHE_TTL, function () use ($url) {
            $response = Http::timeout(10)->get($url);
            if (! $response->ok() || ! is_array($response->json('keys'))) {
                throw new SocialTokenVerificationException('Could not load provider signing keys.');
            }

            return $response->json();
        });

        if (! is_array($jwks) || ! isset($jwks['keys'])) {
            throw new SocialTokenVerificationException('Could not load provider signing keys.');
        }

        return $jwks;
    }

    /** @return list<string> */
    private function allowedClientIds(string $provider): array
    {
        $ids = [(string) config("services.{$provider}.client_id")];
        foreach (explode(',', (string) config("services.{$provider}.allowed_client_ids", '')) as $extra) {
            $ids[] = trim($extra);
        }

        return array_values(array_filter(array_unique($ids)));
    }

    /** @param  list<string>  $allowed */
    private function assertAudience(string $aud, array $allowed, string $label): void
    {
        if ($aud === '' || $allowed === [] || ! in_array($aud, $allowed, true)) {
            throw new SocialTokenVerificationException("{$label} token was issued for a different application.");
        }
    }
}
