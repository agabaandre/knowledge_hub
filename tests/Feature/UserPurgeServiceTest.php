<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Purge\UserPurgeService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserPurgeServiceTest extends TestCase
{
    use DatabaseTransactions;

    private function makeSpammer(): array
    {
        $user = new User();
        $user->forceFill([
            'name' => 'Spam Bot',
            'first_name' => 'Spam',
            'last_name' => 'Bot',
            'email' => 'spammer-'.uniqid().'@fldubai.com',
            'password' => bcrypt('Spam-123456'),
            'is_verified' => 1,
            'status' => 1,
            'access_level_id' => 1,
        ])->save();

        $publicationId = DB::table('publication')->insertGetId([
            'title' => 'Junk publication',
            'slug' => 'junk-'.uniqid(),
            'user_id' => $user->id,
            'is_approved' => 0,
            'created_at' => Carbon::now(),
        ]);
        DB::table('publication_attachments')->insert([
            'publication_id' => $publicationId,
            'file' => 'purge-test-'.uniqid().'.pdf',
        ]);
        DB::table('publication_comments')->insert([
            'publication_id' => $publicationId,
            'user_id' => $user->id,
            'comment' => 'spam comment',
            'created_at' => Carbon::now(),
        ]);

        $forumId = DB::table('forums')->insertGetId([
            'forum_title' => 'Spam thread',
            'slug' => 'spam-thread-'.uniqid(),
            'forum_description' => 'spam',
            'created_by' => $user->id,
            'created_at' => Carbon::now(),
            'is_approved' => 0,
        ]);
        DB::table('forum_comments')->insert([
            'forum_id' => $forumId,
            'comment' => 'spam reply',
            'created_by' => $user->id,
            'created_at' => Carbon::now(),
        ]);

        $copId = DB::table('community_of_practices')->value('id');
        if ($copId) {
            DB::table('community_of_practice_members')->insert([
                'user_id' => $user->id,
                'community_of_practice_id' => $copId,
                'is_approved' => 1,
            ]);
        }

        DB::table('oauth_access_tokens')->insert([
            'id' => Str::random(80),
            'user_id' => $user->id,
            'client_id' => 1,
            'name' => 'Personal Access Token',
            'revoked' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'expires_at' => Carbon::now()->addYear(),
        ]);

        return [$user, $publicationId, $forumId];
    }

    public function test_purge_with_ban_removes_content_revokes_tokens_and_bans_the_email(): void
    {
        [$user, $publicationId, $forumId] = $this->makeSpammer();

        $summary = app(UserPurgeService::class)->purge($user, ['ban' => true, 'reason' => 'test purge']);

        $this->assertDatabaseMissing('publication', ['id' => $publicationId]);
        $this->assertDatabaseMissing('publication_attachments', ['publication_id' => $publicationId]);
        $this->assertDatabaseMissing('publication_comments', ['publication_id' => $publicationId]);
        $this->assertDatabaseMissing('forums', ['id' => $forumId]);
        $this->assertDatabaseMissing('forum_comments', ['forum_id' => $forumId]);
        $this->assertDatabaseMissing('community_of_practice_members', ['user_id' => $user->id]);
        $this->assertDatabaseMissing('oauth_access_tokens', ['user_id' => $user->id]);

        $this->assertDatabaseHas('banned_emails', ['email' => strtolower($user->email)]);
        $this->assertSame(0, (int) $user->fresh()->status);

        $this->assertNotEmpty($summary['snapshot']);
        $this->assertFileExists($summary['snapshot']);
        @unlink($summary['snapshot']);
    }

    public function test_dry_run_changes_nothing(): void
    {
        [$user, $publicationId] = $this->makeSpammer();

        $summary = app(UserPurgeService::class)->purge($user, ['ban' => true, 'dryRun' => true]);

        $this->assertDatabaseHas('publication', ['id' => $publicationId]);
        $this->assertDatabaseHas('oauth_access_tokens', ['user_id' => $user->id]);
        $this->assertDatabaseMissing('banned_emails', ['email' => strtolower($user->email)]);
        $this->assertSame(1, (int) $user->fresh()->status);
        $this->assertSame(1, $summary['publications']);
    }

    public function test_banned_email_cannot_register_again(): void
    {
        [$user] = $this->makeSpammer();
        $email = $user->email;

        // Purge in delete mode, then ban the address (as the ban-list protects
        // even deleted accounts from re-registration).
        app(UserPurgeService::class)->purge($user, ['ban' => false]);
        \App\Models\BannedEmail::create(['email' => strtolower($email), 'reason' => 'test']);
        $this->assertDatabaseMissing('users', ['email' => $email]);

        $countryId = DB::table('country')->value('id');
        $subthemeId = DB::table('sub_thematic_area')->value('id');

        $this->postJson('/api/register', [
            'firstname' => 'Spam', 'lastname' => 'Again',
            'email' => $email,
            'password' => 'Spam-123456', 'password_confirmation' => 'Spam-123456',
            'phone' => '+251115177000', 'country_id' => $countryId,
            'preferences' => [$subthemeId],
            'job' => 'Tester',
        ])->assertStatus(403);
    }
}
