<?php

namespace App\Console\Commands;

use App\Models\FederatedKnowledgeHub;
use App\Services\FederatedHubService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class SyncFederatedHubsCommand extends Command
{
    protected $signature = 'federation:sync
                            {--hub= : Sync a single federated hub by ID}
                            {--all : Sync all active hubs (not only auto_sync)}
                            {--per-page=100 : Remote API page size (max 100)}';

    protected $description = 'Sync public publications and forums from registered country knowledge hubs';

    public function handle(FederatedHubService $federation): int
    {
        if (! Schema::hasTable('federated_knowledge_hubs')) {
            $this->warn('federated_knowledge_hubs table is missing. Run migrations first.');

            return self::FAILURE;
        }

        if (function_exists('hub_admin_units_enabled') && hub_admin_units_enabled()) {
            $this->info('This hub is in country mode; federation sync is intended for continental hubs.');

            return self::SUCCESS;
        }

        $perPage = min(100, max(1, (int) $this->option('per-page')));
        $hubId = $this->option('hub');

        $query = FederatedKnowledgeHub::query()->where('is_active', true);
        if ($hubId) {
            $query->whereKey((int) $hubId);
        } elseif (! $this->option('all')) {
            $query->where('auto_sync', true);
        }

        $hubs = $query->orderBy('name')->get();
        if ($hubs->isEmpty()) {
            $this->info('No hubs matched the sync criteria.');

            return self::SUCCESS;
        }

        $failures = 0;

        foreach ($hubs as $hub) {
            $this->line('Syncing '.$hub->name.' ('.$hub->base_url.')…');

            try {
                $federation->testConnection($hub);
                $payload = $federation->syncPublicData($hub, $perPage);
                $pubCount = (int) data_get($payload, 'publications.meta.total', 0);
                $forumCount = (int) data_get($payload, 'forums.meta.total', 0);
                $this->info("  ✓ {$pubCount} publications, {$forumCount} forums");
            } catch (\Throwable $e) {
                $failures++;
                $hub->connection_status = 'failed';
                $hub->connection_error = $e->getMessage();
                $hub->save();
                $this->error('  ✗ '.$e->getMessage());
            }
        }

        return $failures > 0 ? self::FAILURE : self::SUCCESS;
    }
}
