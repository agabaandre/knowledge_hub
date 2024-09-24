<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Repositories\ExpertsRepository;
use Illuminate\Http\Request;

class ExpertsApiController extends ApiController
{

    private $expertsRepo;
    public function __construct(ExpertsRepository $expertsRepository){
        $this->expertsRepo = $expertsRepository;
    }
      /**
        * @OA\Get(
        * path="/api/experts",
        * operationId="List Country Experts",
        * tags={"List Country Experts"},
        * summary="List Country Experts",
        * security={ {"sanctum": {} }},
        * description="Returns a list of all Country Experts",
        *  @OA\Parameter(
        *      name="term",
        *      in="query",
        *      required=false,
        *      description="Search term for search for specific records",
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
        $request['rows'] = $request->page_size;
        $data = $this->expertsRepo->get($request);
        $data['page_size'] = intval($request->rows);
        $data['status'] = 200;

        return response()->json($data,200);
    }

   /**  @OA\Post(
    * path="/api/experts",
    *   tags={"Create Country Expert"},
    * security={{"bearer_token":{}}},
    *   summary="Create Country Expert",
    * security={{"bearer_token":{}}},
    *   operationId="CreateExpert",
    *   description="Allows users to submit country experts for amdin approval",
    *     @OA\RequestBody(
    *         @OA\MediaType(
    *            mediaType="application/json",
    *            @OA\Schema(
    *               @OA\Property(property="first_name", type="string"),
    *               @OA\Property(property="last_name", type="string"),
    *               @OA\Property(property="job_title", type="string"),
    *               @OA\Property(property="email", type="string"),
    *               @OA\Property(property="phone_number", type="string"),
    *               @OA\Property(property="type_id", type="integer"),
    *               @OA\Property(property="country_id", type="integer")
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
     *      description="not found when you sent the request to an invalid endpoint"
     *   ),
     *   @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *     )
     *)
     **/
   
    public function store(Request $request)
    {
        return  [
            "status" => 201,
            "data"   => $this->expertsRepo->save($request)
        ];
    }

   
    public function show($id)
    {
        return  [
            "status" => 200,
            "data"   => $this->expertsRepo->find($id)
        ];
    }

    /** @OA\Post(
    * path="/api/experts/{id}",
    *   tags={"Update Country Expert"},
    * security={{"bearer_token":{}}},
    *   summary="Update Country Expert",
    *   operationId="UpdateExpert",
    *   description="Allows users to update Country Expert Record",
    *     @OA\RequestBody(
    *         @OA\MediaType(
    *            mediaType="application/json",
    *            @OA\Schema(
    *               @OA\Property(property="first_name", type="string"),
    *               @OA\Property(property="last_name", type="string"),
    *               @OA\Property(property="job_title", type="string"),
    *               @OA\Property(property="email", type="string"),
    *               @OA\Property(property="phone_number", type="string"),
    *               @OA\Property(property="type_id", type="integer"),
    *               @OA\Property(property="country_id", type="integer")
    *            ),
    *        ),
    *    ),
    * @OA\Parameter(
    *      name="id",
    *      in="path",
    *      required=true,
    *      description="Record Id",
    *      @OA\Schema(
    *           type="integer"
    *      )
    *   ),
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
     *      description="not found when you sent the request to an invalid endpoint"
     *   ),
     *   @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *     )
     *)
     **/
   
    public function update(Request $request, $id)
    {
        $request['id']=$id;
       
        return  [
            "status" => 201,
            "data"   => $this->expertsRepo->save($request)
        ];
    }

   /**
* @OA\Delete(
*   path="/api/experts/{id}",
*   operationId="DeleteSingleExpertRecord",
*   tags={"Delete Expert"},
*   summary="Delete Single Expert Record",
*   description="Deletes a single expert record based on the ID.",
*   @OA\Parameter(
*       name="id",
*       in="path",
*       required=true,
*       description="Record ID",
*       @OA\Schema(
*           type="integer"
*       )
*   ),
*   @OA\Response(
*       response=200,
*       description="Successful operation",
*       @OA\JsonContent()
*   ),
*   @OA\Response(
*       response=404,
*       description="Not Found"
*   )
* )
*/

    public function destroy($id)
    {
        return  [
            "status" => 200,
            "data"   => ($this->expertsRepo->delete($id))?"Successfully deleted":"Operation Failed"
        ];
    }

}
