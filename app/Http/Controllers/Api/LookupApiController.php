<?php

namespace App\Http\Controllers\Api;

use App\Models\Publication;
use Illuminate\Http\Request;
use App\Repositories\AuthorsRepository;
use App\Repositories\PublicationsRepository;
use App\Repositories\QuotesRepository;
use App\Repositories\ThemesRepository;
use App\Http\Controllers\Api\ApiController;
use App\Models\Country;
use Illuminate\Http\Request;

class LookupApiController extends ApiController
{
    private $publicationsRepo,$authorsRepo,$quotesRepo,$themesRepo;

    public function __construct(PublicationsRepository $publicationsRepo, 
    AuthorsRepository $authorsRepo, QuotesRepository $quotesRepo,ThemesRepository $themesRepo){

        $this->publicationsRepo = $publicationsRepo;
        $this->authorsRepo      = $authorsRepo;
        $this->quotesRepo       = $quotesRepo;
        $this->themesRepo       = $themesRepo;
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
        * tags={"List  File Types"},
        * summary="List  File Types",
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

    

}
