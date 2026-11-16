<?php

namespace Tests\Feature;

use App\Models\SocialAccount;
use App\Models\User;
use App\Support\Social\SocialTokenVerificationException;
use App\Support\Social\SocialTokenVerifierInterface;
use App\Support\Social\VerifiedSocialIdentity;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SocialLoginApiTest extends TestCase
{
    use DatabaseTransactions;

    private function fakeVerifier(?VerifiedSocialIdentity $identity): void
    {
        $this->app->bind(SocialTokenVerifierInterface::class, fn () => new class($identity) implements SocialTokenVerifierInterface
        {
            public function __construct(private readonly ?VerifiedSocialIdentity $identity)
            {
            }

            public function verify(string $provider, ?string $idToken, ?string $accessToken): VerifiedSocialIdentity
            {
                if (trim((string) $idToken) === '' && trim((string) $accessToken) === '') {
                    throw new SocialTokenVerificationException('A provider credential is required.');
                }
                if ($this->identity === null) {
                    throw new SocialTokenVerificationException('Token signature could not be verified.');
                }

                return $this->identity;
            }
        });
    }

    private function makeUser(array $attributes = []): User
    {
        $user = new User();
        $user->forceFill(array_merge([
            'name' => 'Test Person',
            'first_name' => 'Test',
            'last_name' => 'Person',
            'email' => 'social-test-'.uniqid().'@example.org',
            'password' => bcrypt('Secret-123456'),
            'is_verified' => 1,
            'status' => 1,
            'is_social_login' => 0,
            'access_level_id' => 1,
        ], $attributes));
        $user->save();

        return $user;
    }

    public function test_social_login_without_any_token_is_rejected(): void
    {
        $this->fakeVerifier(null);

        $this->postJson('/api/social-login', ['provider' => 'google'])
            ->assertStatus(401)
            ->assertJsonPath('message', 'The provider credential could not be verified.');
    }

    public function test_forged_token_is_rejected_and_no_identity_comes_from_the_body(): void
    {
        $this->fakeVerifier(null);

        // The old exploit payload: email/name in the body must be worthless now.
        $this->postJson('/api/social-login', [
            'provider' => 'google',
            'idToken' => 'forged.jwt.value',
            'email' => 'admin@africacdc.org',
            'name' => 'Admin',
        ])->assertStatus(401);

        $this->assertDatabaseMissing('users', ['email' => 'admin@africacdc.org']);
    }

    public function test_valid_token_logs_in_the_user_matched_by_provider_subject(): void
    {
        $user = $this->makeUser(['is_social_login' => 1, 'social_provider' => 'google']);
        SocialAccount::create([
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_user_id' => 'sub-123',
            'email' => $user->email,
        ]);

        // Even though the verified email differs, the subject match wins.
        $this->fakeVerifier(new VerifiedSocialIdentity(
            provider: 'google',
            subject: 'sub-123',
            email: $user->email,
            emailVerified: true,
            name: 'Test Person',
        ));

        $response = $this->postJson('/api/social-login', ['provider' => 'google', 'idToken' => 'ok']);
        $response->assertStatus(200);
        $this->assertSame((int) $user->id, (int) $response->json('user.id'));
        $this->assertNotEmpty($response->json('token'));
    }

    public function test_admin_accounts_are_never_reachable_via_social_login(): void
    {
        $admin = $this->makeUser(['access_level_id' => 4]);

        $this->fakeVerifier(new VerifiedSocialIdentity(
            provider: 'microsoft',
            subject: 'oid-999',
            email: $admin->email,
            emailVerified: true,
            name: 'Admin Person',
        ));

        $this->postJson('/api/social-login', ['provider' => 'microsoft', 'idToken' => 'ok'])
            ->assertStatus(403);

        $this->assertDatabaseMissing('social_accounts', ['user_id' => $admin->id]);
    }

    public function test_password_accounts_are_not_auto_linked_by_default(): void
    {
        // The policy must deny when the admin setting is off or absent.
        \Illuminate\Support\Facades\DB::table('setting')->update(['allow_email_password_accounts_social_login' => 0]);
        cache()->forget('settings');
        settings(true);

        $user = $this->makeUser(); // password account, is_social_login = 0

        $this->fakeVerifier(new VerifiedSocialIdentity(
            provider: 'google',
            subject: 'sub-456',
            email: $user->email,
            emailVerified: true,
            name: 'Test Person',
        ));

        $this->postJson('/api/social-login', ['provider' => 'google', 'idToken' => 'ok'])
            ->assertStatus(403);

        $this->assertSame(0, (int) $user->fresh()->is_social_login);
    }

    public function test_new_users_are_created_only_with_a_provider_verified_email(): void
    {
        $email = 'brand-new-'.uniqid().'@example.org';

        $this->fakeVerifier(new VerifiedSocialIdentity(
            provider: 'google',
            subject: 'sub-new-1',
            email: $email,
            emailVerified: false,
            name: 'New Person',
        ));

        $this->postJson('/api/social-login', ['provider' => 'google', 'idToken' => 'ok'])
            ->assertStatus(403);
        $this->assertDatabaseMissing('users', ['email' => $email]);

        $this->fakeVerifier(new VerifiedSocialIdentity(
            provider: 'google',
            subject: 'sub-new-1',
            email: $email,
            emailVerified: true,
            name: 'New Person',
        ));

        $this->postJson('/api/social-login', ['provider' => 'google', 'idToken' => 'ok'])
            ->assertStatus(200);
        $this->assertDatabaseHas('users', ['email' => $email]);
        $this->assertDatabaseHas('social_accounts', ['provider' => 'google', 'provider_user_id' => 'sub-new-1']);
    }
}
