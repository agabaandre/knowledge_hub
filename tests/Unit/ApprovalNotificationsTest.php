<?php

namespace Tests\Unit;

use App\Jobs\NotifyApprovers;
use App\Jobs\SendMailJob;
use App\Models\CommunityOfPractice;
use App\Models\CommunityOfPracticeMembers;
use App\Models\FederatedKnowledgeHub;
use App\Models\User;
use App\Repositories\CommsOfPracticeRepository;
use App\Services\FederatedContentStagingService;
use App\Support\ApprovalNotifications;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use ReflectionProperty;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ApprovalNotificationsTest extends TestCase
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
                'foreign_key_constraints' => false,
            ],
            'cache.default' => 'array',
            'permission.cache.store' => 'array',
            'queue.default' => 'sync',
            'app.url' => 'http://localhost',
        ]);
        \Illuminate\Support\Facades\DB::purge();
        \Illuminate\Support\Facades\DB::reconnect('sqlite');
    }

    public function test_inbox_url_points_to_the_central_approvals_queue(): void
    {
        $url = ApprovalNotifications::inboxUrl();

        $this->assertStringContainsString('/admin/approvals', $url);
        $this->assertStringNotContainsString('type=', $url);
    }

    public function test_inbox_url_includes_type_and_search_for_a_specific_item(): void
    {
        $url = ApprovalNotifications::inboxUrl('publication', 'Malaria briefing');

        $this->assertStringContainsString('/admin/approvals', $url);
        $this->assertStringContainsString('type=publication', $url);
        $this->assertStringContainsString('q=Malaria', $url);
    }

    public function test_recipients_only_include_users_who_can_approve_that_type(): void
    {
        $this->setUpPermissionSchema();

        $publicationApprover = $this->makeUser('pub@example.com', 'Pub Approver');
        $forumApprover = $this->makeUser('forum@example.com', 'Forum Approver');
        $copApprover = $this->makeUser('cop@example.com', 'CoP Approver');
        $viewer = $this->makeUser('viewer@example.com', 'Viewer');

        $publicationApprover->givePermissionTo('moderate_publication');
        $forumApprover->givePermissionTo('moderate_forum');
        $copApprover->givePermissionTo('moderate_cop_participants');
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->assertEquals(['pub@example.com'], ApprovalNotifications::recipientsFor('publication')->pluck('email')->all());
        $this->assertEquals(['forum@example.com'], ApprovalNotifications::recipientsFor('forum')->pluck('email')->all());
        $this->assertEquals(['cop@example.com'], ApprovalNotifications::recipientsFor('cop_participant')->pluck('email')->all());
        $this->assertEqualsCanonicalizing(
            ['pub@example.com', 'forum@example.com'],
            ApprovalNotifications::recipientsFor('federated')->pluck('email')->all()
        );
        $this->assertFalse(ApprovalNotifications::canApprove($viewer, 'publication'));
        $this->assertTrue(ApprovalNotifications::canApprove($publicationApprover, 'federated'));
        $this->assertFalse(ApprovalNotifications::canApprove($copApprover, 'federated'));
    }

    public function test_immediate_notification_job_always_links_to_the_central_inbox(): void
    {
        $job = new NotifyApprovers(
            'publication',
            12,
            'Malaria briefing',
            'A summary',
            'Dr Jane',
            'http://localhost/admin/publications/details?id=12'
        );

        $this->assertStringContainsString('/admin/approvals', $this->jobApproveUrl($job));
        $this->assertStringContainsString('type=publication', $this->jobApproveUrl($job));
        $this->assertStringContainsString('q=Malaria', $this->jobApproveUrl($job));
    }

    public function test_immediate_notification_emails_matching_approvers_with_the_inbox_link(): void
    {
        $this->setUpPermissionSchema();
        $this->setUpSettingsTable();
        Queue::fake();

        $approver = $this->makeUser('approver@example.com', 'Ada');
        $other = $this->makeUser('other@example.com', 'Other');
        $approver->givePermissionTo('moderate_publication');
        $other->givePermissionTo('moderate_forum');

        (new NotifyApprovers('publication', 12, 'Malaria briefing', 'A summary', 'Dr Jane'))->handle();

        Queue::assertPushed(SendMailJob::class, 1);
        Queue::assertPushed(SendMailJob::class, function (SendMailJob $job) {
            $payload = $this->mailPayload($job);

            return $payload->email === 'approver@example.com'
                && str_contains((string) $payload->body, '/admin/approvals')
                && str_contains((string) $payload->body, 'type=publication')
                && str_contains((string) $payload->body, 'Review & Approve');
        });
    }

    public function test_approval_email_templates_send_reviewers_to_the_central_inbox(): void
    {
        $immediate = file_get_contents(resource_path('views/emails/approval_notification.blade.php'));
        $daily = file_get_contents(resource_path('views/emails/daily_approval_summary.blade.php'));

        $this->assertIsString($immediate);
        $this->assertIsString($daily);
        $this->assertStringContainsString('Review & Approve', $immediate);
        $this->assertStringContainsString('/admin/approvals', $daily);
        $this->assertStringContainsString('type=federated', $daily);
        $this->assertStringContainsString('type=cop_participant', $daily);
        $this->assertStringNotContainsString("url('admin/publications/details')", $daily);
        $this->assertStringNotContainsString("url('admin/forums/moderate')", $daily);
    }

    public function test_federated_sync_sends_one_digest_when_new_content_needs_approval(): void
    {
        $this->setUpFederatedSchema();
        Queue::fake();

        $hub = new FederatedKnowledgeHub();
        $hub->forceFill(['name' => 'Ghana Hub', 'base_url' => 'https://khub.example.gh'])->save();

        $stats = (new FederatedContentStagingService())->stageFromSync($hub, [
            'publications' => [
                'data' => [
                    ['id' => 7, 'title' => 'Partner resource', 'updated_at' => '2026-08-23 10:00:00'],
                ],
            ],
        ]);

        $this->assertSame(1, $stats['new']);
        Queue::assertPushed(NotifyApprovers::class, 1);
        Queue::assertPushed(NotifyApprovers::class, function (NotifyApprovers $job) {
            return $this->jobProperty($job, 'contentType') === 'federated'
                && str_contains($this->jobApproveUrl($job), 'type=federated');
        });
    }

    public function test_pending_community_join_notifies_cop_approvers_with_inbox_link(): void
    {
        $this->setUpCommunitySchema();
        Queue::fake();

        $community = new CommunityOfPractice();
        $community->forceFill(['community_name' => 'Epidemiology CoP'])->save();

        $ok = (new CommsOfPracticeRepository())->addMember($community->id, 44);

        $this->assertTrue($ok);
        Queue::assertPushed(NotifyApprovers::class, 1);
        Queue::assertPushed(NotifyApprovers::class, function (NotifyApprovers $job) {
            return $this->jobProperty($job, 'contentType') === 'cop_participant'
                && str_contains($this->jobApproveUrl($job), 'type=cop_participant');
        });
    }

    protected function setUpPermissionSchema(): void
    {
        Schema::dropIfExists('model_has_permissions');
        Schema::dropIfExists('model_has_roles');
        Schema::dropIfExists('role_has_permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
            $table->unique(['name', 'guard_name']);
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
            $table->unique(['name', 'guard_name']);
        });

        Schema::create('model_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->primary(['permission_id', 'model_id', 'model_type']);
        });

        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->primary(['role_id', 'model_id', 'model_type']);
        });

        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');
            $table->primary(['permission_id', 'role_id']);
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (['moderate_publication', 'moderate_forum', 'moderate_cop_participants'] as $name) {
            Permission::findOrCreate($name, 'web');
        }
    }

    protected function setUpSettingsTable(): void
    {
        Schema::dropIfExists('setting');
        Schema::create('setting', function (Blueprint $table) {
            $table->id();
            $table->string('status')->nullable();
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('spotlight_banner')->nullable();
        });
        DB::table('setting')->insert(['status' => 'active']);
        cache()->forget('settings');
        foreach ([
            'asset_types',
            'categories',
            'dashboard_categories',
            'tags_ordered_by_tag_text',
            'health_emergencies',
        ] as $key) {
            cache()->forever($key, collect());
        }
        if (function_exists('settings')) {
            settings(true);
        }
    }

    protected function setUpFederatedSchema(): void
    {
        Schema::dropIfExists('federated_content_items');
        Schema::dropIfExists('federated_knowledge_hubs');

        Schema::create('federated_knowledge_hubs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('base_url')->nullable();
            $table->timestamps();
        });

        Schema::create('federated_content_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('federated_knowledge_hub_id');
            $table->string('content_type', 20);
            $table->unsignedBigInteger('remote_id');
            $table->string('title', 500)->nullable();
            $table->json('payload')->nullable();
            $table->boolean('central_approved')->default(false);
            $table->boolean('central_rejected')->default(false);
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('remote_updated_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    protected function setUpCommunitySchema(): void
    {
        Schema::dropIfExists('community_of_practice_members');
        Schema::dropIfExists('community_of_practices');

        Schema::create('community_of_practices', function (Blueprint $table) {
            $table->id();
            $table->string('community_name')->nullable();
            $table->timestamps();
        });

        Schema::create('community_of_practice_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('community_of_practice_id');
            $table->unsignedBigInteger('user_id');
            $table->integer('is_approved')->default(0);
            $table->integer('is_active')->default(1);
            $table->integer('is_admin')->default(0);
            $table->timestamps();
        });
    }

    protected function makeUser(string $email, string $name): User
    {
        $user = new User();
        $user->forceFill([
            'name' => $name,
            'email' => $email,
            'password' => 'secret',
        ])->save();

        return $user;
    }

    protected function jobApproveUrl(NotifyApprovers $job): string
    {
        return (string) $this->jobProperty($job, 'approveUrl');
    }

    protected function jobProperty(object $job, string $name)
    {
        $property = new ReflectionProperty($job, $name);
        $property->setAccessible(true);

        return $property->getValue($job);
    }

    protected function mailPayload(SendMailJob $job)
    {
        $property = new ReflectionProperty($job, 'data');
        $property->setAccessible(true);

        return $property->getValue($job);
    }
}
