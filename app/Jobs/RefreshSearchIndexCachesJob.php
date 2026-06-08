<?php

namespace App\Jobs;

use App\Support\SearchCache;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RefreshSearchIndexCachesJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 60;

    public int $uniqueFor = 60;

    public function __construct(public ?int $publicationId = null)
    {
    }

    public function uniqueId(): string
    {
        return 'refresh-search-caches:'.($this->publicationId ?? 'global');
    }

    public function handle(): void
    {
        SearchCache::bumpAll();
    }
}
