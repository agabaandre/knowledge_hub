<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Publication;
use App\Models\PublicationAttachment;
use App\Models\PublicationSummary;
use App\Models\PublicationTag;
use App\Models\PublicationComment;
use App\Models\Favourite;
use Carbon\Carbon;

class PurgeRejectedPublications extends Command
{
    protected $signature = 'publications:purge-rejected {--days=90}';
    protected $description = 'Delete publications rejected for N days without appeal and remove all uploads';

    public function handle()
    {
        $days = intval($this->option('days')) ?: 90;
        $cutoff = Carbon::now()->subDays($days);

        $pubs = Publication::where('is_rejected', 1)
            ->where('is_approved', 0)
            ->whereNull('appealed_at')
            ->whereNotNull('rejected_at')
            ->where('rejected_at', '<=', $cutoff)
            ->get();

        $deletedCount = 0;

        foreach ($pubs as $pub) {
            // delete attachments files
            $attachments = $pub->attachments()->get();
            foreach ($attachments as $att) {
                $this->deleteFile(storage_path('/app/public/uploads/publications/'.$att->file));
            }
            PublicationAttachment::where('publication_id', $pub->id)->delete();

            // delete summaries and their files
            $summaries = PublicationSummary::where('resource_id', $pub->id)->get();
            foreach ($summaries as $sum) {
                if ($sum->file_path) {
                    $this->deleteFile(storage_path('/app/public/uploads/publications/summaries/'.$sum->file_path));
                }
            }
            PublicationSummary::where('resource_id', $pub->id)->delete();

            // delete tags, comments, favourites
            PublicationTag::where('publication_id', $pub->id)->delete();
            PublicationComment::where('publication_id', $pub->id)->delete();
            Favourite::where('publication_id', $pub->id)->delete();

            // delete cover file if not default
            $coverName = $pub->getAttributes()['cover'] ?? null;
            if ($coverName && $coverName !== 'cover.jpg') {
                $this->deleteFile(storage_path('/app/public/uploads/publications/'.$coverName));
            }

            $pub->delete();
            $deletedCount++;
        }

        $this->info("Purged {$deletedCount} rejected publications (>{$days} days old)");
        return Command::SUCCESS;
    }

    private function deleteFile(string $path): void
    {
        try {
            if (is_file($path)) { @unlink($path); }
        } catch (\Throwable $e) {
            // swallow
        }
    }
}


