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
use Symfony\Component\HttpFoundation\StreamedResponse;

class PdfChatController extends Controller
{
    private const MAX_MESSAGES_FOR_API = 6;

    public function __construct(private ChatPDFService $chatPdf)
    {
    }

    /**
     * Get or create a PDF chat session for a publication (main PDF or a specific attachment).
     * Returns session id, sourceId, and message history (if logged in).
     */
    public function getOrCreateSession(Request $request)
    {
        $request->validate([
            'publication_id' => 'required|integer|exists:publication,id',
            'attachment_id' => 'nullable|integer',
        ]);

        $publicationId = (int) $request->publication_id;
        $attachmentId = $request->attachment_id ? (int) $request->attachment_id : null;
        $userId = Auth::id();

        $publication = Publication::with('attachments')->findOrFail($publicationId);
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
            return response()->json(['error' => 'This publication has no PDF available for chat.'], 422);
        }

        $session = PdfChatSession::where('publication_id', $publicationId)
            ->where('user_id', $userId)
            ->where(function ($q) use ($attachmentId) {
                if ($attachmentId === null) {
                    $q->whereNull('attachment_id');
                } else {
                    $q->where('attachment_id', $attachmentId);
                }
            })
            ->first();

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
            $session = PdfChatSession::create([
                'user_id' => $userId,
                'publication_id' => $publicationId,
                'attachment_id' => $attachmentId,
                'source_id' => null,
            ]);
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
    }

    /**
     * Send a message and optionally stream the response.
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'publication_id' => 'required|integer|exists:publication,id',
            'session_id' => 'nullable|integer|exists:pdf_chat_sessions,id',
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

        $session = PdfChatSession::where('publication_id', $publicationId)
            ->where('user_id', $userId)
            ->where(function ($q) use ($attachmentId) {
                if ($attachmentId === null) {
                    $q->whereNull('attachment_id');
                } else {
                    $q->where('attachment_id', $attachmentId);
                }
            })
            ->first();

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

        return PdfChatSession::create([
            'user_id' => $userId,
            'publication_id' => $publicationId,
            'attachment_id' => $attachmentId,
            'source_id' => $sourceId,
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
