<?php

namespace App\Http\Controllers\Api;

use App\Models\Publication;
use Illuminate\Http\Request;
use App\Repositories\AuthorsRepository;
use App\Repositories\PublicationsRepository;
use App\Repositories\QuotesRepository;
use App\Repositories\ThemesRepository;
use App\Http\Controllers\Api\ApiController;
use App\Models\CommunityOfPractice;
use App\Models\Country;
use App\Models\License;
use App\Models\StaticLink;
use App\Repositories\CommonsRepository;
use App\Repositories\CommsOfPracticeRepository;
use App\Repositories\ExpertsRepository;
use App\Repositories\FileTypesRepository;
use App\Repositories\TagsRepository;
use App\Repositories\AreasRepository;

class LookupApiController extends ApiController
{
    private $publicationsRepo, $authorsRepo, $quotesRepo, $themesRepo, $expertsRepo, $commsRepo, $tagsRepo, $fileTypesRepo, $commonsRepos, $areasRepo;

    public function __construct(
        PublicationsRepository $publicationsRepo,
        AuthorsRepository $authorsRepo,
        QuotesRepository $quotesRepo,
        ThemesRepository $themesRepo,
        ExpertsRepository $expertsRepo,
        CommsOfPracticeRepository $commsRepo,
        CommonsRepository $commonsRepos,
        TagsRepository $tagsRepo,
        FileTypesRepository $fileTypesRepo,
        AreasRepository $areasRepo
    ) {
        $this->publicationsRepo = $publicationsRepo;
        $this->authorsRepo = $authorsRepo;
        $this->quotesRepo = $quotesRepo;
        $this->themesRepo = $themesRepo;
        $this->expertsRepo = $expertsRepo;
        $this->commsRepo = $commsRepo;
        $this->tagsRepo = $tagsRepo;
        $this->fileTypesRepo = $fileTypesRepo;
        $this->commonsRepos = $commonsRepos;
        $this->areasRepo = $areasRepo;
    }

    /**
     * @OA\Get(
     *     path="/api/lookup/resource-types",
     *     operationId="ListResourceTypes",
     *     tags={"Lookup"},
     *     summary="List Resource Types",
     *     description="Returns a list of all Resource types",
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent()
     *     )
     * )
     */
    public function resource_types()
    {
        $file_types = $this->commonsRepos->resource_types();
        return [
            "status" => 200,
            "data" => $file_types
        ];
    }

    /**
     * @OA\Get(
     *     path="/api/lookup/themes",
     *     operationId="ListThemes",
     *     tags={"Lookup"},
     *     summary="List Themes",
     *     description="Returns a list of Themes",
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent()
     *     )
     * )
     */
    public function themes(Request $request)
    {
        $themes = $this->themesRepo->get($request);
        return [
            "status" => 200,
            "data" => $themes
        ];
    }

    /**
     * @OA\Get(
     *     path="/api/lookup/regions",
     *     operationId="ListRegions",
     *     tags={"Lookup"},
     *     summary="List RCC regions",
     *     description="Returns all regions with `countries_count` (number of countries linked to each region). Use with community filters (`region_id`) and the countries API.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent()
     *     )
     * )
     */
    public function regions()
    {
        $regions = $this->areasRepo->regionsWithCountryCounts();

        return [
            'status' => 200,
            'data' => $regions,
        ];
    }

    /**
     * @OA\Get(
     *     path="/api/lookup/sub_themes",
     *     operationId="ListSubThemes",
     *     tags={"Lookup"},
     *     summary="List Sub Themes",
     *     description="Returns a list of Sub Themes",
     *     @OA\Parameter(
     *         name="theme_id",
     *         in="query",
     *         required=false,
     *         description="Filter by Thematic area id",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent()
     *     )
     * )
     */
    public function sub_themes(Request $request)
    {
        $sub_themes = $this->themesRepo->get_subthemes($request);
        return [
            "status" => 200,
            "data" => $sub_themes
        ];
    }

    /**
     * @OA\Get(
     *     path="/api/lookup/jobs",
     *     operationId="ListJobs",
     *     tags={"Lookup"},
     *     summary="List Jobs",
     *     description="Returns job titles **unique by name** (case-insensitive, trimmed). When duplicates exist in the database, the lowest `id` is kept per normalized name.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent()
     *     )
     * )
     */
    public function jobs(Request $request)
    {
        $jobs = $this->expertsRepo->get_jobs($request);
        return [
            "status" => 200,
            "data" => $jobs
        ];
    }

