<?php

namespace App\Support\Social;

/**
 * Identity proven by a provider token (signature-checked id_token or a
 * server-side userinfo call). Nothing here ever comes from the client body.
 */
final class VerifiedSocialIdentity
{
    public function __construct(
        public readonly string $provider,       // google | microsoft | linkedin
        public readonly string $subject,        // Google sub / Microsoft oid|sub / LinkedIn member id
        public readonly string $email,
        public readonly bool $emailVerified,
        public readonly string $name = '',
        public readonly ?string $pictureUrl = null,
    ) {
    }
}
