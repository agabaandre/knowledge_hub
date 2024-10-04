<?php

namespace App\Http\Controllers\Api;

use App\Models\Publication;
use Illuminate\Http\Request;
use App\Repositories\AuthorsRepository;
use App\Repositories\PublicationsRepository;
use App\Repositories\QuotesRepository;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Str;
use Log;

class PublicationsApiController extends ApiController
{
    private $publicationsRepo,$authorsRepo,$quotesRepo;

    public function __construct(PublicationsRepository $publicationsRepo, 
    AuthorsRepository $authorsRepo, QuotesRepository $quotesRepo){

        $this->publicationsRepo = $publicationsRepo;
        $this->authorsRepo      = $authorsRepo;
        $this->quotesRepo       = $quotesRepo;
    }
   
    /**
        * @OA\Get(
        * path="/api/publications",
        * operationId="List Publications",
        * tags={"List Publications"},
        * summary="List Publications",
        * description="Returns a list of all publications",
        *  @OA\Parameter(
        *      name="term",
        *      in="query",
        *      required=false,
        *      description="Search term for search fro specific records",
        *      @OA\Schema(
        *           type="string"
        *      )
        *   ),
        *  @OA\Parameter(
        *      name="thematic_area_id",
        *      in="query",
        *      required=false,
        *      description="Filter by Themeatic area id",
        *      @OA\Schema(
        *           type="integer"
        *      )
        *   ),
        *  @OA\Parameter(
        *      name="sub_thematic_area_id",
        *      in="query",
        *      required=false,
        *      description="Filter by Sub Themeatic area id",
        *      @OA\Schema(
        *           type="integer"
        *      )
        *   ),
        *  @OA\Parameter(
        *      name="author",
        *      in="query",
        *      required=false,
        *      description="Filter by Author Id",
        *      @OA\Schema(
        *           type="integer"
        *      )
        *   ), 
        *  @OA\Parameter(
        *      name="page_size",
        *      in="query",
        *      required=false,
        *      description="Page Size",
        *      @OA\Schema(
        *           type="integer"
        *      )
        *   ),
        *  @OA\Parameter(
        *      name="page",
        *      in="query",
        *      required=false,
        *      description="Page",
        *      @OA\Schema(
        *           type="integer"
        *      )
        *   ),
        *  @OA\Parameter(
        *      name="is_featured",
        *      in="query",
        *      required=false,
        *      description="Filter Featured",
        *      @OA\Schema(
        *           type="boolean"
        *      )
        *   ),  
        *  @OA\Parameter(
        *      name="order_by_visits",
        *      in="query",
        *      required=false,
        *      description="Order by Visits",
        *      @OA\Schema(
        *           type="boolean"
        *      )
        *   ),
        *      @OA\Response(
        *          response=200,
        *          description="Successful",
        *          @OA\JsonContent()
        *       )
        * )
        */
    public function index(Request $request)
    {
        Log::info($request->all());

        if(!$request->term)
        $request['term']='a';

        $request['rows']= $request->page_size ?? 20;

        $publications = $this->publicationsRepo->get($request,true);
       
        $data = $publications->toArray() ?? [];
        $data['status'] = 200;
        $data['page_size'] = intval($data['per_page']);
        unset($data['links']);
        unset($data['last_page_url']);
        unset($data['next_page_url']);
        unset($data['path']);
        unset($data['first_page_url']);
        unset($data['prev_page_url']);

        // Check for encoding errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            // Handle the error, maybe log it or throw an exception
            throw new \Exception('Error encoding JSON: ' . json_last_error_msg());
        }

