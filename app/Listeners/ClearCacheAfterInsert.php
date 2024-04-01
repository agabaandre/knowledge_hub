<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;

class ClearCacheAfterInsert
{
    public function handle($event)
    {
        // Clear the cache here
        Cache::flush(); // This is just an example, you may want to clear specific cache keys instead
    }
}
