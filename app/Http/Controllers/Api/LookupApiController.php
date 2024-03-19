<?php

namespace App\Http\Controllers\Api;

use App\Models\Publication;
use Illuminate\Http\Request;
use App\Repositories\AuthorsRepository;
use App\Repositories\PublicationsRepository;
use App\Repositories\QuotesRepository;
use App\Http\Controllers\Api\ApiController;
use App\Models\Country;

class LookupApiController extends ApiController
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

    

}