<?php

namespace App\Http\Controllers\Api;

use App\Repositories\PublicationsRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Homepage-style feeds for mobile/API: same section shape as the web home strips.
 */
class HomeApiController extends ApiController
{
    /** @var PublicationsRepository */
    private $publicationsRepo;

    public function __construct(PublicationsRepository $publicationsRepo)
    {
        $this->publicationsRepo = $publicationsRepo;
    }

    /**
     * @OA\Get(
     *     path="/api/home",
     *     operationId="HomeSectionsBundle",
     *     tags={"Home"},
     *     summary="Combined home feed (all sections in one response)",
     *     description="Returns three sections—`recommended`, `top_searches`, `flagship_initiatives`—each with `key`, `title`, `visible`, and `items` (publication arrays). Query `limit` (default **20**, max **48**) sets how many items are loaded for **recommended** and **top searches**. **Recommended** items are ordered newest first. **Flagship** always returns the first **20** rows (category 10), matching the web home paginator default. Send `Authorization: Bearer` (via `auth.passport`) so **recommended** uses the same personalization as the website. Section `visible` flags follow site settings (`show_featured`, `show_top_searches`) and non-empty content. For paginated / infinite lists per section, use the dedicated `/api/publications/sections/*` endpoints instead.",
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Item count for recommended and top_searches sections (default 20, max 48)",
     *         @OA\Schema(type="integer", default=20)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="data.sections[]"
     *     )
     * )
     *
     * Unified home sections: recommended, top searches (by visits), flagship initiatives (category 10).
     *
     * Optional query: limit (default 20, max 48) for recommended and top searches. Flagship initiatives always loads
     * the first 20 items like the web home (category 10). Bearer token optional; when present, recommended uses the same
     * personalization as the website. Section visibility matches the web: show_featured/show_top_searches settings and
     * non-empty items for flagship.
     */
    public function index(Request $request): JsonResponse
    {
        $limit = max(1, min(48, (int) $request->input('limit', 20)));
        $settings = settings();

        $userId = $request->user() ? (int) $request->user()->id : null;

        $recommended = $this->publicationsRepo->homeRecommendedPublications(
            $request,
            $userId,
            $limit
        );

        $topSearchesRequest = clone $request;
        $topSearchesRequest->merge([
            'rows' => $limit,
            'order_by_visits' => true,
            'skip_random_order' => true,
        ]);
        $topSearches = collect($this->publicationsRepo->get($topSearchesRequest, false, false)->items());

        $initiativesRequest = clone $request;
        $initiativesRequest->merge([
            'category' => 10,
            'rows' => 20,
        ]);
        $initiatives = collect($this->publicationsRepo->get($initiativesRequest, false, false)->items());

        return response()->json([
            'status' => 200,
            'data' => [
                'sections' => [
                    $this->homeSection(
                        'recommended',
                        $settings->section_title_recommended ?? 'Recommended',
                        (bool) ($settings->show_featured ?? false) && $recommended->isNotEmpty(),
                        $recommended
                    ),
                    $this->homeSection(
                        'top_searches',
                        $settings->section_title_top_searches ?? 'Top Searches',
                        (bool) ($settings->show_top_searches ?? false) && $topSearches->isNotEmpty(),
                        $topSearches
                    ),
                    $this->homeSection(
                        'flagship_initiatives',
                        $settings->section_title_flagship_initiatives ?? 'Flagship Initiatives',
                        $initiatives->isNotEmpty(),
                        $initiatives
                    ),
                ],
            ],
        ]);
    }

    private function homeSection(string $key, string $title, bool $visible, Collection $publications): array
    {
        return [
            'key' => $key,
            'title' => $title,
            'visible' => $visible,
            'items' => $publications->values()->map(function ($p) {
                return $p->toArray();
            })->all(),
        ];
    }
}
