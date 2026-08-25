<?php

namespace Tests\Feature;

use App\Http\Middleware\BotProtection;
use Tests\TestCase;

class UsersApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(BotProtection::class);
    }

    public function test_me_requires_authentication(): void
    {
        $this->getJson('/api/users/me')->assertStatus(401);
    }

    public function test_unknown_user_id_returns_not_found(): void
    {
        $this->getJson('/api/users/999999999')->assertStatus(404);
    }
}
