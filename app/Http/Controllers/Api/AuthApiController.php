<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthApiController extends ApiController
{
  
    /**
    * @OA\Post(
    * path="/api/auth/get_token",
    * operationId="Authenticates Client and returns JWT token",
    * tags={"Authenticates Client and returns JWT token"},
    * summary="Authenticates Client and returns JWT token",
    * description="Authenticates Client and returns JWT token",
    * @OA\RequestBody(
    *         @OA\MediaType(
    *            mediaType="application/json",
    *            @OA\Schema(
    *               @OA\Property(property="api_key", type="string"),
    *               @OA\Property(property="api_secret", type="string"),
    *            )
    *        )
    *   ),
    *   @OA\Response(
    *      response=200,
    *       description="Success",
    *      @OA\MediaType(
    *           mediaType="application/json",
    *      )
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
    *     ),
    *   @OA\Response(
    *          response=401,
    *          description="Invalid Client DCredentials"
    *     )
    * )
    **/
    
    public function login()
    {
        //JDJ5JDEwJHpDdXhXMWt5NTl1WEUzY1JkQzdvMk84eEI4elRlWjZPR0FGV0daTENFRGlXMXk2QlNmcTBP
        
        $credentials = ['username'=>request("api_key"),'password'=>request("api_secret")];

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

       return $this->respondWithToken($token);
    }


 /**
    * @OA\Post(
    * path="/api/auth/refresh_token",
    * operationId="Refresh JWT token",
    * tags={"Refresh JWT token"},
    * summary="Refresh JWT token",
    * description="Refresh JWT token",
    *     @OA\RequestBody(
    *         @OA\MediaType(
    *            mediaType="application/json",
    *            @OA\Schema(
    *               @OA\Property(property="token", type="string")
    *            )
    *        )
    *   ),
    *   @OA\Response(
    *      response=200,
    *       description="Success",
    *      @OA\MediaType(
    *           mediaType="application/json",
    *      )
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
    *     ),
    *   @OA\Response(
    *          response=401,
    *          description="Invalid Token"
    *     )
    * )
    **/
    
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }


    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
    

    public function username()
    {
        return 'api_key'; // The column name you wish to use for authentication
    }

    public function password()
    {
        return 'api_secret'; // The column name you wish to use for authentication
    }
    

}