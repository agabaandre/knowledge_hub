<?php

namespace App\Console\Commands;

use App\Models\Publication;
use Illuminate\Console\Command;

/**
 * One-off cleanup: remove leading AI "Overview" blocks from publication.description HTML.
 * Targets patterns like <p style="color: teal; font-weight: bold;">Overview</p>
 */
class CleanupPublicationOverviewDescription extends Command
{
    protected $signature = 'publications:cleanup-overview-in-description
                            {--dry-run : List affected IDs without saving}';

    protected $description = 'Strip leading Overview HTML from publication descriptions (teal/bold AI headings)';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        if ($dryRun) {
            $this->warn('Dry run — no changes will be saved.');
        }

        $updated = 0;
        $checked = 0;

        Publication::query()
            ->whereNotNull('description')
            ->where('description', 'like', '%Overview:%')
            ->orderBy('id')
            ->chunkById(100, function ($publications) use ($dryRun, &$updated, &$checked) {
                foreach ($publications as $pub) {
                    $checked++;
                    $original = $pub->description;
                    $cleaned = $this->cleanupDescription($original);

                    if ($cleaned === $original) {
                        continue;
                    }

                    $updated++;
                    $this->line("ID {$pub->id}: " . ($dryRun ? 'would update' : 'updated'));

                    if (!$dryRun) {
                        $pub->description = $cleaned;
                        $pub->save();
                    }
                }
            });

        $this->info("Checked {$checked} row(s) with 'Overview' in description.");
        $this->info($dryRun
            ? "Would update {$updated} publication(s)."
            : "Updated {$updated} publication(s).");

        return self::SUCCESS;
    }

    /**
     * Same post-processing as live AI extraction (overview strip + leading subheading strip).
     */
    private function cleanupDescription(string $html): string
    {
        if (!function_exists('normalize_ai_publication_summary_html')) {
            return $html;
        }

        return normalize_ai_publication_summary_html($html);
    }
}
