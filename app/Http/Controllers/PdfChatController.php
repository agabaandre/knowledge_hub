<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use App\Models\Publication;
use App\Models\PublicationAttachment;
use App\Models\PdfChatSession;
use App\Models\PdfChatMessage;
use App\Services\ChatGPTService;
use App\Services\ChatPDFService;
use App\Support\AiConfig;
use App\Support\ForumAssistantContext;
use App\Support\ForumsListingAssistantContext;
use App\Support\PublicationAssistantContext;
use App\Support\PublicationChatPdfResolver;
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

    private function hasPdfSelectionKeyColumn(): bool
    {
        return Schema::hasColumn('pdf_chat_sessions', 'pdf_selection_key');
    }

    private function hasChatpdfMetaColumn(): bool
    {
        return Schema::hasColumn('pdf_chat_sessions', 'chatpdf_meta');
    }

    private function resolveUserId(Request $request): ?int
    {
        return $request->user()?->id;
    }

    /**
     * @return list<array{role: string, content: string}>
     */
    private function sessionMessagesForUser(PdfChatSession $session, ?int $userId): array
    {
        if (! $userId) {
            return [];
        }

        return $session->messages()
            ->get()
            ->map(fn ($m) => ['role' => $m->role, 'content' => $m->content])
            ->values()
            ->all();
    }

    /**
     * Get or create a chat session: forum thread (GPT), publication (ChatPDF or GPT), reusing pdf_chat_sessions.
     */
    public function getOrCreateSession(Request $request)
    {
        try {
            $request->validate([
                'publication_id' => 'nullable|integer',
                'forum_id' => 'nullable|integer|exists:forums,id',
                'forum_ids' => 'nullable|array',
                'forum_ids.*' => 'integer|exists:forums,id',
                'attachment_id' => 'nullable|integer',
                'pdf_source_keys' => 'nullable|array',
                'pdf_source_keys.*' => 'nullable|string|max:32',
                'assistant_mode' => 'nullable|string|in:chatpdf,publication,auto,forum,forums_index',
            ]);

            if ($request->filled('publication_id') && $request->filled('forum_id')) {
                return response()->json(['error' => 'Send either publication_id or forum_id, not both.'], 422);
            }

            $userId = $this->resolveUserId($request);

            if ($request->input('assistant_mode') === 'forums_index') {
                return $this->getOrCreateForumsIndexSession($userId);
            }

            if ($request->filled('forum_id')) {
                return $this->getOrCreateForumSession((int) $request->forum_id, $userId);
            }

            if (! $request->filled('publication_id')) {
                return response()->json(['error' => 'publication_id, forum_id, or forums_index mode is required.'], 422);
            }

            $publicationId = (int) $request->publication_id;
            $attachmentId = $request->attachment_id ? (int) $request->attachment_id : null;
            $requestedPdfSourceKeys = $this->normalizePdfSourceKeysInput($request->input('pdf_source_keys'));

            $publication = Publication::with('attachments')->find($publicationId);
            if (! $publication) {
                return response()->json(['error' => 'Publication not found.'], 404);
            }

            $selectedPdfSourceKeys = PublicationChatPdfResolver::normalizeSelectedKeys(
                $publication,
                $requestedPdfSourceKeys !== [] ? $requestedPdfSourceKeys : $this->pdfSourceKeysFromAttachmentId($publication, $attachmentId)
            );

            $assistantMode = $this->resolveAssistantMode(
                (string) $request->input('assistant_mode', 'auto'),
                $publication,
                PublicationChatPdfResolver::primaryAttachmentId($selectedPdfSourceKeys)
            );

            if ($assistantMode === 'publication') {
                $attachmentId = null;
                $selectedPdfSourceKeys = [];
            } elseif ($selectedPdfSourceKeys === []) {
                return response()->json(['error' => 'This publication has no PDF available for chat.'], 422);
            } else {
                $attachmentId = PublicationChatPdfResolver::primaryAttachmentId($selectedPdfSourceKeys);
            }

            if ($assistantMode === 'chatpdf' && $attachmentId) {
                $attachment = PublicationAttachment::where('id', $attachmentId)
                    ->where('publication_id', $publicationId)
                    ->first();
                if (! $attachment || ! $attachment->is_pdf) {
                    return response()->json(['error' => 'Attachment not found or is not a PDF.'], 422);
                }
            }

            $pdfSelectionKey = $selectedPdfSourceKeys !== []
                ? PublicationChatPdfResolver::selectionKey($selectedPdfSourceKeys)
                : null;

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

            if ($assistantMode === 'chatpdf' && $this->hasPdfSelectionKeyColumn() && $pdfSelectionKey !== null) {
                $sessionQuery->where('pdf_selection_key', $pdfSelectionKey);
            } elseif ($this->hasAttachmentIdColumn()) {
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
                    return response()->json($this->publicationSessionPayload(
                        $session,
                        $publication,
                        'publication',
                        null,
                        null,
                        $this->sessionMessagesForUser($session, $userId)
                    ));
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

                return response()->json($this->publicationSessionPayload($session, $publication, 'publication', null, null, []));
            }

            // --- ChatPDF (one or more PDF attachments / main PDF) ---
            $existingMeta = ($session && $this->hasChatpdfMetaColumn())
                ? ($session->chatpdf_meta ?? null)
                : null;

            if ($session && $this->sessionHasReadyChatPdfSources($session, $selectedPdfSourceKeys, $existingMeta)) {
                return response()->json($this->publicationSessionPayload(
                    $session,
                    $publication,
                    'chatpdf',
                    $session->source_id,
                    $attachmentId,
                    $this->sessionMessagesForUser($session, $userId),
                    $selectedPdfSourceKeys
                ));
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
                if ($this->hasPdfSelectionKeyColumn()) {
                    $data['pdf_selection_key'] = $pdfSelectionKey;
                }
                $session = PdfChatSession::create($data);
            }

            try {
                $chatpdfMeta = $this->ensureChatPdfMetaForSelection(
                    $publication,
                    $selectedPdfSourceKeys,
                    is_array($existingMeta) ? $existingMeta : null
                );
            } catch (\RuntimeException $e) {
                Log::warning('ChatPDF: could not upload selected PDFs for publication '.$publicationId, [
                    'keys' => $selectedPdfSourceKeys,
                    'message' => $e->getMessage(),
                ]);

                return response()->json(['error' => $e->getMessage()], 502);
            }

            $primarySourceId = $this->primaryChatPdfSourceId($chatpdfMeta, $selectedPdfSourceKeys);
            if ($primarySourceId === null) {
                return response()->json(['error' => 'Could not load the PDF for chat. Please try again later.'], 502);
            }

            $updateData = [
                'source_id' => $primarySourceId,
                'assistant_mode' => 'chatpdf',
            ];
            if ($this->hasAttachmentIdColumn()) {
                $updateData['attachment_id'] = $attachmentId;
            }
            if ($this->hasPdfSelectionKeyColumn()) {
                $updateData['pdf_selection_key'] = $pdfSelectionKey;
            }
            if ($this->hasChatpdfMetaColumn()) {
                $updateData['chatpdf_meta'] = $chatpdfMeta;
            }
            $session->update($updateData);

            return response()->json($this->publicationSessionPayload(
                $session,
                $publication,
                'chatpdf',
                $primarySourceId,
                $attachmentId,
                $this->sessionMessagesForUser($session, $userId),
                $selectedPdfSourceKeys
            ));
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

    private function getOrCreateForumsIndexSession(?int $userId)
    {
        $session = PdfChatSession::where('assistant_mode', 'forums_index')
            ->whereNull('publication_id')
            ->whereNull('forum_id')
            ->when($userId !== null, fn ($q) => $q->where('user_id', $userId))
            ->when($userId === null, fn ($q) => $q->whereNull('user_id'))
            ->first();

        if (! $session) {
            $data = [
                'user_id' => $userId,
                'publication_id' => null,
                'forum_id' => null,
                'source_id' => null,
                'assistant_mode' => 'forums_index',
            ];
            if ($this->hasAttachmentIdColumn()) {
                $data['attachment_id'] = null;
            }
            $session = PdfChatSession::create($data);
        }

        $messages = $userId
            ? $session->messages()->get()->map(fn ($m) => ['role' => $m->role, 'content' => $m->content])
            : [];

        return response()->json([
            'session_id' => $session->id,
            'source_id' => null,
            'assistant_mode' => 'forums_index',
            'context_type' => 'forums_index',
            'messages' => $messages,
        ]);
    }

    /**
     * @param  mixed  $raw
     * @return list<int>
     */
    private function normalizeForumIds($raw): array
    {
        if (! is_array($raw)) {
            return [];
        }

        return array_values(array_unique(array_filter(array_map('intval', $raw))));
    }

    private function forumAssistantUnavailableResponse()
    {
        if (AiConfig::chatProviderFallbackChain('forums') !== []) {
            return null;
        }

        return response()->json([
            'error' => 'AI is not configured for forum assistants. An administrator must enable a chat provider (OpenAI, Gemini, DeepSeek, or custom) under Admin → Settings → AI integrations.',
        ], 503);
    }

    private function sendForumsIndexMessage(?int $sessionId, ?int $userId, string $userMessage, bool $stream, array $forumIds)
    {
        if ($response = $this->forumAssistantUnavailableResponse()) {
            return $response;
        }

        if ($sessionId) {
            $session = PdfChatSession::where('id', $sessionId)
                ->where('assistant_mode', 'forums_index')
                ->whereNull('forum_id')
                ->whereNull('publication_id')
                ->when($userId !== null, fn ($q) => $q->where('user_id', $userId))
                ->when($userId === null, fn ($q) => $q->whereNull('user_id'))
                ->first();
            if (! $session) {
                return response()->json(['error' => 'Session not found or invalid.'], 404);
            }
        } else {
            $session = PdfChatSession::where('assistant_mode', 'forums_index')
                ->whereNull('publication_id')
                ->whereNull('forum_id')
                ->when($userId !== null, fn ($q) => $q->where('user_id', $userId))
                ->when($userId === null, fn ($q) => $q->whereNull('user_id'))
                ->first();
            if (! $session) {
                $session = PdfChatSession::create([
                    'user_id' => $userId,
                    'publication_id' => null,
                    'forum_id' => null,
                    'source_id' => null,
                    'assistant_mode' => 'forums_index',
                ]);
            }
        }

        $systemContent = ForumsListingAssistantContext::buildForMessage($userMessage, $forumIds);
        $history = $this->buildMessagesForApi($session, $userId, $userMessage);
        $openAiMessages = $this->mergeSystemAndHistory($systemContent, $history);

        if ($stream) {
            return $this->streamPublicationResponse($session, $userId, $userMessage, $openAiMessages, 'forums');
        }

        $content = $this->chatGpt->chatMessagesComplete($openAiMessages, 'forums');
        if ($userId) {
            $this->savePair($session, $userMessage, $content);
        }

        return response()->json([
            'content' => $content,
            'references' => [],
        ]);
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
        if ($requested === 'publication') {
            return 'publication';
        }

        $publication->loadMissing('attachments');
        $pdfSources = $publication->pdf_sources;
        $hasPdf = is_array($pdfSources) && count($pdfSources) > 0;

        if ($requested === 'chatpdf') {
            return $hasPdf ? 'chatpdf' : 'publication';
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

        if ($hasPdf) {
            return 'chatpdf';
        }

        return 'publication';
    }

    /**
     * @param  list<array{role: string, content: string}>  $messages
     * @param  list<string>  $selectedPdfSourceKeys
     * @return array<string, mixed>
     */
    private function publicationSessionPayload(
        PdfChatSession $session,
        Publication $publication,
        string $assistantMode,
        ?string $sourceId,
        ?int $attachmentId,
        array $messages,
        array $selectedPdfSourceKeys = []
    ): array {
        if ($selectedPdfSourceKeys === [] && $assistantMode === 'chatpdf') {
            $meta = is_array($session->chatpdf_meta ?? null) ? $session->chatpdf_meta : [];
            $selectedPdfSourceKeys = is_array($meta['selected_keys'] ?? null) ? $meta['selected_keys'] : PublicationChatPdfResolver::defaultSelectedKeys($publication);
        }

        $activeLabel = $this->activeSourceLabel($publication, $selectedPdfSourceKeys);

        return [
            'session_id' => $session->id,
            'source_id' => $sourceId,
            'assistant_mode' => $assistantMode,
            'attachment_id' => $attachmentId,
            'pdf_source_keys' => $selectedPdfSourceKeys,
            'active_source_label' => $activeLabel,
            'pdf_sources' => $publication->pdf_sources,
            'messages' => $messages,
        ];
    }

    /**
     * @param  list<string>  $selectedPdfSourceKeys
     */
    private function activeSourceLabel(Publication $publication, array $selectedPdfSourceKeys): ?string
    {
        if ($selectedPdfSourceKeys === []) {
            return null;
        }

        $labels = PublicationChatPdfResolver::labelsForKeys($publication, $selectedPdfSourceKeys);
        if ($labels === []) {
            return null;
        }

        if (count($labels) === 1) {
            return reset($labels);
        }

        return count($labels).' PDF documents selected';
    }

    /**
     * @return list<string>
     */
    private function normalizePdfSourceKeysInput(mixed $raw): array
    {
        if (! is_array($raw)) {
            return [];
        }

        return array_values(array_filter(array_map(
            static fn ($key) => is_string($key) || is_int($key) ? (string) $key : null,
            $raw
        )));
    }

    /**
     * @return list<string>
     */
    private function pdfSourceKeysFromAttachmentId(Publication $publication, ?int $attachmentId): array
    {
        if ($attachmentId) {
            return [(string) $attachmentId];
        }

        return PublicationChatPdfResolver::defaultSelectedKeys($publication);
    }

    /**
     * @param  list<string>  $selectedPdfSourceKeys
     */
    private function sessionHasReadyChatPdfSources(PdfChatSession $session, array $selectedPdfSourceKeys, ?array $existingMeta): bool
    {
        if (empty($session->source_id)) {
            return false;
        }

        $meta = is_array($existingMeta) ? $existingMeta : (is_array($session->chatpdf_meta ?? null) ? $session->chatpdf_meta : null);
        if (! is_array($meta) || empty($meta['sources']) || ! is_array($meta['sources'])) {
            return count($selectedPdfSourceKeys) <= 1;
        }

        foreach ($selectedPdfSourceKeys as $key) {
            if (empty($meta['sources'][$key])) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  list<string>  $selectedPdfSourceKeys
     * @param  array<string, mixed>|null  $existingMeta
     * @return array{selected_keys: list<string>, sources: array<string, string>, labels: array<string, string>}
     */
    private function ensureChatPdfMetaForSelection(Publication $publication, array $selectedPdfSourceKeys, ?array $existingMeta): array
    {
        $meta = [
            'selected_keys' => $selectedPdfSourceKeys,
            'sources' => is_array($existingMeta['sources'] ?? null) ? $existingMeta['sources'] : [],
            'labels' => is_array($existingMeta['labels'] ?? null) ? $existingMeta['labels'] : [],
        ];

        foreach ($selectedPdfSourceKeys as $key) {
            if (! empty($meta['sources'][$key])) {
                continue;
            }

            $pdf = PublicationChatPdfResolver::resolvePdfForKey($publication, $key);
            if ($pdf === null || (($pdf['url'] ?? null) === null && ($pdf['path'] ?? null) === null)) {
                continue;
            }

            $sourceId = null;
            if (! empty($pdf['url'])) {
                $sourceId = $this->chatPdf->getSourceIdFromUrl($pdf['url']);
            }
            if (! $sourceId && ! empty($pdf['path'])) {
                $sourceId = $this->chatPdf->getSourceIdFromFile($pdf['path']);
            }

            if (! $sourceId) {
                continue;
            }

            $meta['sources'][$key] = $sourceId;
            $meta['labels'][$key] = $pdf['label'];
        }

        foreach ($selectedPdfSourceKeys as $key) {
            if (empty($meta['sources'][$key])) {
                $label = $meta['labels'][$key] ?? $key;
                throw new \RuntimeException('Could not load "'.$label.'" for chat. Please try again or choose a different file.');
            }
        }

        return $meta;
    }

    /**
     * @param  array{selected_keys?: list<string>, sources?: array<string, string>}  $chatpdfMeta
     * @param  list<string>  $selectedPdfSourceKeys
     */
    private function primaryChatPdfSourceId(array $chatpdfMeta, array $selectedPdfSourceKeys): ?string
    {
        $sources = $chatpdfMeta['sources'] ?? [];
        foreach ($selectedPdfSourceKeys as $key) {
            if (! empty($sources[$key])) {
                return (string) $sources[$key];
            }
        }

        return null;
    }

    /**
     * @return array<string, string>
     */
    private function chatPdfSourcesForSession(PdfChatSession $session): array
    {
        $meta = is_array($session->chatpdf_meta ?? null) ? $session->chatpdf_meta : null;
        if (is_array($meta) && ! empty($meta['sources']) && is_array($meta['sources'])) {
            $keys = is_array($meta['selected_keys'] ?? null) ? $meta['selected_keys'] : array_keys($meta['sources']);
            $out = [];
            foreach ($keys as $key) {
                if (! empty($meta['sources'][$key])) {
                    $out[(string) $key] = (string) $meta['sources'][$key];
                }
            }
            if ($out !== []) {
                return $out;
            }
        }

        if (! empty($session->source_id)) {
            return ['default' => (string) $session->source_id];
        }

        return [];
    }

    /**
     * @param  array<string, string>  $sourceIdsByKey
     * @param  array<string, string>  $labelsByKey
     * @param  list<array{role: string, content: string}>  $messages
     */
    private function mergeChatPdfResponses(array $sourceIdsByKey, array $labelsByKey, array $messages): string
    {
        if (count($sourceIdsByKey) <= 1) {
            $sourceId = reset($sourceIdsByKey);
            $response = $this->chatPdf->chat($sourceId, $messages, true);

            return (string) ($response->content ?? ($response->error ?? ''));
        }

        $sections = [];
        foreach ($sourceIdsByKey as $key => $sourceId) {
            $label = $labelsByKey[$key] ?? 'Document';
            $response = $this->chatPdf->chat($sourceId, $messages, true);
            $content = trim((string) ($response->content ?? ''));
            if ($content === '' && isset($response->error)) {
                $content = (string) $response->error;
            }
            if ($content === '') {
                continue;
            }
            $sections[] = '### '.$label."\n\n".$content;
        }

        if ($sections === []) {
            return 'Sorry, no answer could be generated from the selected PDFs.';
        }

        if (count($sections) === 1) {
            return preg_replace('/^### .+\n\n/s', '', $sections[0]) ?? $sections[0];
        }

        return implode("\n\n", $sections);
    }

    /**
     * Send a message and optionally stream the response.
     */
    public function sendMessage(Request $request)
    {
        try {
            $request->validate([
                'publication_id' => 'nullable|integer',
                'forum_id' => 'nullable|integer|exists:forums,id',
                'forum_ids' => 'nullable|array',
                'forum_ids.*' => 'integer|exists:forums,id',
                'session_id' => 'nullable|integer',
                'attachment_id' => 'nullable|integer',
                'message' => 'required|string|max:4000',
                'stream' => 'nullable|boolean',
                'assistant_mode' => 'nullable|string|in:chatpdf,publication,auto,forum,forums_index',
            ]);

            if ($request->filled('publication_id') && $request->filled('forum_id')) {
                return response()->json(['error' => 'Send either publication_id or forum_id, not both.'], 422);
            }

            $sessionId = $request->session_id ? (int) $request->session_id : null;
            $userMessage = $request->message;
            $stream = (bool) $request->get('stream', true);

            $userId = $this->resolveUserId($request);

            if ($request->input('assistant_mode') === 'forums_index') {
                return $this->sendForumsIndexMessage(
                    $sessionId,
                    $userId,
                    $userMessage,
                    $stream,
                    $this->normalizeForumIds($request->input('forum_ids', []))
                );
            }

            if ($request->filled('forum_id')) {
                return $this->sendForumMessage((int) $request->forum_id, $sessionId, $userId, $userMessage, $stream);
            }

            if (! $request->filled('publication_id')) {
                return response()->json(['error' => 'publication_id, forum_id, or forums_index mode is required.'], 422);
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
            $chatPdfSources = $this->chatPdfSourcesForSession($session);
            $labelsByKey = is_array($session->chatpdf_meta['labels'] ?? null) ? $session->chatpdf_meta['labels'] : [];

            if ($stream) {
                return $this->streamResponse($session, $userId, $userMessage, $messages, $chatPdfSources, $labelsByKey);
            }

            $content = $this->mergeChatPdfResponses($chatPdfSources, $labelsByKey, $messages);
            if ($content === '') {
                return response()->json(['error' => 'Could not get a response from ChatPDF.'], 502);
            }

            if ($userId) {
                $this->savePair($session, $userMessage, $content);
            }

            return response()->json([
                'content' => $content,
                'references' => [],
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
        if ($response = $this->forumAssistantUnavailableResponse()) {
            return $response;
        }

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
            return $this->streamPublicationResponse($session, $userId, $userMessage, $openAiMessages, 'forums');
        }

        $content = $this->chatGpt->chatMessagesComplete($openAiMessages, 'forums');
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

    private function streamPublicationResponse(PdfChatSession $session, ?int $userId, string $userMessage, array $openAiMessages, string $feature = 'chat'): StreamedResponse
    {
        return new StreamedResponse(function () use ($session, $userId, $userMessage, $openAiMessages, $feature) {
            $buffer = '';
            $this->chatGpt->chatMessagesStream($openAiMessages, function ($chunk) use (&$buffer) {
                $buffer .= $chunk;
                echo $chunk;
                if (ob_get_level()) {
                    ob_flush();
                }
                flush();
            }, $feature);

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

    /**
     * @param  array<string, string>  $chatPdfSources
     * @param  array<string, string>  $labelsByKey
     */
    private function streamResponse(
        PdfChatSession $session,
        ?int $userId,
        string $userMessage,
        array $messages,
        array $chatPdfSources = [],
        array $labelsByKey = []
    ): StreamedResponse {
        if ($chatPdfSources === [] && ! empty($session->source_id)) {
            $chatPdfSources = ['default' => (string) $session->source_id];
        }

        return new StreamedResponse(function () use ($session, $userId, $userMessage, $messages, $chatPdfSources, $labelsByKey) {
            $buffer = '';

            if (count($chatPdfSources) <= 1) {
                $sourceId = reset($chatPdfSources);
                $this->chatPdf->chatStream($sourceId, $messages, function ($chunk) use (&$buffer) {
                    $buffer .= $chunk;
                    echo $chunk;
                    if (ob_get_level()) {
                        ob_flush();
                    }
                    flush();
                });
            } else {
                $buffer = $this->mergeChatPdfResponses($chatPdfSources, $labelsByKey, $messages);
                echo $buffer;
                if (ob_get_level()) {
                    ob_flush();
                }
                flush();
            }

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
