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
use App\Repositories\CommonsRepository;
use App\Repositories\CommsOfPracticeRepository;
use App\Repositories\ExpertsRepository;
use App\Repositories\FileTypesRepository;
use App\Repositories\TagsRepository;

class LookupApiController extends ApiController
{
    private $publicationsRepo,$authorsRepo,$quotesRepo,$themesRepo,$expertsRepo,$commsRepo,$tagsRepo,$fileTypesRepo,$commonsRepos;

    public function __construct(PublicationsRepository $publicationsRepo, 
    AuthorsRepository $authorsRepo, QuotesRepository $quotesRepo,ThemesRepository $themesRepo
    ,ExpertsRepository $expertsRepo, CommsOfPracticeRepository $commsRepo,CommonsRepository $commonsRepos
    ,TagsRepository $tagsRepo,FileTypesRepository $fileTypesRepo){

        $this->publicationsRepo = $publicationsRepo;
        $this->authorsRepo      = $authorsRepo;
        $this->quotesRepo       = $quotesRepo;
        $this->themesRepo       = $themesRepo;
        $this->expertsRepo      = $expertsRepo;
        $this->commsRepo        = $commsRepo;
        $this->tagsRepo         = $tagsRepo;
        $this->fileTypesRepo    = $fileTypesRepo;
        $this->$commonsRepos    = $commonsRepos;
    }


    /**
        * @OA\Get(
        * path="/api/lookup/filetypes",
        * operationId="List Publications File Types",
        * tags={"List  File Types"},
        * summary="List  File Types",
        * description="Returns a list of all file types",
        *      @OA\Response(
        *          response=200,
        *          description="Successful",
        *          @OA\JsonContent()
        *       )
        * )
        */
        public function file_types()
        {
            $file_types = $this->publicationsRepo->get_types();
            return [
                "status" => 200,
                "data" => $file_types
            ];
        }

      /**
        * @OA\Get(
        * path="/api/lookup/themes",
        * operationId="List Themes",
        * tags={"List Themes"},
        * summary="List Themes",
        * description="Returns a list of Themes",
        *      @OA\Response(
        *          response=200,
        *          description="Successful",
        *          @OA\JsonContent()
        *       )
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
        * path="/api/lookup/sub_themes",
        * operationId="List Sub Themes",
        * tags={"List Sub Themes"},
        * summary="List Sub Themes",
        * description="Returns a list of Sub Themes",
        *  @OA\Parameter(
        *      name="theme_id",
        *      in="query",
        *      required=false,
        *      description="Filter by Themeatic area id",
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
    
        public function sub_themes(Request $request)
        {
            $sub_themes =  $this->themesRepo->get_subthemes($request);
            return [
                "status" => 200,
                "data" => $sub_themes
            ];
        }


    
    /**
    * @OA\Get(
    * path="/api/lookup/jobs",
    * operationId="List Jobs",
    * tags={"List Jobs"},
    * summary="List Jobs",
    * description="Returns a list of Jobs",
    * @OA\Response(
    *    response=200,
    *    description="Successful",
    *    @OA\JsonContent()
    *  )
    * )
    */
    
    public function jobs(Request $request)
    {
        $sub_themes =  $this->expertsRepo->get_jobs($request,true);
        return [
            "status" => 200,
            "data" => $sub_themes
        ];
    }

      /**
    * @OA\Get(
    * path="/api/lookup/communities",
    * operationId="List Communities",
    * tags={"List Communities"},
    * summary="List Communities",
    * description="Returns a list of Communities",
    * @OA\Response(
    *    response=200,
    *    description="Successful",
    *    @OA\JsonContent()
    *  )
    * )
    */
    
    public function communities(Request $request)
    {
        $comms =  $this->commsRepo->get($request,true);
        return [
            "status" => 200,
            "data" => $comms
        ];
    }

       /**
    * @OA\Get(
    * path="/api/lookup/preferences",
    * operationId="List Preferences",
    * tags={"List Preferences"},
    * summary="List Preferences",
    * description="Returns a list of Preferences",
    * @OA\Response(
    *    response=200,
    *    description="Successful",
    *    @OA\JsonContent()
    *  )
    * )
    */
    
    public function preferences(Request $request)
    {
        $prefs =  $this->tagsRepo->get($request,true);
        return [
            "status" => 200,
            "data" => $prefs
        ];
    }

    
    /**
    * @OA\Get(
    * path="/api/lookup/file-categories",
    * operationId="List File Categories",
    * tags={"List File Categories"},
    * summary="List File Categories",
    * description="Returns a list of File Categories",
    * @OA\Response(
    *    response=200,
    *    description="Successful",
    *    @OA\JsonContent()
    *  )
    * )
    */
    
    public function file_categories(Request $request)
    {
        $categories =  $this->fileTypesRepo->get($request,true);
        return [
            "status" => 200,
            "data" => $categories
        ];
    }

       /**
    * @OA\Get(
    * path="/api/lookup/resource-categories",
    * operationId="List Resource Categories",
    * tags={"List Resource Categories"},
    * summary="List Resource Categories",
    * description="Returns a Resource of File Categories",
    * @OA\Response(
    *    response=200,
    *    description="Successful",
    *    @OA\JsonContent()
    *  )
    * )
    */
    
    public function resource_categories(Request $request)
    {
        $categories =  $this->commonsRepos->publication_categories();
        return [
            "status" => 200,
            "data" => $categories
        ];
    }
    
    
     /**
        * @OA\Get(
        * path="/api/lookup/authors",
        * operationId="List Authors",
        * tags={"List Authors"},
        * summary="List Authors",
        * description="Returns a list of Authors",
        *      @OA\Response(
        *          response=200,
        *          description="Successful",
        *          @OA\JsonContent()
        *       )
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
    

}
