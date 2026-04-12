<?php

namespace App\Http\Controllers\Api;

use App\Models\Publication;
use Illuminate\Http\Request;
use App\Repositories\AuthorsRepository;
use App\Repositories\PublicationsRepository;
use App\Repositories\QuotesRepository;
use App\Http\Controllers\Api\ApiController;
use App\Support\PublicationSubmissionValidation;
use App\Support\RecordsSearchFilterSchema;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Log;

class PublicationsApiController extends ApiController
{
    private $publicationsRepo, $authorsRepo, $quotesRepo;

    public function __construct(
        PublicationsRepository $publicationsRepo,
        AuthorsRepository $authorsRepo,
        QuotesRepository $quotesRepo
    ) {
        $this->publicationsRepo = $publicationsRepo;
        $this->authorsRepo = $authorsRepo;
        $this->quotesRepo = $quotesRepo;
    }

    /**
     * @OA\Get(
     *     path="/api/publications",
     *     operationId="ListPublications",
     *     tags={"Publications"},
     *     summary="List Publications",
     *     description="Records search / listing aligned with web `records/search` filters. By default includes `meta.filter_groups` for mobile refine UI (set include_filters=0 to omit). Empty `term` returns the same broad listing as the website (no dummy keyword).",
     *     @OA\Parameter(
     *         name="term",
     *         in="query",
     *         required=false,
     *         description="Search term for specific records",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="thematic_area_id",
     *         in="query",
     *         required=false,
     *         description="Filter by Thematic area id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="sub_thematic_area_id",
     *         in="query",
     *         required=false,
     *         description="Filter by Sub Thematic area id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="author",
     *         in="query",
     *         required=false,
     *         description="Filter by Author Id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="page_size",
     *         in="query",
     *         required=false,
     *         description="Page Size",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="is_featured",
     *         in="query",
     *         required=false,
     *         description="Filter Featured",
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="order_by_visits",
     *         in="query",
     *         required=false,
     *         description="Order by Visits",
     *         @OA\Schema(type="boolean")
     *     ),
     *  @OA\Parameter(
     *         name="community_id",
     *         in="query",
     *         required=false,
     *         description="Filter by Community Id",
     *         @OA\Schema(type="integer")
     *     ),
     * *  @OA\Parameter(
     *         name="category",
     *         in="query",
     *         required=false,
     *         description="Filter by Category Id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(name="theme", in="query", description="Alias for thematic_area_id (web)", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="rcc", in="query", description="Region id when member states enabled", @OA\Schema(type="string")),
     *     @OA\Parameter(name="country_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="tag", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="data_category_id", in="query", description="Repeat or use array for multi-select", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="file_category_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="file_type_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="include_filters", in="query", description="1 (default) = include meta.filter_groups", @OA\Schema(type="boolean")),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent()
     *     )
     * )
     */
    public function index(Request $request)
    {
        $this->preparePublicationListingRequest($request);

        $get_featured = filter_var($request->input('is_featured', false), FILTER_VALIDATE_BOOLEAN);
        $publications = $this->publicationsRepo->get($request, true, $get_featured);

        $data = $publications->toArray() ?? [];
        $data['status'] = 200;
        $data['page_size'] = (int) ($data['per_page'] ?? 20);
        unset($data['links'], $data['last_page_url'], $data['next_page_url'], $data['path'], $data['first_page_url'], $data['prev_page_url']);

        if ($request->boolean('include_filters', true)) {
            $schema = RecordsSearchFilterSchema::build($request, $this->publicationsRepo);
            $data['meta'] = [
                'active_filters' => RecordsSearchFilterSchema::activeFilterSnapshot($request),
                'filter_groups' => $schema['filter_groups'],
                'supported_query_params' => $schema['supported_query_params'],
                'filter_notes' => $schema['notes'],
            ];
        }

        return response()->json($data, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/publications/sections/recommended",
     *     operationId="PublicationsSectionRecommended",
     *     tags={"Publications"},
     *     summary="Home-style recommended publications (paginated)",
     *     description="Matches the web home **Recommended** strip: strictly featured pool plus preference- and favorite-tag–based publications when a Bearer token is sent (`auth.passport` optional). Paginate with `page` and `per_page` (aliases: `page_size`, `limit`) for infinite scroll. Defaults: page=1, per_page=20 (max 100). `data.meta`: `has_more`, `ranking_total` (size of diversified ranking window, not full DB count). `data.visible` follows `settings.show_featured`.",
     *     @OA\Parameter(name="page", in="query", description="1-based page index", @OA\Schema(type="integer", default=1)),
     *     @OA\Parameter(name="per_page", in="query", description="Page size (default 20)", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="page_size", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="limit", in="query", description="Legacy alias for per_page", @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="status, data.key, data.title, data.visible, data.items[], data.meta"
     *     )
     * )
     *
     * Same pool and ordering as the web home "Recommended" strip (featured + preferences when logged in),
     * shown only when settings.show_featured is on and there is at least one item (matches web).
     */
    public function sectionRecommended(Request $request): JsonResponse
    {
        $settings = settings();
        $page = max(1, (int) $request->input('page', 1));
        $perPage = (int) $request->input('per_page', $request->input('page_size', $request->input('limit', 20)));
        $perPage = max(1, min(100, $perPage));

        $slice = $this->publicationsRepo->homeRecommendedPublicationsPage(
            $request,
            $this->apiOptionalUserId($request),
            $perPage,
            $page
        );

        $items = $slice['items'];
        $sectionOn = (bool) ($settings->show_featured ?? false);
        $visible = $sectionOn && ($page > 1 || $items->isNotEmpty());

        return response()->json([
            'status' => 200,
            'data' => [
                'key' => 'recommended',
                'title' => $settings->section_title_recommended ?? 'Recommended',
                'visible' => $visible,
                'items' => $items->values()->map(fn ($p) => $p->toArray())->all(),
                'meta' => [
                    'page' => $page,
                    'per_page' => $perPage,
                    'has_more' => $slice['has_more'],
                    'ranking_total' => $slice['ranking_total'],
                ],
            ],
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/publications/sections/top-searches",
     *     operationId="PublicationsSectionTopSearches",
     *     tags={"Publications"},
     *     summary="Top publications by visits (paginated)",
     *     description="Same ranking as the web home **Top Searches** strip: `order_by_visits` with stable order (`skip_random_order`). Paginate with `page` and `per_page` (aliases: `page_size`, `limit`). Defaults: page=1, per_page=20 (max 100). `data.meta`: `total`, `last_page`, `has_more`. `data.visible` follows `settings.show_top_searches`.",
     *     @OA\Parameter(name="page", in="query", @OA\Schema(type="integer", default=1)),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="page_size", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="limit", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="status, data.items[], data.meta (page, per_page, total, last_page, has_more)"
     *     )
     * )
     *
     * Same ranking as web home $recent: order_by_visits + skip_random_order. Paginate with page / per_page for infinite scroll.
     */
    public function sectionTopSearches(Request $request): JsonResponse
    {
        $settings = settings();
        $page = max(1, (int) $request->input('page', 1));
        $perPage = (int) $request->input('per_page', $request->input('page_size', $request->input('limit', 20)));
        $perPage = max(1, min(100, $perPage));

        $topReq = clone $request;
        $topReq->merge([
            'rows' => $perPage,
            'page' => $page,
            'order_by_visits' => true,
            'skip_random_order' => true,
        ]);
        $paginator = $this->publicationsRepo->get($topReq, false, false);
        $items = collect($paginator->items());
        $sectionOn = (bool) ($settings->show_top_searches ?? false);
        $visible = $sectionOn && ($page > 1 || $items->isNotEmpty());

        return response()->json([
            'status' => 200,
            'data' => [
                'key' => 'top_searches',
                'title' => $settings->section_title_top_searches ?? 'Top Searches',
                'visible' => $visible,
                'items' => $items->values()->map(fn ($p) => $p->toArray())->all(),
                'meta' => $this->paginationMetaFromPaginator($paginator),
            ],
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/publications/sections/flagship-initiatives",
     *     operationId="PublicationsSectionFlagshipInitiatives",
     *     tags={"Publications"},
     *     summary="Flagship initiatives (category 10, paginated)",
     *     description="Publications in **category / publication category id 10**, same pool as the web home initiatives strip. For infinite scroll this endpoint uses **stable** ordering (visits, then id), not the web carousel’s single-page random shuffle. Paginate with `page` and `per_page` (aliases: `page_size`, `limit`). Defaults: page=1, per_page=20 (max 100). `data.meta`: `total`, `last_page`, `has_more`.",
     *     @OA\Parameter(name="page", in="query", @OA\Schema(type="integer", default=1)),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="page_size", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="limit", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="status, data.items[], data.meta (page, per_page, total, last_page, has_more)"
     *     )
     * )
     *
     * Flagship initiatives (category 10). Uses stable ordering (visits, id) so pages do not reshuffle — required for infinite scroll
     * (web home carousel uses random order on a single page only).
     */
    public function sectionFlagshipInitiatives(Request $request): JsonResponse
    {
        $settings = settings();
        $page = max(1, (int) $request->input('page', 1));
        $perPage = (int) $request->input('per_page', $request->input('page_size', $request->input('limit', 20)));
        $perPage = max(1, min(100, $perPage));

        $req = clone $request;
        $req->merge([
            'category' => 10,
            'rows' => $perPage,
            'page' => $page,
            'order_by_visits' => true,
            'skip_random_order' => true,
        ]);
        $paginator = $this->publicationsRepo->get($req, false, false);
        $items = collect($paginator->items());
        $visible = $page > 1 || $items->isNotEmpty();

        return response()->json([
            'status' => 200,
            'data' => [
                'key' => 'flagship_initiatives',
                'title' => $settings->section_title_flagship_initiatives ?? 'Flagship Initiatives',
                'visible' => $visible,
                'items' => $items->values()->map(fn ($p) => $p->toArray())->all(),
                'meta' => $this->paginationMetaFromPaginator($paginator),
            ],
        ]);
    }

    /**
     * @param  \Illuminate\Contracts\Pagination\LengthAwarePaginator  $paginator
     */
    private function paginationMetaFromPaginator($paginator): array
    {
        return [
            'page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
            'has_more' => $paginator->hasMorePages(),
        ];
    }

    private function preparePublicationListingRequest(Request $request): void
    {
        $mergedThematic = $request->input('theme');
        if ($mergedThematic === null) {
            $mergedThematic = $request->input('thematic_area_id');
        }
        if ($mergedThematic !== null && $mergedThematic !== '') {
            $request->merge(['thematic_area_id' => $mergedThematic]);
        }

        $request->merge([
            'rows' => $request->input('page_size', $request->input('rows', 20)),
        ]);

        if ($request->filled('sub_thematic_area_id')) {
            $request->merge(['subtheme' => $request->input('sub_thematic_area_id')]);
        }
    }

    private function apiOptionalUserId(Request $request): ?int
    {
        $u = $request->user();

        return $u ? (int) $u->id : null;
    }

    /**
     * @OA\Get(
     *     path="/api/publications/published",
     *     operationId="ListOwnPublications",
     *     tags={"Publications"},
     *     security={{"bearer_token":{}}},
     *     summary="List Own Publications",
     *     description="Returns a list of own publications",
     *     @OA\Parameter(
     *         name="term",
     *         in="query",
     *         required=false,
     *         description="Search term for specific records",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="thematic_area_id",
     *         in="query",
     *         required=false,
     *         description="Filter by Thematic area id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="sub_thematic_area_id",
     *         in="query",
     *         required=false,
     *         description="Filter by Sub Thematic area id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="page_size",
     *         in="query",
     *         required=false,
     *         description="Page Size",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="is_featured",
     *         in="query",
     *         required=false,
     *         description="Filter Featured",
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="order_by_visits",
     *         in="query",
     *         required=false,
     *         description="Order by visits",
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent()
     *     )
     * )
     */
    public function my_publications(Request $request)
    {
        Log::info($request->all());

        if (!$request->term) {
            $request['term'] = 'a';
        }

        $request['rows'] = $request->page_size ?? 20;
        $request['user_id'] = auth()->user()->id;

        $publications = $this->publicationsRepo->my_publications($request);

        $data = $publications->toArray() ?? [];
        $data['status'] = 200;
        $data['page_size'] = intval($data['per_page']);
        unset($data['links'], $data['last_page_url'], $data['next_page_url'], $data['path'], $data['first_page_url'], $data['prev_page_url']);

        // Check for encoding errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Error encoding JSON: ' . json_last_error_msg());
        }

        return response()->json($data, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/publications/favourites",
     *     operationId="ListFavouritePublications",
     *     tags={"Publications"},
     *     security={{"bearer_token":{}}},
     *     summary="Favourite Publications",
     *     description="Favourite publications",
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent()
     *     )
     * )
     */
    public function favourites(Request $request)
    {
        $publications = $this->publicationsRepo->favourites($request);

        $data = $publications->toArray() ?? [];
        $data['status'] = 200;
        $data['page_size'] = intval($data['per_page']);
        unset($data['links'], $data['last_page_url'], $data['next_page_url'], $data['path'], $data['first_page_url'], $data['prev_page_url']);

        // Check for encoding errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Error encoding JSON: ' . json_last_error_msg());
        }

        return response()->json($data, 200);
    }

    /**
     * Add publication to favourites (same as web heart / “likes” count on listings).
     *
     * Canonical: `POST /api/publications/favourite` with JSON `publication_id` (or `id`).
     * Same handler: `GET|POST /api/publications/add_favourite?id=`, `POST /api/publications/like/{publicationId}`.
     *
     * @OA\Post(
     *     path="/api/publications/favourite",
     *     operationId="AddPublicationFavourite",
     *     tags={"Publications"},
     *     security={{"bearer_token":{}}},
     *     summary="Add favourite (like)",
     *     description="Favourite a publication. Web equivalent: `/publications/add_favourite`. Aliases: `GET|POST /api/publications/add_favourite?id=`, `POST /api/publications/like/{publicationId}`.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"publication_id"},
     *             @OA\Property(property="publication_id", type="integer", example=42),
     *             @OA\Property(property="id", type="integer", description="Alias for publication_id")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="favourited", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(response=400, description="Missing or invalid publication id"),
     *     @OA\Response(response=404, description="Publication not found")
     * )
     */
    public function add_favourite(Request $request, $publicationId = null)
    {
        $id = $this->resolvePublicationFavouriteId($request, $publicationId);
        if ($id === null) {
            return response()->json(['success' => false, 'error' => 'Publication ID required'], 400);
        }

        if (! Publication::query()->whereKey($id)->exists()) {
            return response()->json(['success' => false, 'error' => 'Publication not found'], 404);
        }

        $this->publicationsRepo->add_favourite($id);

        return response()->json(['success' => true, 'favourited' => true], 200);
    }

    /**
     * Remove publication from favourites (unlike).
     *
     * Canonical: `DELETE /api/publications/favourite/{publicationId}`.
     * Same handler: `GET|POST /api/publications/remove_favourite?id=`, `POST /api/publications/unlike/{publicationId}`.
     *
     * @OA\Delete(
     *     path="/api/publications/favourite/{publicationId}",
     *     operationId="RemovePublicationFavourite",
     *     tags={"Publications"},
     *     security={{"bearer_token":{}}},
     *     summary="Remove favourite (unlike)",
     *     description="Web equivalent: `/publications/remove_favourite`. Aliases: `GET|POST /api/publications/remove_favourite?id=`, `POST /api/publications/unlike/{publicationId}`.",
     *     @OA\Parameter(name="publicationId", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="favourited", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(response=400, description="Missing or invalid publication id"),
     *     @OA\Response(response=404, description="Publication not found")
     * )
     */
    public function remove_favourite(Request $request, $publicationId = null)
    {
        $id = $this->resolvePublicationFavouriteId($request, $publicationId);
        if ($id === null) {
            return response()->json(['success' => false, 'error' => 'Publication ID required'], 400);
        }

        if (! Publication::query()->whereKey($id)->exists()) {
            return response()->json(['success' => false, 'error' => 'Publication not found'], 404);
        }

        $this->publicationsRepo->remove_favourite($id);

        return response()->json(['success' => true, 'favourited' => false], 200);
    }

    /**
     * @param  int|string|null  $publicationId  From route parameter when using /like/{id}, etc.
     */
    private function resolvePublicationFavouriteId(Request $request, $publicationId = null): ?int
    {
        if ($publicationId !== null && $publicationId !== '') {
            $n = (int) $publicationId;

            return $n > 0 ? $n : null;
        }

        $raw = $request->input('id', $request->input('publication_id'));
        if ($raw === null || $raw === '') {
            return null;
        }
        $n = (int) $raw;

        return $n > 0 ? $n : null;
    }

    /**
     * @OA\Post(
     *     path="/api/publications",
     *     tags={"Publications"},
     *     summary="Create Publication",
     *     operationId="CreatePublication",
     *     security={{"bearer_token":{}}},
     *     description="Allows users to submit publications for admin approval",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 description="Same fields as the web publish wizard (account/publish). Required fields follow admin settings (publication_required_fields, publication_min_words).",
     *                 required={"title","description","associated_authors","author_affiliation","tags","theme","sub_theme","data_category_id"},
     *                 @OA\Property(property="upload_type", type="string", enum={"upload","link"}, description="link requires link URL"),
     *                 @OA\Property(property="link", type="string", format="uri", description="External resource URL when upload_type=link"),
     *                 @OA\Property(property="is_embedded", type="boolean", description="Embed linked content on the resource page"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="year_published", type="integer"),
     *                 @OA\Property(property="data_category_id", type="integer", description="Resource category (wizard Category)"),
     *                 @OA\Property(property="category_id", type="integer", description="File/resource sub-type (wizard Sub Category)"),
     *                 @OA\Property(property="publication_sub_category_id", type="integer", nullable=true),
     *                 @OA\Property(property="theme", type="integer", description="Thematic area id"),
     *                 @OA\Property(property="sub_theme", type="integer", description="Sub-thematic area id"),
     *                 @OA\Property(property="tags", type="array", @OA\Items(type="integer"), description="Health topic tag ids"),
     *                 @OA\Property(property="description", type="string", description="HTML or plain text; min length from settings"),
     *                 @OA\Property(property="associated_authors", type="string"),
     *                 @OA\Property(property="author_affiliation", type="string"),
     *                 @OA\Property(property="doi", type="string", nullable=true),
     *                 @OA\Property(property="issn", type="string", nullable=true),
     *                 @OA\Property(property="isbn", type="string", nullable=true),
     *                 @OA\Property(property="publisher", type="string", nullable=true),
     *                 @OA\Property(property="license_id", type="integer", nullable=true),
     *                 @OA\Property(property="funder", type="string", nullable=true),
     *                 @OA\Property(property="copyright_info", type="string", nullable=true),
     *                 @OA\Property(property="journal_name", type="string", nullable=true),
     *                 @OA\Property(property="journal_volume", type="string", nullable=true),
     *                 @OA\Property(property="journal_issue", type="string", nullable=true),
     *                 @OA\Property(property="journal_pages", type="string", nullable=true),
     *                 @OA\Property(property="rccs", type="array", @OA\Items(type="integer"), description="Regional coverage ids"),
     *                 @OA\Property(property="countries", type="array", @OA\Items(type="integer"), description="Member state country ids"),
     *                 @OA\Property(property="communities", type="array", @OA\Items(type="integer"), description="Target communities of practice"),
     *                 @OA\Property(property="tag_all_my_communities", type="boolean", nullable=true),
     *                 @OA\Property(property="cover", type="string", format="binary", description="Cover image (optional; default cover if omitted)"),
     *                 @OA\Property(property="cover_url", type="string", nullable=true, description="External cover URL when editing"),
     *                 @OA\Property(property="files", type="array", @OA\Items(type="string", format="binary"), description="Attachments (multipart files[])"),
     *                 @OA\Property(property="author", type="integer", nullable=true, description="Admin only: source author id"),
     *                 @OA\Property(property="original_id", type="integer", nullable=true, description="Create new version from this publication id"),
     *                 @OA\Property(property="show_disclaimer", type="boolean", nullable=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Success",
     *         @OA\MediaType(mediaType="application/json")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request, when some required data is missing"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found when you send the request to an invalid endpoint"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     )
     * )
     */
    public function store(Request $request)
    {
        Log::info('API publication store', ['keys' => array_keys($request->all())]);

        $user = auth()->user();
        if (! $user->is_verified) {
            return response()->json([
                'status' => 400,
                'data' => null,
                'msg' => 'User not verified',
            ], 400);
        }

        if (! is_admin() && ! $user->author_id) {
            return response()->json([
                'status' => 422,
                'message' => 'Your account is not associated with an author. Please contact the administrator to link your account to an author.',
            ], 422);
        }

        $this->normalizePublicationArrayInputs($request);
        $request->merge(['user_id' => null]);

        if ($request->original_id) {
            $request->merge(['file_type' => 1]);
        }

        if (! $request->is_active) {
            $request->merge(['is_active' => 'In-Active']);
        }

        try {
            $request->validate(
                PublicationSubmissionValidation::rules($request),
                PublicationSubmissionValidation::messages($request)
            );
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 422,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        if (! $request->has('show_disclaimer')) {
            $request->merge(['show_disclaimer' => 1]);
        }

        $result = $this->publicationsRepo->save($request);

        return response()->json([
            'status' => $result ? 200 : 400,
            'data' => $result,
            'msg' => $result ? 'Publication saved successfully' : 'Request failed try again',
        ], $result ? 200 : 400);
    }

    /**
     * @OA\Post(
     *     path="/api/publications/comment",
     *     tags={"Publications"},
     *     summary="Create Publication Comment",
     *     operationId="CreatePublicationComment",
     *     security={{"bearer_token":{}}},
     *     description="Allows users to submit publication comment for admin approval",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"publication_id", "comment"},
     *                 @OA\Property(property="publication_id", type="integer"),
     *                 @OA\Property(property="comment", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Success",
     *         @OA\MediaType(mediaType="application/json")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request, when some required data is missing"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found when you send the request to an invalid endpoint"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     )
     * )
     */
    public function comment(Request $request)
    {
        $val_rules = [
            'publication_id' => 'required',
            'comment' => 'required'
        ];

        $request->validate($val_rules);

        $publication = $this->publicationsRepo->save_comment($request);

        return [
            "status" => 200,
            "data" => $publication,
            "msg" => "Publication saved successfully"
        ];
    }

    /**
     * @OA\Get(
     *     path="/api/publications/{id}",
     *     operationId="RetrieveSinglePublication",
     *     tags={"Publications"},
     *     summary="Retrieve Single Publication",
     *     description="Retrieve Single Publication",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Record Id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent()
     *     )
     * )
     */
    public function show($publication_id)
    {
        $publication = $this->publicationsRepo->find($publication_id);

        return [
            "status" => 200,
            "data" => $publication
        ];
    }

    /**
     * @OA\Post(
     *     path="/api/publications/{id}",
     *     tags={"Publications"},
     *     summary="Update Publication",
     *     operationId="UpdatePublication",
     *     security={{"bearer_token":{}}},
     *     description="Update a draft or pending publication (same body as create; id is in the URL). Cover file optional when unchanged.",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="upload_type", type="string", enum={"upload","link"}),
     *                 @OA\Property(property="link", type="string", format="uri"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="associated_authors", type="string"),
     *                 @OA\Property(property="author_affiliation", type="string"),
     *                 @OA\Property(property="tags", type="array", @OA\Items(type="integer")),
     *                 @OA\Property(property="theme", type="integer"),
     *                 @OA\Property(property="sub_theme", type="integer"),
     *                 @OA\Property(property="data_category_id", type="integer"),
     *                 @OA\Property(property="category_id", type="integer"),
     *                 @OA\Property(property="cover", type="string", format="binary"),
     *                 @OA\Property(property="files", type="array", @OA\Items(type="string", format="binary"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Success",
     *         @OA\MediaType(mediaType="application/json")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request, when some required data is missing"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found when you send the request to an invalid endpoint"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     )
     * )
     */
    public function update(Request $request, int $id)
    {
        $user = auth()->user();
        if (! $user->is_verified) {
            return response()->json([
                'status' => 400,
                'data' => null,
                'msg' => 'User not verified',
            ], 400);
        }

        $publication = Publication::query()->find($id);
        if (! $publication) {
            return response()->json(['status' => 404, 'message' => 'Publication not found'], 404);
        }

        if ((int) $publication->user_id !== (int) $user->id && ! is_admin()) {
            return response()->json(['status' => 403, 'message' => 'Forbidden'], 403);
        }

        if ($publication->is_approved && ! is_admin()) {
            return response()->json([
                'status' => 403,
                'message' => 'Approved publications cannot be edited. Contact an administrator if you need changes.',
            ], 403);
        }

        if (! is_admin() && ! $user->author_id) {
            return response()->json([
                'status' => 422,
                'message' => 'Your account is not associated with an author. Please contact the administrator to link your account to an author.',
            ], 422);
        }

        $this->normalizePublicationArrayInputs($request);
        $request->merge(['id' => $id, 'user_id' => null]);

        if (! $request->is_active) {
            $request->merge(['is_active' => 'In-Active']);
        }

        try {
            $request->validate(
                PublicationSubmissionValidation::rules($request),
                PublicationSubmissionValidation::messages($request)
            );
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 422,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        if (! $request->has('show_disclaimer')) {
            $request->merge(['show_disclaimer' => 1]);
        }

        $saved = $this->publicationsRepo->save($request);

        return response()->json([
            'status' => $saved ? 200 : 400,
            'data' => $saved,
            'msg' => $saved ? 'Publication updated successfully' : 'Request failed try again',
        ], $saved ? 200 : 400);
    }

    /**
     * Decode JSON-encoded array fields from mobile clients (multipart).
     */
    private function normalizePublicationArrayInputs(Request $request): void
    {
        foreach (['tags', 'countries', 'rccs', 'communities'] as $key) {
            if (! $request->has($key)) {
                continue;
            }
            $v = $request->input($key);
            if (is_string($v)) {
                $t = trim($v);
                if ($t !== '' && isset($t[0]) && $t[0] === '[') {
                    $decoded = json_decode($t, true);
                    if (is_array($decoded)) {
                        $request->merge([$key => $decoded]);
                    }
                }
            }
        }
    }

    public function destroy(Publication $publication)
    {
        $publication->delete();
        return [
            "status" => 200,
            "data" => $publication,
            "msg" => "Publication deleted successfully"
        ];
    }

    /**
     * @OA\Post(
     *     path="/api/publications/content-request",
     *     tags={"Publications"},
     *     summary="Create Content Request",
     *     operationId="CreateContentRequest",
     *     security={{"bearer_token":{}}},
     *     description="Allows users to submit a content request",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"title", "description", "country_id"},
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="country_id", type="integer"),
     *                 @OA\Property(property="email", type="string", nullable=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Content request created successfully",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request, when some required data is missing"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     )
     * )
     */
    public function content_request(Request $request)
    {
        $val_rules = [
            'title' => 'required|string',
            'description' => 'required|string',
            'country_id' => 'nullable|integer',
            'email' => 'nullable|email'
        ];

        $request->validate($val_rules);

        $contentRequest = $this->publicationsRepo->save_content_request($request);

        return response()->json([
            "status" => 201,
            "data" => $contentRequest,
            "msg" => "Content request created successfully"
        ], 201);
    }
}