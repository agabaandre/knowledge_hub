<?php

namespace App\Console\Commands;

use App\Services\SitemapService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class GenerateSitemapCommand extends Command
{
    protected $signature = 'sitemap:generate {--warm-cache : Regenerate cached sitemap responses}';

    protected $description = 'Warm the public XML sitemap cache (admin paths excluded)';

    public function handle(SitemapService $sitemap): int
    {
        if ($this->option('warm-cache')) {
            Cache::forget('sitemap:index:v1');
            foreach ($sitemap->indexEntries() as $entry) {
                $path = parse_url($entry['loc'], PHP_URL_PATH) ?: '';
                if (preg_match('#/sitemaps/(.+)\.xml$#', $path, $matches) === 1) {
                    Cache::forget('sitemap:section:v1:'.$matches[1]);
                }
            }
        }

        Cache::put('sitemap:index:v1', $sitemap->renderIndex(), now()->addHours(6));

        $sections = 0;
        foreach ($sitemap->indexEntries() as $entry) {
            $path = parse_url($entry['loc'], PHP_URL_PATH) ?: '';
            if (preg_match('#/sitemaps/(.+)\.xml$#', $path, $matches) !== 1) {
                continue;
            }

            $name = $matches[1];
            Cache::put('sitemap:section:v1:'.$name, $sitemap->renderSection($name), now()->addHours(6));
            $sections++;
        }

        $this->info('Sitemap index cached at /sitemap.xml');
        $this->info("Cached {$sections} section sitemap(s) at /sitemaps/*.xml");

        return self::SUCCESS;
    }
}
