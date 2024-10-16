<?php

namespace App\Http\Controllers\Api;

use App\Models\Publication;
use App\Models\ContentRequest;
use Illuminate\Http\Request;
use App\Repositories\AuthorsRepository;
use App\Repositories\PublicationsRepository;
use App\Repositories\QuotesRepository;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Str;
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
     *     description="Returns a list of all publications",
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
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent()
     *     )
     * )
     */
    public function index(Request $request)
    {
        Log::info($request->all());

        if (!$request->term) {
            $request['term'] = 'a';
        }

        $request['rows'] = $request->page_size ?? 20;

        $publications = $this->publicationsRepo->get($request, true);

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
     * @OA\Get(
     *     path="/api/publications/add_favourite",
     *     operationId="AddFavouritePublications",
     *     tags={"Publications"},
     *     security={{"bearer_token":{}}},
     *     summary="Add Favourite Publications",
     *     description="Add Favourite publications",
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *         description="Publication Id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent()
     *     )
     * )
     */
    public function add_favourite(Request $request)
    {
        $this->publicationsRepo->add_favourite($request->id);
        $data['message'] = "Added to favourites successfully";

        return response()->json($data, 200);
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
     *                 required={"cover", "data_category_id", "sub_theme", "description", "user_id"},
     *                 @OA\Property(property="cover", type="file"),
     *                 @OA\Property(property="data_category_id", type="integer"),
     *                 @OA\Property(property="sub_theme", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="author", type="integer", nullable=true),
     *                 @OA\Property(property="user_id", type="integer", nullable=true),
     *                 @OA\Property(property="publication_category_id", type="integer"),
     *                 @OA\Property(property="link", type="string", nullable=true),
     *                 @OA\Property(property="communities", type="array", nullable=true, @OA\Items(type="integer")),
     *                 @OA\Property(property="preferences", type="array", nullable=true, @OA\Items(type="integer"), description="Array of Preference Ids", example={1})
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
        Log::info("Request" . json_encode($request->all()));

        $user = auth()->user();

        $val_rules = [
            'cover' => 'required',
            'sub_theme' => 'required',
            'description' => 'required'
        ];

        $request->validate($val_rules);
        $result = null;

        if ($user->is_verified) {
            $result = $this->publicationsRepo->save($request);
        }

        return [
            "status" => ($user->is_verified) ? 200 : 400,
            "data" => $result,
            "msg" => ($user->is_verified) ? "Publication saved successfully" : "User not verified"
        ];
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
     *     description="Allows users to update publications for admin approval",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"cover", "file_type", "sub_theme", "description", "user_id"},
     *                 @OA\Property(property="cover", type="file"),
     *                 @OA\Property(property="file_type", type="integer"),
     *                 @OA\Property(property="sub_theme", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="author", type="integer"),
     *                 @OA\Property(property="user_id", type="integer"),
     *                 @OA\Property(property="link", type="string")
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
    public function update(Request $request, Publication $Publication)
    {
        $val_rules = [
            'cover' => 'required',
            'file_type' => 'required',
            'sub_theme' => 'required',
            'description' => 'required'
        ];

        $request->validate($val_rules);

        $publication = $this->publicationsRepo->save($request);

        return [
            "status" => 200,
            "data" => $publication,
            "msg" => "Publication updated successfully"
        ];
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