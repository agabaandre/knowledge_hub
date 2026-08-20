<?php

namespace Tests\Unit;

use App\Models\Author;
use App\Models\User;
use App\Repositories\AuthorsRepository;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class EnsureAuthorForUserTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ],
            'cache.default' => 'array',
        ]);

        Schema::dropIfExists('users');
        Schema::dropIfExists('author');

        Schema::create('author', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('icon', 30)->default('fa fa-archive');
            $table->string('is_organsiation', 10)->default('No');
            $table->string('address', 255)->default('');
            $table->string('telephone', 50)->default('');
            $table->string('email', 255)->default('');
            $table->string('logo', 100)->default('author.png');
            $table->string('slug')->nullable();
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->unsignedInteger('author_id')->nullable();
            $table->timestamps();
        });
    }

    public function test_it_creates_author_with_required_columns_and_links_user(): void
    {
        $user = User::create([
            'name' => 'Ama Mensah',
            'first_name' => 'Ama',
            'last_name' => 'Mensah',
            'email' => 'ama@example.com',
        ]);

        $author = (new AuthorsRepository())->ensureAuthorForUser($user);

        $this->assertGreaterThan(0, (int) $author->id);
        $this->assertSame('Ama Mensah', $author->name);
        $this->assertSame('No', $author->is_organsiation);
        $this->assertSame((int) $author->id, (int) $user->fresh()->author_id);
        $this->assertDatabaseHas('author', [
            'id' => $author->id,
            'email' => 'ama@example.com',
        ]);
    }

    public function test_assign_missing_author_accounts_backfills_users(): void
    {
        $u1 = User::create(['name' => 'One', 'email' => 'one@example.com']);
        $u2 = User::create(['name' => 'Two', 'email' => 'two@example.com']);

        $result = (new AuthorsRepository())->assignMissingAuthorAccounts();

        $this->assertSame(2, $result['assigned']);
        $this->assertNotNull($u1->fresh()->author_id);
        $this->assertNotNull($u2->fresh()->author_id);
        $this->assertSame(2, Author::count());
    }
}
