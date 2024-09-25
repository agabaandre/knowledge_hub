<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Repositories\UsersRepository;
use Auth;

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
    *            mediaType="multipart/form-data",
    *            @OA\Schema(
    *               @OA\Property(property="firstname", type="string"),
    *               @OA\Property(property="lastname", type="string"),
    *               @OA\Property(property="country_id", type="integer"),
    *               @OA\Property(property="job", type="string"),
    *               @OA\Property(property="phone", type="string"),
    *               @OA\Property(property="email", type="string"),
    *               @OA\Property(property="password", type="string"),
      *               @OA\Property(property="password_confirmation", type="string"),
    *               @OA\Property(property="preferences", type="array",
    *                  @OA\Items(type="integer"),
    *                     description="Array of PReference Ids",
    *                     example={1, 2, 3}),
    *               @OA\Property(
    *                     property="photo",
    *                     type="string",
    *                     format="binary",
    *                     description="Photo file to upload"
    *                )
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
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'required|string|min:10',
            'job' => 'required|string|min:4',
            'country_id' => 'required|integer'
        ]);

        $user   = $this->usersRepo->save($request);
        $client = \Laravel\Passport\Client::where('password_client', 1)->first();
        $token  = $user->createToken( 'KhubApp', ['*'], $client->id)->accessToken;

        $user->photo = user_profile_photo($user->photo);
        $user->verification_token = null;
        $user->token = $token;
        unset($user->password);
        unset($user->area);

        return   response()->json(["status"=>200,"data"=>$user]);
    }

    /**
    * @OA\Post(
    * path="/api/login",
    * operationId="User Login",
    * tags={"User Login"},
    * summary="User Login",
    * description="User Login",
    * @OA\RequestBody(
    *         @OA\MediaType(
    *            mediaType="application/json",
    *            @OA\Schema(
    *               @OA\Property(property="username", type="string"),
    *               @OA\Property(property="password", type="string")
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
        
        
        // $credentials = $request->only('email', 'password');

        // if (! $token = auth()->attempt($credentials)) {
        //     return response()->json(['error' => 'Unauthorized'], 401);
        // }

        // return response()->json(["status"=>200,"data"=>"Users Passport Instead"]);

        // Validate the request
        $request->validate([
            'username' => 'required',
            'password' => 'required|string',
        ]);

        // Attempt to authenticate the user
        if (!Auth::attempt(['email'=>$request->username,"password"=>$request->password])) {
            return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }

        // Get the authenticated user
        $user = Auth::user();

        // Generate access token using Passport
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->accessToken;
        $tokenExpiration = $tokenResult->token->expires_at;

        // Return token and user object
        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_at' => $tokenExpiration,
            'user' => $user
        ]);
    }


    

}