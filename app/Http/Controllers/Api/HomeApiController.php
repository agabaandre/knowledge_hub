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
     * Unified home sections: recommended, top searches (by visits), flagship initiatives (category 10).
     *
     * Optional query: limit (default 6, max 24). Bearer token optional; when present, recommended uses the same
     * personalization as the website.
     */
    public function index(Request $request): JsonResponse
    {
        $limit = max(1, min(24, (int) $request->input('limit', 6)));
        $settings = settings();

        $recommended = $this->publicationsRepo->homeRecommendedPublications(
            $request,
            auth()->id(),
            $limit
        );

        $topSearchesRequest = clone $request;
        $topSearchesRequest->merge([
            'rows' => $limit,
            'order_by_visits' => true,
            'skip_random_order' => true,
        ]);
        $topSearches = collect($this->publicationsRepo->get($topSearchesRequest)->items());

        $initiativesRequest = clone $request;
        $initiativesRequest->merge([
            'category' => 10,
            'rows' => $limit,
            'skip_random_order' => true,
        ]);
        $initiatives = collect($this->publicationsRepo->get($initiativesRequest)->items());

        return response()->json([
            'status' => 200,
            'data' => [
                'sections' => [
                    $this->homeSection(
                        'recommended',
                        $settings->section_title_recommended ?? 'Recommended',
                        (bool) ($settings->show_featured ?? false),
                        $recommended
                    ),
                    $this->homeSection(
                        'top_searches',
                        $settings->section_title_top_searches ?? 'Top Searches',
                        (bool) ($settings->show_top_searches ?? false),
                        $topSearches
                    ),
                    $this->homeSection(
                        'flagship_initiatives',
                        $settings->section_title_flagship_initiatives ?? 'Flagship Initiatives',
                        true,
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
