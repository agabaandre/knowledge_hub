<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use App\Models\Publication;
use App\Models\PublicationAttachment;
use App\Models\PdfChatSession;
use App\Models\PdfChatMessage;
use App\Services\ChatGPTService;
use App\Services\ChatPDFService;
use App\Support\ForumAssistantContext;
use App\Support\PublicationAssistantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PdfChatController extends Controller
{
    private const MAX_MESSAGES_FOR_API = 6;

    public function __construct(
        private ChatPDFService $chatPdf,
        private ChatGPTService $chatGpt
    ) {
    }

    private function hasAttachmentIdColumn(): bool
    {
        return Schema::hasColumn('pdf_chat_sessions', 'attachment_id');
    }

    private function resolveUserId(Request $request): ?int
    {
        return $request->user()?->id;
    }

    /**
     * Get or create a chat session: forum thread (GPT), publication (ChatPDF or GPT), reusing pdf_chat_sessions.
     */
    public function getOrCreateSession(Request $request)
    {
        try {
            $request->validate([
                'publication_id' => 'nullable|integer|required_without:forum_id',
                'forum_id' => 'nullable|integer|required_without:publication_id|exists:forums,id',
                'attachment_id' => 'nullable|integer',
                'assistant_mode' => 'nullable|string|in:chatpdf,publication,auto,forum',
            ]);

            if ($request->filled('publication_id') && $request->filled('forum_id')) {
                return response()->json(['error' => 'Send either publication_id or forum_id, not both.'], 422);
            }

            $userId = $this->resolveUserId($request);

            if ($request->filled('forum_id')) {
                return $this->getOrCreateForumSession((int) $request->forum_id, $userId);
            }

            $publicationId = (int) $request->publication_id;
            $attachmentId = $request->attachment_id ? (int) $request->attachment_id : null;

            $publication = Publication::with('attachments')->find($publicationId);
            if (! $publication) {
                return response()->json(['error' => 'Publication not found.'], 404);
            }

            $assistantMode = $this->resolveAssistantMode((string) $request->input('assistant_mode', 'auto'), $publication, $attachmentId);

            if ($assistantMode === 'chatpdf' && $attachmentId) {
                $attachment = PublicationAttachment::where('id', $attachmentId)
                    ->where('publication_id', $publicationId)
                    ->first();
                if (! $attachment || ! $attachment->is_pdf) {
                    return response()->json(['error' => 'Attachment not found or is not a PDF.'], 422);
                }
            }

            if ($assistantMode === 'publication') {
                $attachmentId = null;
            }

            $sessionQuery = PdfChatSession::where('publication_id', $publicationId)
                ->where(function ($q) use ($assistantMode) {
                    if ($assistantMode === 'chatpdf') {
                        $q->where('assistant_mode', 'chatpdf')->orWhereNull('assistant_mode');
                    } else {
                        $q->where('assistant_mode', 'publication');
                    }
                })
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

            if ($assistantMode === 'publication') {
                if ($session) {
                    $messages = $userId
                        ? $session->messages()->get()->map(fn ($m) => ['role' => $m->role, 'content' => $m->content])
                        : [];

                    return response()->json([
                        'session_id' => $session->id,
                        'source_id' => null,
                        'assistant_mode' => 'publication',
                        'messages' => $messages,
                    ]);
                }

                $data = [
                    'user_id' => $userId,
                    'publication_id' => $publicationId,
                    'source_id' => null,
                    'assistant_mode' => 'publication',
                ];
                if ($this->hasAttachmentIdColumn()) {
                    $data['attachment_id'] = null;
                }
                $session = PdfChatSession::create($data);

                return response()->json([
                    'session_id' => $session->id,
                    'source_id' => null,
                    'assistant_mode' => 'publication',
                    'messages' => [],
                ]);
            }

            // --- ChatPDF (PDF attachment or main PDF) ---
            $pdfUrl = null;
            $pdfPath = null;
            if ($attachmentId) {
                $attachment = PublicationAttachment::where('id', $attachmentId)
                    ->where('publication_id', $publicationId)
                    ->first();
                $pdfUrl = $attachment->file_url ?? null;
                $pdfPath = $attachment->file_path ?? null;
            } else {
                $pdfUrl = $publication->publication_pdf_url;
                $pdfPath = $publication->publication_pdf_path;
            }

            if (! $pdfUrl && ! $pdfPath) {
                return response()->json(['error' => 'This publication has no PDF available for chat.'], 422);
            }

            if ($session && ! empty($session->source_id)) {
                $messages = $userId
                    ? $session->messages()->get()->map(fn ($m) => ['role' => $m->role, 'content' => $m->content])
                    : [];

                return response()->json([
                    'session_id' => $session->id,
                    'source_id' => $session->source_id,
                    'assistant_mode' => 'chatpdf',
                    'messages' => $messages,
                ]);
            }

            if (! $session) {
                $data = [
                    'user_id' => $userId,
                    'publication_id' => $publicationId,
                    'source_id' => null,
                    'assistant_mode' => 'chatpdf',
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
            if (! $sourceId && $pdfPath) {
                $sourceId = $this->chatPdf->getSourceIdFromFile($pdfPath);
            }

            if (! $sourceId) {
                Log::warning('ChatPDF: could not obtain sourceId for publication '.$publicationId.' attachment '.$attachmentId);

                return response()->json(['error' => 'Could not load the PDF for chat. Please try again later.'], 502);
            }

            $session->update(['source_id' => $sourceId, 'assistant_mode' => 'chatpdf']);

            $messages = $userId
                ? $session->messages()->get()->map(fn ($m) => ['role' => $m->role, 'content' => $m->content])
                : [];

            return response()->json([
                'session_id' => $session->id,
                'source_id' => $sourceId,
                'assistant_mode' => 'chatpdf',
                'messages' => $messages,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('PdfChat getOrCreateSession error: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'publication_id' => $request->input('publication_id'),
                'forum_id' => $request->input('forum_id'),
            ]);

            return response()->json([
                'error' => 'Could not start chat. Please try again.',
            ], 500);
        }
    }

    private function getOrCreateForumSession(int $forumId, ?int $userId)
    {
        if (! Forum::query()->whereKey($forumId)->exists()) {
            return response()->json(['error' => 'Forum thread not found.'], 404);
        }

        $session = $this->findForumSession($forumId, $userId);
        if (! $session) {
            $session = $this->createForumChatSession($forumId, $userId);
        }

        $messages = $userId
            ? $session->messages()->get()->map(fn ($m) => ['role' => $m->role, 'content' => $m->content])
            : [];

        return response()->json([
            'session_id' => $session->id,
            'source_id' => null,
            'assistant_mode' => 'forum',
            'context_type' => 'forum',
            'messages' => $messages,
        ]);
    }

    private function findForumSession(int $forumId, ?int $userId): ?PdfChatSession
    {
        return PdfChatSession::where('forum_id', $forumId)
            ->where('assistant_mode', 'forum')
            ->when($userId !== null, fn ($q) => $q->where('user_id', $userId))
            ->when($userId === null, fn ($q) => $q->whereNull('user_id'))
            ->first();
    }

    private function createForumChatSession(int $forumId, ?int $userId): PdfChatSession
    {
        $data = [
            'user_id' => $userId,
            'publication_id' => null,
            'forum_id' => $forumId,
            'source_id' => null,
            'assistant_mode' => 'forum',
        ];
        if ($this->hasAttachmentIdColumn()) {
            $data['attachment_id'] = null;
        }

        return PdfChatSession::create($data);
    }

    private function resolveAssistantMode(string $requested, Publication $publication, ?int $attachmentId): string
    {
        $publication->loadMissing('attachments');
        $pdfSources = $publication->pdf_sources;
        $pdfSourceCount = is_array($pdfSources) ? count($pdfSources) : 0;

        // ChatPDF is single-document. Multiple PDFs on the resource → GPT context with text from all PDFs.
        if ($pdfSourceCount > 1 && ! $attachmentId) {
            if ($requested === 'chatpdf') {
                return 'publication';
            }

            return 'publication';
        }

        if ($requested === 'publication') {
            return 'publication';
        }
        if ($requested === 'chatpdf') {
            return 'chatpdf';
        }
        if ($attachmentId) {
            $attachment = PublicationAttachment::where('id', $attachmentId)
                ->where('publication_id', $publication->id)
                ->first();
            if ($attachment && $attachment->is_pdf && ($attachment->file_url || $attachment->file_path)) {
                return 'chatpdf';
            }

            return 'publication';
        }

        if ($publication->publication_pdf_url || $publication->publication_pdf_path) {
            return 'chatpdf';
        }

        return 'publication';
    }

    /**
     * Send a message and optionally stream the response.
     */
    public function sendMessage(Request $request)
    {
        try {
            $request->validate([
                'publication_id' => 'nullable|integer|required_without:forum_id',
                'forum_id' => 'nullable|integer|required_without:publication_id|exists:forums,id',
                'session_id' => 'nullable|integer',
                'attachment_id' => 'nullable|integer',
                'message' => 'required|string|max:4000',
                'stream' => 'nullable|boolean',
            ]);

            if ($request->filled('publication_id') && $request->filled('forum_id')) {
                return response()->json(['error' => 'Send either publication_id or forum_id, not both.'], 422);
            }

            $sessionId = $request->session_id ? (int) $request->session_id : null;
            $userMessage = $request->message;
            $stream = (bool) $request->get('stream', true);

            $userId = $this->resolveUserId($request);

            if ($request->filled('forum_id')) {
                return $this->sendForumMessage((int) $request->forum_id, $sessionId, $userId, $userMessage, $stream);
            }

            $publicationId = (int) $request->publication_id;
            $attachmentId = $request->attachment_id ? (int) $request->attachment_id : null;

            $session = $this->resolveSession($publicationId, $sessionId, $attachmentId, $userId);
            if (! $session) {
                return response()->json(['error' => 'Session not found or invalid.'], 404);
            }

            if (($session->assistant_mode ?? 'chatpdf') === 'publication') {
                $publication = Publication::with(['comments', 'attachments'])->find($publicationId);
                if (! $publication) {
                    return response()->json(['error' => 'Publication not found.'], 404);
                }
                $systemContent = PublicationAssistantContext::build($publication);
                $history = $this->buildMessagesForApi($session, $userId, $userMessage);
                $openAiMessages = $this->mergeSystemAndHistory($systemContent, $history);

                if ($stream) {
                    return $this->streamPublicationResponse($session, $userId, $userMessage, $openAiMessages);
                }

                $content = $this->chatGpt->chatMessagesComplete($openAiMessages);
                if ($userId) {
                    $this->savePair($session, $userMessage, $content);
                }

                return response()->json([
                    'content' => $content,
                    'references' => [],
                ]);
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

    private function sendForumMessage(int $forumId, ?int $sessionId, ?int $userId, string $userMessage, bool $stream)
    {
        $forum = Forum::query()->find($forumId);
        if (! $forum) {
            return response()->json(['error' => 'Forum thread not found.'], 404);
        }

        if ($sessionId) {
            $session = PdfChatSession::where('id', $sessionId)
                ->where('forum_id', $forumId)
                ->where('assistant_mode', 'forum')
                ->when($userId !== null, fn ($q) => $q->where('user_id', $userId))
                ->when($userId === null, fn ($q) => $q->whereNull('user_id'))
                ->first();
            if (! $session) {
                return response()->json(['error' => 'Session not found or invalid.'], 404);
            }
        } else {
            $session = $this->findForumSession($forumId, $userId);
            if (! $session) {
                $session = $this->createForumChatSession($forumId, $userId);
            }
        }

        $systemContent = ForumAssistantContext::build($forum);
        $history = $this->buildMessagesForApi($session, $userId, $userMessage);
        $openAiMessages = $this->mergeSystemAndHistory($systemContent, $history);

        if ($stream) {
            return $this->streamPublicationResponse($session, $userId, $userMessage, $openAiMessages);
        }

        $content = $this->chatGpt->chatMessagesComplete($openAiMessages);
        if ($userId) {
            $this->savePair($session, $userMessage, $content);
        }

        return response()->json([
            'content' => $content,
            'references' => [],
        ]);
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
            return null;
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
            'assistant_mode' => 'chatpdf',
        ];
        if ($this->hasAttachmentIdColumn()) {
            $data['attachment_id'] = $attachmentId;
        }

        return PdfChatSession::create($data);
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $historyEndsWithNewUser
     * @return array<int, array{role: string, content: string}>
     */
    private function mergeSystemAndHistory(string $systemContent, array $historyEndsWithNewUser): array
    {
        $out = [['role' => 'system', 'content' => $systemContent]];
        $tail = array_slice($historyEndsWithNewUser, -12);

        return array_merge($out, $tail);
    }

    private function streamPublicationResponse(PdfChatSession $session, ?int $userId, string $userMessage, array $openAiMessages): StreamedResponse
    {
        return new StreamedResponse(function () use ($session, $userId, $userMessage, $openAiMessages) {
            $buffer = '';
            $this->chatGpt->chatMessagesStream($openAiMessages, function ($chunk) use (&$buffer) {
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
        $list[] = ['role' => 'user', 'content' => $newUserMessage];
        return array_slice($list, -self::MAX_MESSAGES_FOR_API);
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
