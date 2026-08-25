<?php

namespace Tests\Unit;

use App\Support\ApiUserDetails;
use App\Models\User;
use Illuminate\Support\Collection;
use Tests\TestCase;

class ApiUserDetailsTest extends TestCase
{
    public function test_public_details_omit_contact_and_secrets(): void
    {
        $payload = ApiUserDetails::publicDetails($this->userFixture());

        $this->assertSame(7, $payload['id']);
        $this->assertSame('Ada', $payload['first_name']);
        $this->assertSame('Lovelace', $payload['last_name']);
        $this->assertSame('Analyst', $payload['job_title']);
        $this->assertSame('Africa CDC', $payload['organization_name']);
        $this->assertArrayHasKey('names', $payload);
        $this->assertArrayHasKey('photo', $payload);
        $this->assertArrayHasKey('country', $payload);
        $this->assertArrayHasKey('author', $payload);
        $this->assertArrayNotHasKey('email', $payload);
        $this->assertArrayNotHasKey('phone_number', $payload);
        $this->assertArrayNotHasKey('password', $payload);
        $this->assertArrayNotHasKey('fcm_token', $payload);
        $this->assertArrayNotHasKey('verification_token', $payload);
        $this->assertArrayNotHasKey('remember_token', $payload);
    }

    public function test_full_details_include_account_fields_and_hide_password(): void
    {
        $payload = ApiUserDetails::fullDetails($this->userFixture());

        $this->assertSame('ada@example.org', $payload['email']);
        $this->assertSame('Analyst', $payload['job_title']);
        $this->assertSame([], $payload['preference_subtheme_ids']);
        $this->assertSame([], $payload['community_ids']);
        $this->assertArrayHasKey('level_id', $payload);
        $this->assertArrayNotHasKey('password', $payload);
        $this->assertArrayNotHasKey('remember_token', $payload);
    }

    public function test_docs_and_routes_include_user_details_endpoints(): void
    {
        $routes = file_get_contents(base_path('routes/api.php'));
        $controller = file_get_contents(app_path('Http/Controllers/Api/UsersApiController.php'));

        $this->assertStringContainsString("'/users/me'", $routes);
        $this->assertStringContainsString("'/users/{id}'", $routes);
        $this->assertStringContainsString('path="/api/users/me"', $controller);
        $this->assertStringContainsString('path="/api/users/{id}"', $controller);
        $this->assertStringContainsString('Get user details', $controller);
        $this->assertStringContainsString('UsersApiController', $routes);
    }

    private function userFixture(): User
    {
        $user = new User();
        $user->forceFill([
            'id' => 7,
            'first_name' => 'Ada',
            'last_name' => 'Lovelace',
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.org',
            'phone_number' => '+256700000000',
            'job_title' => 'Analyst',
            'organization_name' => 'Africa CDC',
            'orcid' => null,
            'country_id' => null,
            'author_id' => null,
            'photo' => 'avatar.jpg',
            'password' => 'secret-hash',
            'remember_token' => 'remember',
            'fcm_token' => 'fcm',
            'verification_token' => 'verify',
            'access_level_id' => 1,
        ]);
        $user->syncOriginal();
        $user->setRelation('country', null);
        $user->setRelation('author', null);
        $user->setRelation('preferences', new Collection());
        $user->setRelation('communities', new Collection());
        $user->setRelation('access_level', null);

        return $user;
    }
}
