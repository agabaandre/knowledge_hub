<?php

namespace App\Console\Commands;

use App\Support\SeoSlugSync;
use Illuminate\Console\Command;

class RegenerateSeoSlugsCommand extends Command
{
    protected $signature = 'slugs:regenerate
                            {type=all : Comma-separated types (themes,subthemes,publications,forums,tags,communities,authors,countries,data-categories,subject-areas) or all}
                            {--only-empty : Only fill blank slugs; keep existing values}
                            {--dry-run : Count changes without saving}';

    protected $description = 'Regenerate SEO slugs from current titles/names for themes, subthemes, publications, and other slug-based records';

    public function handle(): int
    {
        $requested = strtolower(trim((string) $this->argument('type')));
        $known = array_keys(SeoSlugSync::catalog());
        $types = $requested === '' || $requested === 'all'
            ? $known
            : array_values(array_filter(array_map('trim', explode(',', $requested))));

        foreach ($types as $type) {
            if (! in_array($type, $known, true)) {
                $this->error('Unknown type "'.$type.'". Allowed: '.implode(', ', $known));

                return self::FAILURE;
            }
        }

        $onlyEmpty = (bool) $this->option('only-empty');
        $dryRun = (bool) $this->option('dry-run');

        if ($dryRun) {
            $this->warn('Dry run — no slugs will be saved.');
        }

        foreach ($types as $type) {
            $result = SeoSlugSync::regenerate($type, $onlyEmpty, $dryRun);
            $this->info(sprintf(
                '%s: scanned %d, %s %d, unchanged %d',
                $type,
                $result['scanned'],
                $dryRun ? 'would update' : 'updated',
                $result['updated'],
                $result['skipped']
            ));
        }

        return self::SUCCESS;
    }
}
