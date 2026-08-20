<?php

namespace App\Console\Commands;

use App\Services\ContentWipeService;
use Illuminate\Console\Command;

class WipeHubContentCommand extends Command
{
    protected $signature = 'khub:wipe-content
        {--force : Required confirmation flag}
        {--phrase= : Must match hub_content.wipe_confirmation_phrase}
        {--keep-communities : Do not wipe communities of practice}';

    protected $description = 'Delete all publications, forums, comments, access logs, authors (and optionally communities), then reset auto-increment. Requires HUB_ALLOW_CONTENT_WIPE=true.';

    public function handle(ContentWipeService $wipe): int
    {
        if (! config('hub_content.allow_content_wipe')) {
            $this->error('HUB_ALLOW_CONTENT_WIPE is not enabled.');

            return self::FAILURE;
        }

        if (! $this->option('force')) {
            $this->error('Refusing to run without --force.');

            return self::FAILURE;
        }

        $expected = (string) config('hub_content.wipe_confirmation_phrase', 'WIPE');
        $phrase = (string) ($this->option('phrase') ?: '');
        if ($phrase !== $expected) {
            $this->error('Confirmation phrase mismatch. Pass --phrase='.$expected);

            return self::FAILURE;
        }

        if (! $this->confirm('This permanently deletes publications, forums, and comments. Continue?', false)) {
            $this->warn('Cancelled.');

            return self::SUCCESS;
        }

        $result = $wipe->wipe(! $this->option('keep-communities'));
        foreach ($result['tables'] as $table => $count) {
            $this->line(sprintf('%-45s %d', $table, $count));
        }
        $this->info('AUTO_INCREMENT reset on: '.implode(', ', $result['resets']));

        return self::SUCCESS;
    }
}
