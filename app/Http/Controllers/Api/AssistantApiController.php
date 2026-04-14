<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PdfChatController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Passport/JWT API surface for Khub AI (same behaviour as web `ai/pdf-chat/*`).
 */
class AssistantApiController extends Controller
{
    /** Shown on session-only responses and in OpenAPI description for integrators. */
    private const CHAT_USAGE_PROMPT = <<<'TXT'
Use this one endpoint for document and forum assistant chat.

• Send exactly one scope: `publication_id` OR `forum_id` (never both).
• For a publication, optional `attachment_id` (must belong to that publication) selects **one** PDF attachment for ChatPDF. Omit it to use the main publication PDF when present, or—when the resource has **several** PDFs and you use `assistant_mode` `auto`/`publication`—to chat over **all** PDFs via GPT context (same as web).
• Optional `assistant_mode`: `auto` (default), `chatpdf`, `publication`, or `forum` (implicit when using `forum_id`).
• Omit `message` (or send an empty string) to only open or resume the session and return prior `messages`.
• Include `message` to chat in the same request: the API always ensures a session for that document or forum first, then sends your message.
• Responses: default `stream: true` returns assistant text as `text/plain` (chunked). Set `stream: false` for JSON `{ "content", "references" }`.
• Optional `session_id`: if you already have one, send it with `message` to skip re-resolving the session (you must still send the same `publication_id` or `forum_id` as required by validation).
TXT;

    public function __construct(
        private PdfChatController $pdfChat
    ) {}

    /**
     * @OA\Post(
     *     path="/api/ai/chat",
     *     operationId="aiChat",
     *     tags={"AI Operations"},
     *     summary="Document or forum assistant (recommended)",
     *     description="Single entry point for Khub AI chat. Send **either** `publication_id` **or** `forum_id` (not both). Always creates or reuses a session for that scope before sending. **No `message`:** JSON with `session_id`, `messages`, and `usage` (integration guide). **With `message`:** if `stream` is true (default), response body is **`text/plain`** streamed chunks (not JSON); if `stream` is false, JSON with `content` and `references`.",
     *     security={{"bearer_token":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             @OA\Property(property="publication_id", type="integer", nullable=true, example=42, description="Document scope; required unless forum_id"),
     *             @OA\Property(property="forum_id", type="integer", nullable=true, example=7, description="Forum thread scope; XOR publication_id"),
     *             @OA\Property(property="attachment_id", type="integer", nullable=true, description="Optional PDF attachment id (`publication_attachments.id`) for single-file ChatPDF; omit for main PDF or multi-PDF GPT context (see `/api/ai/assistant/session`)"),
     *             @OA\Property(property="assistant_mode", type="string", enum={"auto","chatpdf","publication"}, example="auto", description="Ignored for forum scope"),
     *             @OA\Property(property="message", type="string", nullable=true, description="User turn; omit to only create/resume session"),
     *             @OA\Property(property="session_id", type="integer", nullable=true, description="If set with message, use existing session after scope checks"),
     *             @OA\Property(property="stream", type="boolean", example=true, description="When false, assistant reply is JSON with content and references")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="**Session-only:** JSON with session_id, messages, usage. **Chat with stream=true:** `Content-Type: text/plain` body. **Chat with stream=false:** JSON content + references.",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="session_id", type="integer"),
     *             @OA\Property(property="usage", type="string", description="Integration guide; present when no message was sent"),
     *             @OA\Property(property="content", type="string"),
     *             @OA\Property(property="references", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Resource not found"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=502, description="Upstream AI/PDF service error")
     * )
     */
    public function chat(Request $request)
    {
        $message = trim((string) $request->input('message', ''));
        $sessionId = $request->input('session_id');

        if ($message === '') {
            $sessionResponse = $this->pdfChat->getOrCreateSession($request);
            if (! $sessionResponse instanceof JsonResponse || $sessionResponse->status() !== 200) {
                return $sessionResponse;
            }
            $data = $sessionResponse->getData(true);
            if (! is_array($data)) {
                return $sessionResponse;
            }
            $data['usage'] = self::CHAT_USAGE_PROMPT;

            return response()->json($data);
        }

        if ($sessionId) {
            return $this->pdfChat->sendMessage($request);
        }

        $sessionResponse = $this->pdfChat->getOrCreateSession($request);
        if (! $sessionResponse instanceof JsonResponse || $sessionResponse->status() !== 200) {
            return $sessionResponse;
        }
        $data = $sessionResponse->getData(true);
        if (! is_array($data) || empty($data['session_id'])) {
            return $sessionResponse;
        }

        $request->merge(['session_id' => (int) $data['session_id']]);

        return $this->pdfChat->sendMessage($request);
    }

