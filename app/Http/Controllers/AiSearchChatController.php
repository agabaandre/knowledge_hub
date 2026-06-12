<?php

namespace App\Http\Controllers;

use App\Repositories\CommsOfPracticeRepository;
use App\Repositories\ForumsRepository;
use App\Services\AiSearchChatService;
use App\Services\FederatedContentService;
use Illuminate\Http\Request;

class AiSearchChatController extends Controller
{
    public function __construct(
        private AiSearchChatService $chatService,
        private ForumsRepository $forumsRepo,
        private CommsOfPracticeRepository $commsRepo,
        private FederatedContentService $federationContent
    ) {
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'conversation_id' => 'nullable|string|max:64',
            'term' => 'nullable|string|max:500',
        ]);

        $chatRequest = $this->buildSearchRequest($request);

        $searchForums = (settings()->search_show_forums ?? true)
            ? $this->forumsRepo->searchForRecords($chatRequest, 8, false)
            : collect();
        $searchCommunities = (settings()->search_show_communities ?? true)
            ? $this->commsRepo->searchForRecords($chatRequest, 6)
            : collect();

        $federatedPublications = collect();
        if ($this->federationContent->federationConsumerEnabled() && $chatRequest->filled('term')) {
            $federated = $this->federationContent->search($chatRequest->input('term'), 10);
            $federatedPublications = $federated['publications'] ?? collect();
        }

        $result = $this->chatService->respond(
            $chatRequest,
            (string) $request->input('message'),
            $request->input('conversation_id'),
            $searchForums,
            $searchCommunities,
            $federatedPublications
        );

        if (! ($result['ok'] ?? false)) {
            return response()->json([
                'ok' => false,
                'error' => $result['error'] ?? 'Unable to process your question.',
            ], 422);
        }

        return response()->json($result);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'conversation_id' => 'nullable|string|max:64',
        ]);

        $this->chatService->resetConversation($request->input('conversation_id'));

        return response()->json(['ok' => true]);
    }

    private function buildSearchRequest(Request $request): Request
    {
        $params = $request->only([
            'term', 'thematic_area_id', 'sub_thematic_area_id', 'country_id',
            'data_category_id', 'author_id', 'file_type_id', 'rcc', 'tag', 'theme',
        ]);
        $params['thematic_area_id'] = $params['theme'] ?? $params['thematic_area_id'] ?? null;

        return new Request($params);
    }
}
