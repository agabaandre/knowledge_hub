<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Publication;
use Carbon\Carbon;

class PurgeRejectedPublications extends Command
{
    protected $signature = 'publications:purge-rejected {--days=90}';
    protected $description = 'Delete publications rejected for N days without appeal and remove all uploads';

    public function handle(\App\Services\Purge\PublicationPurger $purger)
    {
        $days = intval($this->option('days')) ?: 90;
        $cutoff = Carbon::now()->subDays($days);

        $ids = Publication::where('is_rejected', 1)
            ->where('is_approved', 0)
            ->whereNull('appealed_at')
            ->whereNotNull('rejected_at')
            ->where('rejected_at', '<=', $cutoff)
            ->pluck('id');

        $deletedCount = 0;

        // PublicationPurger resolves files on the configured hub files disk
        // (storage_path('app/public/...') was wrong once public/storage was
        // symlinked to /var/khubdata) and removes every child row.
        foreach ($ids as $id) {
            $purger->purge((int) $id);
            $deletedCount++;
        }

        $this->info("Purged {$deletedCount} rejected publications (>{$days} days old)");
        return Command::SUCCESS;
    }
}