    /**
     * @OA\Post(
     *     path="/api/ai/assistant/session",
     *     operationId="aiAssistantSession",
     *     tags={"AI Operations"},
     *     summary="Start or resume Khub AI session",
     *     description="Creates or returns a chat session for a publication (ChatPDF on one PDF, or GPT over publication metadata + extracted PDF text) or a forum thread. Send exactly one of `publication_id` or `forum_id`. **Attachments (publications only):** optional `attachment_id` is the `publication_attachments.id` of a **PDF** belonging to that publication—use it to open a **single-file** ChatPDF session on that file. Omit `attachment_id` to use the main record PDF when available; if the publication has **multiple** PDFs and you omit `attachment_id`, `auto` resolves to **`publication`** mode so the assistant can use **all** PDFs (extracted text in context). `assistant_mode`: `auto` (default), `chatpdf`, `publication`, or `forum` (implicit for `forum_id`).",
     *     security={{"bearer_token":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             @OA\Property(property="publication_id", type="integer", nullable=true, example=42, description="Use with attachment_id for PDF chat; XOR forum_id"),
     *             @OA\Property(property="forum_id", type="integer", nullable=true, example=7, description="Forum thread id; XOR publication_id"),
     *             @OA\Property(property="attachment_id", type="integer", nullable=true, description="Optional. PDF attachment row id for this publication. When set: ChatPDF session for that file only. When omitted: main PDF or multi-PDF publication mode per `assistant_mode` / `auto`."),
     *             @OA\Property(property="assistant_mode", type="string", enum={"auto","chatpdf","publication","forum"}, example="auto")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Session ready",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="session_id", type="integer", example=1),
     *             @OA\Property(property="source_id", type="string", nullable=true, description="ChatPDF source when mode is chatpdf"),
     *             @OA\Property(property="assistant_mode", type="string", example="publication"),
     *             @OA\Property(property="context_type", type="string", nullable=true, example="forum"),
     *             @OA\Property(property="messages", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function session(Request $request)
    {
        return $this->pdfChat->getOrCreateSession($request);
    }

    /**
     * @OA\Post(
     *     path="/api/ai/assistant/message",
     *     operationId="aiAssistantMessage",
     *     tags={"AI Operations"},
     *     summary="Send a message to Khub AI",
     *     description="Send a user message for an existing session. **Repeat the same scope as `/api/ai/assistant/session`:** `publication_id` with the **same** optional `attachment_id` (if the session was opened on one attachment), or `forum_id`. Include `session_id` from the session response when possible. Responses are streamed as `text/plain` when `stream` is true (default); set `stream` to false for JSON with `content` and `references` (ChatPDF).",
     *     security={{"bearer_token":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"message"},
     *
     *             @OA\Property(property="session_id", type="integer", nullable=true, description="From session response; omit only if a single session already exists for this user and scope"),
     *             @OA\Property(property="message", type="string", example="Summarise the main points in bullet form."),
     *             @OA\Property(property="stream", type="boolean", example=true),
     *             @OA\Property(property="publication_id", type="integer", nullable=true, description="Same publication as session; required with publication scope"),
     *             @OA\Property(property="attachment_id", type="integer", nullable=true, description="Must match session if ChatPDF was opened on a specific PDF attachment; omit if session used main PDF or multi-PDF publication mode"),
     *             @OA\Property(property="forum_id", type="integer", nullable=true, description="Same forum thread as session")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Assistant reply (JSON when stream=false)",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="content", type="string"),
     *             @OA\Property(property="references", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Session or resource not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function message(Request $request)
    {
        return $this->pdfChat->sendMessage($request);
    }
}
