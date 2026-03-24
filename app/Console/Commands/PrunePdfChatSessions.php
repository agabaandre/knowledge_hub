<?php

namespace App\Console\Commands;

use App\Models\PdfChatSession;
use Illuminate\Console\Command;

class PrunePdfChatSessions extends Command
{
    protected $signature = 'pdf-chat:prune {--days=7 : Delete sessions older than this many days}';

    protected $description = 'Delete PDF chat sessions (and their messages) older than one week';

    public function handle()
    {
        if (!(settings()->enable_ai_chat_prune ?? true)) {
            $this->info('AI chat cleanup is disabled in admin settings. Skipping prune.');
            return Command::SUCCESS;
        }

        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days);

        $count = PdfChatSession::where('created_at', '<', $cutoff)->delete();

        $this->info("Deleted {$count} PDF chat session(s) older than {$days} days.");

        return Command::SUCCESS;
    }
}
