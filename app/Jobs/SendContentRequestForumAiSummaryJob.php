<?php

namespace App\Jobs;

use App\Jobs\SendMailJob;
use App\Models\ContentRequest;
use App\Services\AIService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendContentRequestForumAiSummaryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $contentRequestId, public ?int $forumId = null)
    {
        $this->onQueue('default');
    }

    public function handle(): void
    {
        $cr = ContentRequest::query()->find($this->contentRequestId);
        $forumId = $this->forumId ?? ($cr ? (int) $cr->referral_forum_id : 0);
        if (! $cr || ! $cr->email || $forumId < 1) {
            return;
        }

        try {
            $ai = app(AIService::class);
            $result = $ai->summarise($forumId, 1, 'en', 'Focus on what the requester needs to know; keep the tone helpful and concise.');
            $html = $result['content'] ?? '';
            if (trim(strip_tags($html)) === '') {
                return;
            }

            SendMailJob::dispatch([
                'to' => $cr->email,
                'subject' => 'AI overview: '.$cr->subject,
                'title' => 'Discussion overview',
                'body' => view('emails.content_request_forum_ai_summary', [
                    'contentRequest' => $cr,
                    'forumUrl' => url('forums/thread?id='.$forumId),
                    'summaryHtml' => $html,
                ])->render(),
            ])->onQueue('default');
        } catch (\Throwable $e) {
            Log::warning('SendContentRequestForumAiSummaryJob: skipped', [
                'content_request_id' => $this->contentRequestId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
