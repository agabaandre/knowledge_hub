<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Repositories\ExpertsRepository;
use App\Repositories\ForumsRepository;
use Illuminate\Http\Request;

class ForumsApiController extends ApiController
{

    private $forumsRepo;
    public function __construct(ForumsRepository $forumsRepo){
        $this->forumsRepo = $forumsRepo;
    }
      /**
        * @OA\Get(
        * path="/api/forums",
        * operationId="List Forums",
        * tags={"List Forums"},
        * summary="List Forums",
        * description="Returns a list of all Forums",
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

        $request['rows'] = $request->page_size ?? 20;
        $data = $this->forumsRepo->get($request)->toArray() ?? [];
        $data['status']  =200;
        $data['page_size']=$request->rows;
        unset($data['links']);

        return  response()->json($data,200);
    }

   /**  @OA\Post(
    * path="/api/forums",
    *   tags={"Create Forum Discussion"},
    *   summary="Create Forum",
    * security={{"bearer_token":{}}},
    *   operationId="CreateForum",
    *   description="Allows users to submit Forum Discussion",
    *     @OA\RequestBody(
    *         @OA\MediaType(
    *            mediaType="multipart/form-data",
    *            @OA\Schema(
    *               @OA\Property(property="title", type="string"),
    *               @OA\Property(property="description", type="string"),
    *               @OA\Property(
    *                     property="image",
    *                     type="string",
    *                     format="binary",
    *                     description="Photo file to upload"
    *                )
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
            "data"   => $this->forumsRepo->save($request)
        ];
    }


       /**  @OA\Post(
    * path="/api/forums/comment",
    *   tags={"Create Forum Discussion Comment"},
    *   summary="Create Forum Comment",
    * security={{"bearer_token":{}}},
    *   operationId="CreateForumCOmment",
    *   description="Allows users to submit Forum Discussion Comment",
    *     @OA\RequestBody(
    *         @OA\MediaType(
    *            mediaType="application/json",
    *            @OA\Schema(
    *               @OA\Property(property="id", type="integer"),
    *               @OA\Property(property="comment", type="string"),
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
   
     public function comment(Request $request)
     {
         return  [
             "status" => 201,
             "data"   => $this->forumsRepo->save_comment($request)
         ];
     }

   
    public function show($id)
    {
        $data = $this->forumsRepo->find($id);
        $data['status'] = 200;
        return  response()->json($data,200);
    }

   /**
* @OA\Delete(
*   path="/api/forums/{id}",
*   operationId="Delete Forum",
*   tags={"Delete Forum"},
*   summary="Delete Forum",
*   description="Deletes a single forum record based on the ID.",
*   @OA\Parameter(
*       name="id",
*       in="path",
*       required=true,
*       description="Froum ID",
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
            "data"   => ($this->forumsRepo->delete($id))?"Successfully deleted":"Operation Failed"
        ];
    }

}
