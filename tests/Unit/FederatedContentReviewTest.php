<?php

namespace Tests\Unit;

use App\Http\Controllers\Admin\FederatedContentAdminController;
use App\Models\FederatedContentItem;
use App\Models\FederatedKnowledgeHub;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class FederatedContentReviewTest extends TestCase
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
                'foreign_key_constraints' => true,
            ],
            'cache.default' => 'array',
        ]);

        Schema::dropIfExists('federated_content_items');
        Schema::dropIfExists('federated_knowledge_hubs');

        Schema::create('federated_knowledge_hubs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('base_url');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('federated_content_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('federated_knowledge_hub_id');
            $table->string('content_type', 20);
            $table->unsignedBigInteger('remote_id');
            $table->string('title', 500)->nullable();
            $table->json('payload');
            $table->boolean('central_approved')->default(false);
            $table->boolean('central_rejected')->default(false);
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('remote_updated_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function test_approve_marks_pending_items_as_centrally_approved(): void
    {
        $item = $this->pendingItem();

        $response = (new FederatedContentAdminController())->bulkReview(Request::create(
            '/admin/federated-content/review',
            'POST',
            ['action' => 'approve', 'item_ids' => [$item->id]]
        ));

        $this->assertTrue($response->isRedirect());
        $item->refresh();
        $this->assertTrue($item->central_approved);
        $this->assertFalse($item->central_rejected);
        $this->assertNotNull($item->reviewed_at);
    }

    public function test_reject_marks_pending_items_as_centrally_rejected(): void
    {
        $item = $this->pendingItem();

        $response = (new FederatedContentAdminController())->bulkReview(Request::create(
            '/admin/federated-content/review',
            'POST',
            ['action' => 'reject', 'item_ids' => [$item->id]]
        ));

        $this->assertTrue($response->isRedirect());
        $item->refresh();
        $this->assertFalse($item->central_approved);
        $this->assertTrue($item->central_rejected);
    }

    private function pendingItem(): FederatedContentItem
    {
        $hub = new FederatedKnowledgeHub();
        $hub->forceFill([
            'name' => 'Ghana Hub',
            'base_url' => 'https://khub.example.gh',
            'is_active' => true,
        ])->save();

        $item = new FederatedContentItem();
        $item->forceFill([
            'federated_knowledge_hub_id' => $hub->id,
            'content_type' => 'publication',
            'remote_id' => 42,
            'title' => 'Pending resource',
            'payload' => ['id' => 42, 'title' => 'Pending resource'],
            'central_approved' => false,
            'central_rejected' => false,
            'is_active' => true,
        ])->save();

        return $item;
    }
}
