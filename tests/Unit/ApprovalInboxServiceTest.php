<?php

namespace Tests\Unit;

use App\Models\CommunityOfPractice;
use App\Models\CommunityOfPracticeMembers;
use App\Models\FederatedContentItem;
use App\Models\FederatedKnowledgeHub;
use App\Models\Forum;
use App\Models\Publication;
use App\Services\ApprovalInboxService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ApprovalInboxServiceTest extends TestCase
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
            'scout.driver' => 'null',
            'queue.default' => 'sync',
        ]);

        Publication::unsetEventDispatcher();
        Forum::unsetEventDispatcher();
        Schema::dropIfExists('federated_content_items');
        Schema::dropIfExists('federated_knowledge_hubs');
        Schema::dropIfExists('community_of_practice_members');
        Schema::dropIfExists('community_of_practices');
        Schema::dropIfExists('forums');
        Schema::dropIfExists('publication');

        Schema::create('publication', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->integer('is_approved')->default(0);
            $table->integer('is_rejected')->default(0);
            $table->timestamps();
        });

        Schema::create('forums', function (Blueprint $table) {
            $table->id();
            $table->string('forum_title')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->integer('is_approved')->default(0);
            $table->integer('status')->default(0);
            $table->integer('is_rejected')->default(0);
            $table->timestamps();
        });

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
            $table->timestamps();
        });

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
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function test_it_lists_only_items_waiting_for_approval(): void
    {
        $pendingPub = new Publication();
        $pendingPub->forceFill(['title' => 'Pending resource', 'is_approved' => 0, 'is_rejected' => 0])->save();
        $approvedPub = new Publication();
        $approvedPub->forceFill(['title' => 'Live resource', 'is_approved' => 1, 'is_rejected' => 0])->save();

        $pendingForum = new Forum();
        $pendingForum->forceFill(['forum_title' => 'Pending forum', 'is_approved' => 0, 'status' => 0, 'is_rejected' => 0])->save();
        $approvedForum = new Forum();
        $approvedForum->forceFill(['forum_title' => 'Live forum', 'is_approved' => 1, 'status' => 1, 'is_rejected' => 0])->save();

        $community = new CommunityOfPractice();
        $community->forceFill(['community_name' => 'Epidemiology CoP'])->save();
        $pendingMember = new CommunityOfPracticeMembers();
        $pendingMember->forceFill([
            'community_of_practice_id' => $community->id,
            'user_id' => 9,
            'is_approved' => 0,
        ])->save();
        $approvedMember = new CommunityOfPracticeMembers();
        $approvedMember->forceFill([
            'community_of_practice_id' => $community->id,
            'user_id' => 10,
            'is_approved' => 1,
        ])->save();

        $hub = new FederatedKnowledgeHub();
        $hub->forceFill(['name' => 'Ghana Hub', 'base_url' => 'https://khub.example.gh'])->save();
        $pendingFed = new FederatedContentItem();
        $pendingFed->forceFill([
            'federated_knowledge_hub_id' => $hub->id,
            'content_type' => 'publication',
            'remote_id' => 7,
            'title' => 'Partner resource',
            'payload' => ['id' => 7],
            'central_approved' => false,
            'central_rejected' => false,
            'is_active' => true,
        ])->save();
        $approvedFed = new FederatedContentItem();
        $approvedFed->forceFill([
            'federated_knowledge_hub_id' => $hub->id,
            'content_type' => 'publication',
            'remote_id' => 8,
            'title' => 'Already approved partner resource',
            'payload' => ['id' => 8],
            'central_approved' => true,
            'central_rejected' => false,
            'is_active' => true,
        ])->save();

        $inbox = new ApprovalInboxService();
        $items = $inbox->pendingItems();
        $titles = $items->pluck('title')->all();
        $counts = $inbox->counts();

        $this->assertContains('Pending resource', $titles);
        $this->assertContains('Pending forum', $titles);
        $this->assertContains('Partner resource', $titles);
        $this->assertNotContains('Live resource', $titles);
        $this->assertNotContains('Live forum', $titles);
        $this->assertNotContains('Already approved partner resource', $titles);
        $this->assertSame(1, $counts['publication']);
        $this->assertSame(1, $counts['forum']);
        $this->assertSame(1, $counts['cop_participant']);
        $this->assertSame(1, $counts['federated']);
        $this->assertSame(4, $counts['all']);
        $this->assertCount(1, $inbox->pendingItems('publication'));
        $this->assertSame('publication', $inbox->pendingItems('publication')->first()['type']);
    }
}
