<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use App\Models\PublicationAttachment;
use App\Models\PdfChatSession;
use App\Models\PdfChatMessage;
use App\Services\ChatPDFService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PdfChatController extends Controller
{
    private const MAX_MESSAGES_FOR_API = 6;
    private const MAX_COMMENT_CONTEXT_CHARS = 2500;
    private const MAX_COMMENT_ITEMS = 20;

    public function __construct(private ChatPDFService $chatPdf)
    {
    }

    private function hasAttachmentIdColumn(): bool
    {
        return Schema::hasColumn('pdf_chat_sessions', 'attachment_id');
    }

    /**
     * Get or create a PDF chat session for a publication (main PDF or a specific attachment).
     * Returns session id, sourceId, and message history (if logged in).
     */
    public function getOrCreateSession(Request $request)
    {
        try {
            $request->validate([
                'publication_id' => 'required|integer',
                'attachment_id' => 'nullable|integer',
            ]);

            $publicationId = (int) $request->publication_id;
            $attachmentId = $request->attachment_id ? (int) $request->attachment_id : null;
            $userId = Auth::id();

            $publication = Publication::with('attachments')->find($publicationId);
            if (!$publication) {
                return response()->json(['error' => 'Publication not found.'], 404);
            }

            $pdfUrl = null;
            $pdfPath = null;

            if ($attachmentId) {
                $attachment = PublicationAttachment::where('id', $attachmentId)
                    ->where('publication_id', $publicationId)
                    ->first();
                if (!$attachment || !$attachment->is_pdf) {
                    return response()->json(['error' => 'Attachment not found or is not a PDF.'], 422);
                }
                $pdfUrl = $attachment->file_url;
                $pdfPath = $attachment->file_path;
            } else {
                $pdfUrl = $publication->publication_pdf_url;
                $pdfPath = $publication->publication_pdf_path;
            }

            if (!$pdfUrl && !$pdfPath) {
                $pubLink = trim((string) ($publication->publication ?? ''));
                $fileTypeName = strtolower((string) ($publication->file_type->name ?? ''));
                $mediaEligible = $pubLink !== '' && (
                    is_video_platform_url($pubLink) ||
                    is_direct_video_file_url($pubLink) ||
                    is_direct_audio_file_url($pubLink) ||
                    strpos($fileTypeName, 'audio') !== false
                );
                if ($mediaEligible) {
                    $pdfUrl = $pubLink; // ChatPDF add-url fallback for media links
                } else {
                    return response()->json(['error' => 'This publication has no PDF or supported media URL available for chat.'], 422);
                }
            }

            $sessionQuery = PdfChatSession::where('publication_id', $publicationId)
                ->when($userId !== null, fn ($q) => $q->where('user_id', $userId))
                ->when($userId === null, fn ($q) => $q->whereNull('user_id'));

            if ($this->hasAttachmentIdColumn()) {
                $sessionQuery->where(function ($q) use ($attachmentId) {
                    if ($attachmentId === null) {
                        $q->whereNull('attachment_id');
                    } else {
                        $q->where('attachment_id', $attachmentId);
                    }
                });
            }

            $session = $sessionQuery->first();

            if ($session && !empty($session->source_id)) {
                $messages = $userId
                    ? $session->messages()->get()->map(fn ($m) => ['role' => $m->role, 'content' => $m->content])
                    : [];
                return response()->json([
                    'session_id' => $session->id,
                    'source_id' => $session->source_id,
                    'messages' => $messages,
                ]);
            }

            if (!$session) {
                $data = [
                    'user_id' => $userId,
                    'publication_id' => $publicationId,
                    'source_id' => null,
                ];
                if ($this->hasAttachmentIdColumn()) {
                    $data['attachment_id'] = $attachmentId;
                }
                $session = PdfChatSession::create($data);
            }

            $sourceId = null;
            if ($pdfUrl) {
                $sourceId = $this->chatPdf->getSourceIdFromUrl($pdfUrl);
            }
            if (!$sourceId && $pdfPath) {
                $sourceId = $this->chatPdf->getSourceIdFromFile($pdfPath);
            }

            if (!$sourceId) {
                Log::warning('ChatPDF: could not obtain sourceId for publication ' . $publicationId . ' attachment ' . $attachmentId);
                return response()->json(['error' => 'Could not load the PDF for chat. Please try again later.'], 502);
            }

            $session->update(['source_id' => $sourceId]);

            $messages = $userId
                ? $session->messages()->get()->map(fn ($m) => ['role' => $m->role, 'content' => $m->content])
                : [];

            return response()->json([
                'session_id' => $session->id,
                'source_id' => $sourceId,
                'messages' => $messages,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('PdfChat getOrCreateSession error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'publication_id' => $request->input('publication_id'),
            ]);
            return response()->json([
                'error' => 'Could not start chat. Please try again.',
            ], 500);
        }
    }

    /**
     * Send a message and optionally stream the response.
     */
    public function sendMessage(Request $request)
    {
        try {
            $request->validate([
                'publication_id' => 'required|integer',
                'session_id' => 'nullable|integer',
                'attachment_id' => 'nullable|integer',
                'message' => 'required|string|max:4000',
                'stream' => 'nullable|boolean',
            ]);

            $publicationId = (int) $request->publication_id;
            $sessionId = $request->session_id ? (int) $request->session_id : null;
            $attachmentId = $request->attachment_id ? (int) $request->attachment_id : null;
            $userMessage = $request->message;
            $stream = (bool) $request->get('stream', true);

            $userId = Auth::id();

            $session = $this->resolveSession($publicationId, $sessionId, $attachmentId, $userId);
            if (!$session) {
                return response()->json(['error' => 'Session not found or invalid.'], 404);
            }

            $messages = $this->buildMessagesForApi($session, $userId, $userMessage);

            if ($stream) {
                return $this->streamResponse($session, $userId, $userMessage, $messages);
            }

            $response = $this->chatPdf->chat($session->source_id, $messages, true);
            $content = $response->content ?? '';
            if (isset($response->error)) {
                return response()->json(['error' => $response->error], 502);
            }

            if ($userId) {
                $this->savePair($session, $userMessage, $content);
            }

            return response()->json([
                'content' => $content,
                'references' => $response->references ?? [],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('PdfChat sendMessage error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'error' => 'Could not send message. Please try again.',
            ], 500);
        }
    }

    private function resolveSession(int $publicationId, ?int $sessionId, ?int $attachmentId, ?int $userId): ?PdfChatSession
    {
        if ($sessionId) {
            $session = PdfChatSession::where('id', $sessionId)
                ->where('publication_id', $publicationId)
                ->when($userId !== null, fn ($q) => $q->where('user_id', $userId))
                ->when($userId === null, fn ($q) => $q->whereNull('user_id'))
                ->first();
            return $session;
        }

        $sessionQuery = PdfChatSession::where('publication_id', $publicationId)
            ->when($userId !== null, fn ($q) => $q->where('user_id', $userId))
            ->when($userId === null, fn ($q) => $q->whereNull('user_id'));

        if ($this->hasAttachmentIdColumn()) {
            $sessionQuery->where(function ($q) use ($attachmentId) {
                if ($attachmentId === null) {
                    $q->whereNull('attachment_id');
                } else {
                    $q->where('attachment_id', $attachmentId);
                }
            });
        }

        $session = $sessionQuery->first();

        if ($session) {
            return $session;
        }

        $publication = Publication::with('attachments')->find($publicationId);
        if (!$publication) {
            return null;
        }

        $pdfUrl = null;
        $pdfPath = null;
        if ($attachmentId) {
            $attachment = PublicationAttachment::where('id', $attachmentId)
                ->where('publication_id', $publicationId)
                ->first();
            if (!$attachment || !$attachment->is_pdf) {
                return null;
            }
            $pdfUrl = $attachment->file_url;
            $pdfPath = $attachment->file_path;
        } else {
            $pdfUrl = $publication->publication_pdf_url;
            $pdfPath = $publication->publication_pdf_path;
        }

        if (!$pdfUrl && !$pdfPath) {
            $pubLink = trim((string) ($publication->publication ?? ''));
            $fileTypeName = strtolower((string) ($publication->file_type->name ?? ''));
            $mediaEligible = $pubLink !== '' && (
                is_video_platform_url($pubLink) ||
                is_direct_video_file_url($pubLink) ||
                is_direct_audio_file_url($pubLink) ||
                strpos($fileTypeName, 'audio') !== false
            );
            if ($mediaEligible) {
                $pdfUrl = $pubLink;
            } else {
                return null;
            }
        }

        $sourceId = $pdfUrl ? $this->chatPdf->getSourceIdFromUrl($pdfUrl) : null;
        if (!$sourceId && $pdfPath) {
            $sourceId = $this->chatPdf->getSourceIdFromFile($pdfPath);
        }
        if (!$sourceId) {
            return null;
        }

        $data = [
            'user_id' => $userId,
            'publication_id' => $publicationId,
            'source_id' => $sourceId,
        ];
        if ($this->hasAttachmentIdColumn()) {
            $data['attachment_id'] = $attachmentId;
        }
        return PdfChatSession::create($data);
    }

    private function buildMessagesForApi(PdfChatSession $session, ?int $userId, string $newUserMessage): array
    {
        $list = [];
        if ($userId) {
            $list = $session->messages()
                ->orderBy('created_at')
                ->get()
                ->map(fn ($m) => ['role' => $m->role, 'content' => $m->content])
                ->toArray();
        }
        $liveCommentsContext = $this->buildLiveCommentsContext((int) $session->publication_id);
        if ($liveCommentsContext !== null) {
            $list[] = ['role' => 'user', 'content' => $liveCommentsContext];
        }
        $list[] = ['role' => 'user', 'content' => $newUserMessage];
        return array_slice($list, -self::MAX_MESSAGES_FOR_API);
    }

    private function buildLiveCommentsContext(int $publicationId): ?string
    {
        $publication = Publication::with(['comments' => function ($q) {
            $q->latest()->take(self::MAX_COMMENT_ITEMS);
        }])->find($publicationId);

        if (!$publication || !$publication->comments || $publication->comments->isEmpty()) {
            return null;
        }

        $lines = [];
        foreach ($publication->comments as $comment) {
            $body = trim(strip_tags((string) ($comment->comment ?? '')));
            if ($body === '') {
                continue;
            }

            $author = trim((string) ($comment->commentor_name ?? $comment->author ?? 'Anonymous'));
            $date = '';
            if (!empty($comment->created_at)) {
                try {
                    $date = $comment->created_at->format('Y-m-d');
                } catch (\Throwable $e) {
                    $date = '';
                }
            }
            $prefix = '- ' . ($author !== '' ? $author : 'Anonymous');
            if ($date !== '') {
                $prefix .= ' (' . $date . ')';
            }
            $lines[] = $prefix . ': ' . $body;
        }

        if (empty($lines)) {
            return null;
        }

        $context = "Live community comments for sentiment/context (latest first). Use these as additional signals alongside the document content:\n" . implode("\n", $lines);
        if (strlen($context) > self::MAX_COMMENT_CONTEXT_CHARS) {
            $context = substr($context, 0, self::MAX_COMMENT_CONTEXT_CHARS) . "\n[Truncated due to length]";
        }
        return $context;
    }

    private function savePair(PdfChatSession $session, string $userContent, string $assistantContent): void
    {
        PdfChatMessage::create([
            'pdf_chat_session_id' => $session->id,
            'role' => 'user',
            'content' => $userContent,
        ]);
        PdfChatMessage::create([
            'pdf_chat_session_id' => $session->id,
            'role' => 'assistant',
            'content' => $assistantContent,
        ]);
    }

    private function streamResponse(PdfChatSession $session, ?int $userId, string $userMessage, array $messages): StreamedResponse
    {
        return new StreamedResponse(function () use ($session, $userId, $userMessage, $messages) {
            $buffer = '';
            $this->chatPdf->chatStream($session->source_id, $messages, function ($chunk) use (&$buffer) {
                $buffer .= $chunk;
                echo $chunk;
                if (ob_get_level()) {
                    ob_flush();
                }
                flush();
            });

            if ($userId && $buffer !== '') {
                $this->savePair($session, $userMessage, $buffer);
            }
        }, 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }
}