       return  response()->json($data, 200); 
    }

     /**
        * @OA\Get(
        * path="/api/publications/published",
        * operationId="List Own Publications",
        * tags={"List Own Publications"},
        *   security={{"bearer_token":{}}},
        * summary="List Own Publications",
        * description="Returns a list of own publications",
        *  @OA\Parameter(
        *      name="term",
        *      in="query",
        *      required=false,
        *      description="Search term for search fro specific records",
        *      @OA\Schema(
        *           type="string"
        *      )
        *   ),
        *  @OA\Parameter(
        *      name="thematic_area_id",
        *      in="query",
        *      required=false,
        *      description="Filter by Themeatic area id",
        *      @OA\Schema(
        *           type="integer"
        *      )
        *   ),
        *  @OA\Parameter(
        *      name="sub_thematic_area_id",
        *      in="query",
        *      required=false,
        *      description="Filter by Sub Themeatic area id",
        *      @OA\Schema(
        *           type="integer"
        *      )
        *   ),
        *  @OA\Parameter(
        *      name="page_size",
        *      in="query",
        *      required=false,
        *      description="Page Size",
        *      @OA\Schema(
        *           type="integer"
        *      )
        *   ),
        *  @OA\Parameter(
        *      name="page",
        *      in="query",
        *      required=false,
        *      description="Page",
        *      @OA\Schema(
        *           type="integer"
        *      )
        *   ),
        *  @OA\Parameter(
        *      name="is_featured",
        *      in="query",
        *      required=false,
        *      description="Filter Featured",
        *      @OA\Schema(
        *           type="boolean"
        *      )
        *   ), 
        *  @OA\Parameter(
        *      name="order_by_visits",
        *      in="query",
        *      required=false,
        *      description="Order by visits",
        *      @OA\Schema(
        *           type="boolean"
        *      )
        *   ),
        *     @OA\Response(
        *          response=200,
        *          description="Successful",
        *          @OA\JsonContent()
        *       )
        * )
        */
        public function my_publications(Request $request)
        {
            Log::info($request->all());
    
            if(!$request->term)
            $request['term']='a';
    
            $request['rows']= $request->page_size ?? 20;
            $request['author_id'] = auth()->user()->author_id;
            $request['user_id'] = auth()->user()->id;
    
            $publications = $this->publicationsRepo->get($request,true);
           
            $data = $publications->toArray() ?? [];
            $data['status'] = 200;
            $data['page_size'] = intval($data['per_page']);
            unset($data['links']);
            unset($data['last_page_url']);
            unset($data['next_page_url']);
            unset($data['path']);
            unset($data['first_page_url']);
            unset($data['prev_page_url']);
    
            // Check for encoding errors
            if (json_last_error() !== JSON_ERROR_NONE) {
                // Handle the error, maybe log it or throw an exception
                throw new \Exception('Error encoding JSON: ' . json_last_error_msg());
            }
    
           return  response()->json($data, 200); 
    }

     /**
        * @OA\Get(
        * path="/api/publications/favourites",
        * operationId="List Favourite Publications",
        * tags={"Favourite Publications"},
        *   security={{"bearer_token":{}}},
        * summary="Favourite Publications",
        * description="Favourite publications",
        *     @OA\Response(
        *          response=200,
        *          description="Successful",
        *          @OA\JsonContent()
        *       )
        * )
        */
        public function favourites(Request $request)
        {
           
            $publications = $this->publicationsRepo->favourites($request);
           
            $data = $publications->toArray() ?? [];
            $data['status'] = 200;
            $data['page_size'] = intval($data['per_page']);
            unset($data['links']);
            unset($data['last_page_url']);
            unset($data['next_page_url']);
            unset($data['path']);
            unset($data['first_page_url']);
            unset($data['prev_page_url']);
    
            // Check for encoding errors
            if (json_last_error() !== JSON_ERROR_NONE) {
                // Handle the error, maybe log it or throw an exception
                throw new \Exception('Error encoding JSON: ' . json_last_error_msg());
            }
    
           return  response()->json($data, 200); 
    }

       /**
        * @OA\Get(
        * path="/api/publications/add_favourite",
        * operationId="Add Favourite Publications",
        * tags={"Add Favourite Publications"},
        *   security={{"bearer_token":{}}},
        * summary="Add Favourite Publications",
        * description="Add Favourite publications",
        *  @OA\Parameter(
        *      name="id",
        *      in="query",
        *      required=true,
        *      description="Publication Id",
        *      @OA\Schema(
        *           type="integer"
        *      )
        *   ),
        *     @OA\Response(
        *          response=200,
        *          description="Successful",
        *          @OA\JsonContent()
        *       )
        * )
        */
        public function add_favourite(Request $request)
        {
           
           $this->publicationsRepo->add_favourite($request->id);
            $data['message'] = "Added to favourites successfully";

           return  response()->json($data, 200); 
       }

    /**
    * @OA\Post(
    ** path="/api/publications",
    *   tags={"Create Publication"},
    *   summary="Create Publication",
    *   operationId="CreatePublication",
    *   security={{"bearer_token":{}}},
    *   description="Allows users to submit publications for amdin approval",
    *     @OA\RequestBody(
    *         @OA\MediaType(
    *            mediaType="multipart/form-data",
    *            @OA\Schema(
    *               type="object",
    *               required={"cover","data_category_id", "sub_theme", "description","user_id"},
    *               @OA\Property(property="cover", type="file"),
    *               @OA\Property(property="data_category_id", type="integer"),
    *               @OA\Property(property="sub_theme", type="integer"),
    *               @OA\Property(property="title", type="string"),
    *               @OA\Property(property="description", type="string"),
    *               @OA\Property(property="author", type="integer", nullable=true),
    *               @OA\Property(property="user_id", type="integer", nullable=true),
    *               @OA\Property(property="publication_category_id", type="integer"),
    *               @OA\Property(property="link", type="string",nullable=true),
    *               @OA\Property(property="communities", type="array",nullable=true, @OA\Items(type="integer"),
    *                     description="Array of PReference Ids",
    *                     example={1}),
    *            ),
    *        ),
    *    ),
     *   @OA\Response(
     *      response=201,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request, when some required data is missing"
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="not found when you send the request to an invalid endpoint"
     *   ),
     *   @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *     )
     *)
     **/
    public function store(Request $request)
    {

        Log::info("Request".json_encode($request->all()));

        $val_rules = [
            'cover'=>'required',
            //'file_type'=>'required',
            'sub_theme'=>'required',
            'description'=>'required'
        ];

        $request->validate($val_rules);
        $publication = $this->publicationsRepo->save($request);
      
        return [
            "status" => 200,
            "data" => $publication,
            "msg" => "Publication saved successfully"
        ];
    }


    
    /**
    * @OA\Post(
    ** path="/api/publications/comment",
    *   tags={"Create Publication Comment"},
    *   summary="Create Publication Comment",
    *   operationId="Comment Publication Comment",
    *   security={{"bearer_token":{}}},
    *   description="Allows users to submit publication comment for amdin approval",
    *     @OA\RequestBody(
    *         @OA\MediaType(
    *            mediaType="application/json",
    *            @OA\Schema(
    *               type="object",
    *               required={"publication_id","comment"},
    *               @OA\Property(property="publication_id", type="integer"),
    *               @OA\Property(property="comment", type="string")
    *            ),
    *        ),
    *    ),
     *   @OA\Response(
     *      response=201,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request, when some required data is missing"
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="not found when you send the request to an invalid endpoint"
     *   ),
     *   @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *     )
     *)
     **/
    public function comment(Request $request)
    {

        $val_rules = [
            'publication_id'=>'required',
            'comment'=>'required'
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
        * path="/api/publications/{id}",
        * operationId="Retrieve Single Publication",
        * tags={"Retrieve Single Publication"},
        * summary="Retrieve Single Publication",
        * description="Retrieve Single Publication",
        * @OA\Parameter(
        *      name="id",
        *      in="path",
        *      required=true,
        *      description="Record Id",
        *      @OA\Schema(
        *           type="integer"
        *      )
        *   ),
        *      @OA\Response(
        *          response=200,
        *          description="Successful",
        *          @OA\JsonContent()
        *       )
        * )
        */
    public function show($publication_id)
    {
        $publication = $this->publicationsRepo->find($publication_id);
        
        return [
            "status" => 200,
            "data" =>$publication
        ];
    }


    /**
    * @OA\Post(
    ** path="/api/publications/{id}",
    *   tags={"Update Publication"},
    *   summary="Update Publication",
    *   operationId="UpdatePublication",
    *   security={{"bearer_token":{}}},
    *   description="Allows users to update publications for amdin approval",
    *     @OA\RequestBody(
    *         @OA\JsonContent(),
    *         @OA\MediaType(
    *            mediaType="multipart/form-data",
    *            @OA\Schema(
    *               type="object",
    *               required={"cover","file_type", "sub_theme", "description","user_id"},
    *               @OA\Property(property="cover", type="file"),
    *               @OA\Property(property="file_type", type="integer"),
    *               @OA\Property(property="sub_theme", type="integer"),
    *               @OA\Property(property="title", type="string"),
    *               @OA\Property(property="description", type="string"),
    *               @OA\Property(property="author", type="integer"),
    *               @OA\Property(property="user_id", type="integer"),
    *               @OA\Property(property="link", type="string")
    *            ),
    *        ),
    *    ),
     *   @OA\Response(
     *      response=201,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request, when some required data is missing"
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="not found when you send the request to an invalid endpoint"
     *   ),
     *   @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *     )
     *)
     **/
    public function update(Request $request, Publication $Publication)
    {
        $val_rules = [
            'cover'=>'required',
            'file_type'=>'required',
            'sub_theme'=>'required',
            'description'=>'required'
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
}