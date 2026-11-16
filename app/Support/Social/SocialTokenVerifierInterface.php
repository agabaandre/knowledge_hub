<?php

namespace App\Support\Social;

interface SocialTokenVerifierInterface
{
    /**
     * Verify a provider credential and return the proven identity.
     *
     * @param  string  $provider  Canonical provider: google | microsoft | linkedin
     * @param  ?string  $idToken  OIDC id_token (Google, Microsoft)
     * @param  ?string  $accessToken  OAuth access token (Microsoft Graph, LinkedIn userinfo)
     *
     * @throws SocialTokenVerificationException when the credential is missing, forged, expired, or for another app
     */
    public function verify(string $provider, ?string $idToken, ?string $accessToken): VerifiedSocialIdentity;
}
