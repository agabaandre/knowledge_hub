<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PdfChatController;
use Illuminate\Http\Request;

/**
 * Passport/JWT API surface for Khub AI Assistant (same behaviour as web `ai/pdf-chat/*`).
 */
class AssistantApiController extends Controller
{
    public function __construct(
        private PdfChatController $pdfChat
    ) {}

    /**
     * @OA\Post(
     *     path="/api/ai/assistant/session",
     *     operationId="aiAssistantSession",
     *     tags={"AI Operations"},
     *     summary="Start or resume Khub AI Assistant session",
     *     description="Creates or returns a chat session for a publication (PDF ChatPDF or publication context) or a forum thread. Send exactly one of `publication_id` or `forum_id`. For publications, optional `attachment_id` targets a specific PDF; `assistant_mode` can be `auto`, `chatpdf`, `publication`, or `forum` (forced when using `forum_id`).",
     *     security={{"bearer_token":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             @OA\Property(property="publication_id", type="integer", nullable=true, example=42, description="Use with attachment_id for PDF chat; XOR forum_id"),
     *             @OA\Property(property="forum_id", type="integer", nullable=true, example=7, description="Forum thread id; XOR publication_id"),
     *             @OA\Property(property="attachment_id", type="integer", nullable=true, description="Publication PDF attachment id for ChatPDF"),
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
     *     summary="Send a message to Khub AI Assistant",
     *     description="Send a user message for an existing session. Use the same scope as session creation (`publication_id` + optional `attachment_id`, or `forum_id`). Responses are streamed as `text/plain` when `stream` is true (default); set `stream` to false for a single JSON body with `content`. For comparisons across resources, ask the assistant in natural language within this chat instead of a separate compare endpoint.",
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
     *             @OA\Property(property="publication_id", type="integer", nullable=true),
     *             @OA\Property(property="attachment_id", type="integer", nullable=true),
     *             @OA\Property(property="forum_id", type="integer", nullable=true)
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
