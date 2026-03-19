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
            ->where('description', 'like', '%Overview%')
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
     * Remove leading Overview blocks (shared helper + teal-styled &lt;p&gt; variants).
     */
    private function cleanupDescription(string $html): string
    {
        $out = $html;

        for ($i = 0; $i < 15; $i++) {
            $prev = $out;
            if (function_exists('strip_leading_overview_heading_from_summary_html')) {
                $out = strip_leading_overview_heading_from_summary_html($out);
            }

            // <p style="color: teal; font-weight: bold;">Overview</p> (and attribute order / quote variants)
            $out = preg_replace(
                '#^\s*(?:<br\s*/?>\s*|&nbsp;\s*|\xc2\xa0\s*)*(?:<div\b[^>]*>\s*)*' .
                '<p\b[^>]*style\s*=\s*["\'][^"\']*teal[^"\']*["\'][^>]*>\s*' .
                'Overview(?:\s+of\s+the\s+Document)?\s*</p>\s*#iu',
                '',
                $out,
                1
            );

            // <p><strong style="...teal...">Overview</strong></p> at start
            $out = preg_replace(
                '#^\s*(?:<br\s*/?>\s*|&nbsp;\s*|\xc2\xa0\s*)*(?:<div\b[^>]*>\s*)*' .
                '<p\b[^>]*>\s*<(?:strong|b)\b[^>]*style\s*=\s*["\'][^"\']*teal[^"\']*["\'][^>]*>\s*' .
                'Overview(?:\s+of\s+the\s+Document)?\s*</(?:strong|b)>\s*</p>\s*#iu',
                '',
                $out,
                1
            );

            // Teal on <p> with bold inside (no style on strong)
            $out = preg_replace(
                '#^\s*(?:<br\s*/?>\s*|&nbsp;\s*|\xc2\xa0\s*)*(?:<div\b[^>]*>\s*)*' .
                '<p\b[^>]*style\s*=\s*["\'][^"\']*teal[^"\']*["\'][^>]*>\s*' .
                '<(?:strong|b)\b[^>]*>\s*Overview(?:\s+of\s+the\s+Document)?\s*</(?:strong|b)>\s*</p>\s*#iu',
                '',
                $out,
                1
            );

            if ($out === $prev) {
                break;
            }
        }

        return $out;
    }
}
