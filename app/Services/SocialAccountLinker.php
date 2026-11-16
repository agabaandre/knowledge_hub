<?php

namespace App\Services;

use App\Models\BannedEmail;
use App\Models\SocialAccount;
use App\Models\User;
use App\Support\OAuthAccountSecurity;
use App\Support\Social\SocialLoginDeniedException;
use App\Support\Social\VerifiedSocialIdentity;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Account-matching policy for social sign-in. Users are matched on the
 * provider subject first; email is only used under strict linking rules.
 * Privileged accounts (access_level_id > 1) never sign in socially.
 */
class SocialAccountLinker
{
    public function __construct(private readonly SocialLoginService $socialLoginService)
    {
    }

    /**
     * @throws SocialLoginDeniedException
     */
    public function resolveUser(VerifiedSocialIdentity $identity): User
    {
        if (BannedEmail::isBanned($identity->email)) {
            throw new SocialLoginDeniedException('This account is not allowed to sign in.');
        }

        $account = SocialAccount::where('provider', $identity->provider)
            ->where('provider_user_id', $identity->subject)
            ->first();

        if ($account && $account->user) {
            $this->assertNotPrivileged($account->user);
            $this->assertActive($account->user);
            $account->forceFill(['email' => $identity->email, 'last_login_at' => Carbon::now()])->save();

            return $account->user;
        }

        $existing = User::where('email', $identity->email)->first();
        if ($existing) {
            return $this->linkExisting($existing, $identity);
        }

        return $this->createUser($identity);
    }

    private function linkExisting(User $user, VerifiedSocialIdentity $identity): User
    {
        $this->assertNotPrivileged($user);
        $this->assertActive($user);

        if (! $identity->emailVerified) {
            throw new SocialLoginDeniedException(
                'Your '.OAuthAccountSecurity::providerDisplayName($identity->provider)
                .' email address is not verified, so it cannot be linked to an existing account.'
            );
        }

        if (! $user->is_social_login && ! OAuthAccountSecurity::allowPasswordAccountsToUseSocialLogin()) {
            throw new SocialLoginDeniedException(
                'This email is registered with email and password. Please sign in using your password.'
            );
        }

        $this->recordLink($user, $identity);

        return $user;
    }

    private function createUser(VerifiedSocialIdentity $identity): User
    {
        if (! $identity->emailVerified) {
            throw new SocialLoginDeniedException(
                'Your '.OAuthAccountSecurity::providerDisplayName($identity->provider)
                .' email address is not verified. Verify it with the provider and try again.'
            );
        }

        $user = $this->socialLoginService->createFromVerifiedIdentity($identity);
        if (! $user) {
            throw new SocialLoginDeniedException('Unable to complete sign-in.', 400);
        }

        $this->recordLink($user, $identity);

        return $user;
    }

    public function recordLink(User $user, VerifiedSocialIdentity $identity): void
    {
        DB::transaction(function () use ($user, $identity) {
            SocialAccount::updateOrCreate(
                ['provider' => $identity->provider, 'provider_user_id' => $identity->subject],
                ['user_id' => $user->id, 'email' => $identity->email, 'last_login_at' => Carbon::now()]
            );
        });
    }

    private function assertActive(User $user): void
    {
        // Deactivated/banned accounts must not be silently reactivated by social sign-in.
        if ($user->exists && (int) ($user->status ?? 1) === 0) {
            throw new SocialLoginDeniedException('This account has been deactivated. Contact the hub administrators.');
        }
    }

    private function assertNotPrivileged(User $user): void
    {
        if ((int) ($user->access_level_id ?? 0) > 1) {
            throw new SocialLoginDeniedException(
                'Administrator accounts must sign in with their password (and two-factor authentication where enabled).'
            );
        }
    }
}
