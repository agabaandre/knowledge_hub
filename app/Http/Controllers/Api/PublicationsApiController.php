<?php

namespace App\Http\Controllers\Api;

use App\Models\Publication;
use Illuminate\Http\Request;
use App\Repositories\AuthorsRepository;
use App\Repositories\PublicationsRepository;
use App\Repositories\QuotesRepository;
use App\Http\Controllers\Controller;

class PublicationsApiController extends Controller
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
        * path="/knowhub/api/publications",
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
        *      @OA\Response(
        *          response=200,
        *          description="Successful",
        *          @OA\JsonContent()
        *       )
        * )
        */
    public function index(Request $request)
    {
        $publications = $this->publicationsRepo->get($request);
        return [
            "status" => 200,
            "data" => $publications
        ];
    }

   
    public function store(Request $request)
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
            "msg" => "Publication saved successfully"
        ];
    }

    public function show(Publication $publication)
    {
       
        return [
            "status" => 200,
            "data" =>$publication
        ];
    }


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