    /**
     * @OA\Get(
     *     path="/api/lookup/communities",
     *     operationId="ListCommunities",
     *     tags={"Lookup"},
     *     summary="List Communities",
     *     description="Returns a list of Communities",
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent()
     *     )
     * )
     */
    public function communities(Request $request)
    {
        $comms = $this->commsRepo->get($request, true);
        return [
            "status" => 200,
            "data" => $comms
        ];
    }

    /**
     * @OA\Get(
     *     path="/api/lookup/preferences",
     *     operationId="ListPreferences",
     *     tags={"Lookup"},
     *     summary="List Preferences",
     *     description="Returns a list of Preferences",
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent()
     *     )
     * )
     */
    public function preferences(Request $request)
    {
        $prefs = $this->themesRepo->get_subthemes($request,true);
        return [
            "status" => 200,
            "data" => $prefs
        ];
    }

    /**
     * @OA\Get(
     *     path="/api/lookup/file-categories",
     *     operationId="ListFileCategories",
     *     tags={"Lookup"},
     *     summary="List File Categories",
     *     description="Returns a list of File Categories",
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent()
     *     )
     * )
     */
    public function file_categories(Request $request)
    {
        $categories = $this->fileTypesRepo->get($request, true);
        return [
            "status" => 200,
            "data" => $categories
        ];
    }

    /**
     * @OA\Get(
     *     path="/api/lookup/resource-categories",
     *     operationId="ListResourceCategories",
     *     tags={"Lookup"},
     *     summary="List Resource Categories",
     *     description="Returns a list of Resource Categories",
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent()
     *     )
     * )
     */
    public function resource_categories(Request $request)
    {
        $categories = $this->commonsRepos->publication_categories();
        return [
            "status" => 200,
            "data" => $categories
        ];
    }

    /**
     * @OA\Get(
     *     path="/api/lookup/licenses",
     *     operationId="ListPublicationLicenses",
     *     tags={"Lookup"},
     *     summary="List publication licenses",
     *     description="Returns Creative Commons / other license options for publications (`license_id` on create/update), ordered like the admin licenses list. By default only **active** licenses are returned.",
     *     @OA\Parameter(
     *         name="all",
     *         in="query",
     *         required=false,
     *         description="If `true` or `1`, include inactive licenses (same full list as admin).",
     *         @OA\Schema(type="boolean", example=false)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent()
     *     )
     * )
     */
    public function licenses(Request $request)
    {
        $query = License::query()->orderBy('sort_order')->orderBy('name');

        if (! $request->boolean('all')) {
            $query->where('is_active', true);
        }

        return [
            'status' => 200,
            'data' => $query->get(),
        ];
    }

    /**
     * @OA\Get(
     *     path="/api/lookup/static-links",
     *     operationId="ListStaticLinks",
     *     tags={"Lookup"},
     *     summary="Key links (static links)",
     *     description="Returns the same ordered list managed under **Admin → Static Links** (site header “Key links”): title, URL, display order, and whether to open in a new tab.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent()
     *     )
     * )
     */
    public function staticLinks()
    {
        $links = StaticLink::query()
            ->orderBy('order')
            ->orderBy('title')
            ->get();

        return [
            'status' => 200,
            'data' => $links,
        ];
    }

    /**
     * @OA\Get(
     *     path="/api/lookup/authors",
     *     operationId="ListAuthors",
     *     tags={"Lookup"},
     *     summary="List Authors",
     *     description="Returns a list of Authors",
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent()
     *     )
     * )
     */
    public function authors(Request $request)
    {
        $authors = $this->authorsRepo->get($request);
        return [
            "status" => 200,
            "data" => $authors
        ];
    }

    /**
     * @OA\Get(
     *     path="/api/lookup/settings",
     *     operationId="System Settings",
     *     tags={"Lookup"},
     *     summary="System Settings",
     *     description="Returns configurations",
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent()
     *     )
     * )
     */
    public function settings(Request $request)
    {
        return [
            "status" => 200,
            "data" => settings()
        ];
    }

    public function frontendTheme()
    {
        return [
            'status' => 200,
            'data' => \App\Support\FrontendThemes::resolve(settings()),
            'catalog' => array_merge(
                \App\Support\FrontendThemes::builtinCatalog(),
                \App\Support\FrontendThemes::packsFromSettings(settings())
            ),
        ];
    }
}