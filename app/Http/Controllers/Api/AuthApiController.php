<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Repositories\UsersRepository;

class AuthApiController extends ApiController
{
  
    private $usersRepo;

    public function __construct( UsersRepository $usersRepo)
    {
        $this->usersRepo       = $usersRepo;
    }
    /**
    * @OA\Post(
    * path="/api/register",
    * operationId="User Registration",
    * tags={"User Registration"},
    * summary="User Registration",
    * description="User Registration",
    * @OA\RequestBody(
    *         @OA\MediaType(
    *            mediaType="application/json",
    *            @OA\Schema(
    *               @OA\Property(property="first_name", type="string"),
    *               @OA\Property(property="last_name", type="string"),
    *               @OA\Property(property="country_id", type="string"),
    *               @OA\Property(property="job", type="string"),
    *               @OA\Property(property="phone_number", type="string"),
    *               @OA\Property(property="email", type="string"),
    *               @OA\Property(property="password", type="string"),
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
    *      description="Not found when you sent the request to an invalid endpoint"
    *   ),
    *   @OA\Response(
    *          response=403,
    *          description="Forbidden"
    *     ),
    *   @OA\Response(
    *          response=401,
    *          description="Invalid User Credentials"
    *     )
    * )
    **/
    public function register(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'phone_number' => 'required|string|min:10',
        ]);

        $user   = $this->usersRepo->update_profile($request);
        $token = auth()->login($user);

        return $this->respondWithToken($token);
    }

    /**
    * @OA\Post(
    * path="/oauth/token",
    * operationId="User Login",
    * tags={"User Login"},
    * summary="User Login",
    * description="User Login",
    * @OA\RequestBody(
    *         @OA\MediaType(
    *            mediaType="application/json",
    *            @OA\Schema(
    *               @OA\Property(property="username", type="string"),
    *               @OA\Property(property="password", type="string"),
    *               @OA\Property(property="refresh_token", type="string"),
    *               @OA\Property(property="client_id", type="string"),
    *               @OA\Property(property="client_secret", type="string"),
    *               @OA\Property(property="grant_type", type="string"),
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
    *      description="Not found when you sent the request to an invalid endpoint"
    *   ),
    *   @OA\Response(
    *          response=403,
    *          description="Forbidden"
    *     ),
    *   @OA\Response(
    *          response=401,
    *          description="Invalid User Credentials"
    *     )
    * )
    **/
    
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }


    

}