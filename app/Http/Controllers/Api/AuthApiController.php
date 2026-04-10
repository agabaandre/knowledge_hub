<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use App\Repositories\UsersRepository;
use App\Services\SocialLoginService;
use App\Support\OAuthAccountSecurity;
use Auth;
use Hash;
use Password;

class AuthApiController extends ApiController
{
  
    private $usersRepo,$socialLoginService;

    public function __construct( UsersRepository $usersRepo, SocialLoginService $socialLoginService)
    {
        $this->usersRepo       = $usersRepo;
        $this->socialLoginService = $socialLoginService;
    }
    /**
     * @OA\Post(
     *     path="/api/register",
     *     operationId="UserRegistration",
     *     tags={"Authentication"},
     *     summary="User Registration",
     *     description="Register a new user",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="firstname", type="string"),
     *                 @OA\Property(property="lastname", type="string"),
     *                 @OA\Property(property="country_id", type="integer"),
     *                 @OA\Property(property="job", type="string"),
     *                 @OA\Property(property="phone", type="string"),
     *                 @OA\Property(property="email", type="string"),
     *                 @OA\Property(property="password", type="string"),
     *                 @OA\Property(property="password_confirmation", type="string"),
     *                 @OA\Property(property="preferences", type="array",
     *                     @OA\Items(type="integer"),
     *                     description="Array of Preference Ids",
     *                     example={1, 2, 3}
     *                 ),
     *                 @OA\Property(
     *                     property="photo",
     *                     type="string",
     *                     format="binary",
     *                     description="Photo file to upload"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request, when some required data is missing"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found when you sent the request to an invalid endpoint"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid User Credentials"
     *     )
     * )
     */
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
     *     path="/api/login",
     *     operationId="UserLogin",
     *     tags={"Authentication"},
     *     summary="User Login",
     *     description="Authenticate a user and return a token",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="username", type="string"),
     *                 @OA\Property(property="password", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request, when some required data is missing"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found when you sent the request to an invalid endpoint"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid User Credentials"
     *     )
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt(['email' => $request->username, "password" => $request->password])) {
            return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }

        $user = Auth::user();

        if(!$user->is_verified){

            Auth::logout();

            return response()->json([
                'message' => 'User is not verified',
            ], 401);

        }

        $user->load("communities");
        $user->load("preferences");
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->accessToken;
        $tokenExpiration = $tokenResult->token->expires_at;

        updateUserPushToken($request,$user);
        //sendPushNotification( "Happy to see you again", "Welcome back ".$user->firstname, [$user->fcm_token]);


        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_at' => $tokenExpiration,
            'user' => $user
        ]);
    }
    /**
     * @OA\Post(
     *     path="/api/forgot-password",
     *     summary="Send password reset link",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset link sent"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );
 
        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Password reset link sent to your email','status'=>200], 200)
            : response()->json(['message' => 'Unable to send reset link','status'=>400], 400);
    }

    /**
     * @OA\Get(
     *     path="/api/refresh",
     *     summary="Refresh access token",
     *     tags={"Authentication"},
     *     security={{"bearer_token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Token refreshed successfully"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function refresh(Request $request)
    {
        $user = $request->user();

        if ($user && $user->token()) {
            $user->load("communities");
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->accessToken;
            $tokenExpiration = $tokenResult->token->expires_at;

            return response()->json([
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_at' => $tokenExpiration,
                'user' => $user
            ]);
        }

        return response()->json([
            'message' => 'Unauthenticated, no existing token found. Login to get a new token'
        ], 401);
    }

    /**
 * @OA\Get(
 *     path="/api/profile",
 *     operationId="UserProfile",
 *     tags={"User"},
 *     security={{"bearer_token":{}}},
 *     summary="Get User Profile",
 *     description="Retrieve a user's profile by ID",
  *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *         description="User Id",
     *         @OA\Schema(type="integer")
     *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Success",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="John Doe"),
 *                 @OA\Property(property="email", type="string", example="john.doe@example.com"),
 *                 @OA\Property(property="photo", type="string", example="http://example.com/photo.jpg")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad Request, when some required data is missing"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     )
 * )
 */
    public function profile(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:users,id',
        ]);

        $user = $this->usersRepo->profile($request);

        if (!$user) {
            return response()->json(['status' => 404, 'message' => 'User not found'], 404);
        }

        // Optionally, load related data
        $user->load('preferences', 'country', 'author', 'access_level');

        unset($user->password);
        unset($user->remember_token);

        return response()->json(['status' => 200, 'data' => $user]);
    }
    

    /**
     * @OA\Post(
     *     path="/api/change-password",
     *     summary="Change user password",
     *     tags={"Authentication"},
     *     security={{"bearer_token":{}}},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"password", "password_confirmation"},
     *             @OA\Property(property="password", type="string", description="New password"),
     *             @OA\Property(property="password_confirmation", type="string", description="Password confirmation")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password changed successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function changePassword(Request $request)
    {
        $user = $request->user();

        $validatedData = $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user->password = Hash::make($validatedData['password']);
        $user->save();

        return response()->json([
            'status'=>200,
            'message' => 'Password changed successfully',
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/logout",
     *     operationId="UserLogout",
     *     tags={"Authentication"},
     *     summary="User Logout",
     *     description="Logs out the user and revokes the token",
     *     security={{"bearer_token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully logged out"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user) {
            $this->usersRepo->logout($user);

            return response()->json([
                'status' => 200,
                'message' => 'Successfully logged out',
            ]);
        }

        return response()->json([
            'message' => 'Unauthenticated',
        ], 401);
    }

    /**
     * @OA\Post(
     *     path="/api/profile/update",
     *     operationId="UserProfileUpdate",
     *     tags={"User"},
     *     summary="Update User Profile",
     *     security={{"bearer_token":{}}},
     *     description="Update an existing user's profile",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="id", type="integer", description="User ID", example=1),
     *                 @OA\Property(property="firstname", type="string"),
     *                 @OA\Property(property="lastname", type="string"),
     *                 @OA\Property(property="country_id", type="integer"),
     *                 @OA\Property(property="job", type="string"),
     *                 @OA\Property(property="phone", type="string"),
     *                 @OA\Property(property="email", type="string"),
     *                 @OA\Property(property="preferences", type="array",
     *                     @OA\Items(type="integer"),
     *                     description="Array of Preference Ids",
     *                     example={1, 2, 3}
     *                 ),
     *                 @OA\Property(
     *                     property="photo",
     *                     type="string",
     *                     format="binary",
     *                     description="Photo file to upload"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *                 @OA\Property(property="photo", type="string", example="http://example.com/photo.jpg"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request, when some required data is missing"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function updateProfile(Request $request)
    {
        $this->validate($request, [
            'firstname' => 'sometimes|string|max:255',
            'lastname' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|exists:users,email',
            'password' => 'sometimes|string|min:6|confirmed',
            'phone' => 'sometimes|string|min:10',
            'job' => 'sometimes|string|min:4',
            'country_id' => 'sometimes|integer',
            'photo' => 'sometimes|file|image|max:2048',
            'id' => 'required|integer|exists:users,id',
        ]);

        $saved   = $this->usersRepo->update_profile($request);

        $message = ($saved)?'Profile update successfully':'Request failed try again';

        $data['message']     = $message;
        $data['status']      = ($saved)?200:400;
        $data['data']        = $saved;

        return response()->json($data);
    }

    /**
     * @OA\Post(
     *     path="/api/social-login",
     *     operationId="SocialLogin",
     *     tags={"Authentication"},
     *     summary="Social Login",
     *     description="Authenticate a user via social login",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"provider", "email", "name", "photoUrl", "providerId"},
     *             @OA\Property(property="provider", type="string", example="google"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="photoUrl", type="string"),
     *             @OA\Property(property="providerId", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request, when some required data is missing"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid User Credentials"
     *     )
     * )
     */
    public function socialLogin(Request $request)
    {
        $request->validate([
            'provider' => 'required|string|in:google,microsoft',
            'email' => 'required|string|email',
            'name' => 'required|string',
        ]);

        $email = OAuthAccountSecurity::normalizedProviderEmail($request->email);
        if (! $email) {
            return response()->json(['message' => 'Invalid email address.'], 422);
        }

        $canonical = OAuthAccountSecurity::canonicalOAuthProvider($request->provider);

        $existing = User::where('email', $email)->first();
        if ($existing) {
            if ($msg = OAuthAccountSecurity::oauthLoginDeniedMessage($existing, $canonical)) {
                return response()->json(['message' => $msg], 403);
            }
            if ($existing->is_social_login && empty($existing->social_provider)) {
                $existing->social_provider = $canonical;
                $existing->save();
            }

            $user = $existing;
            Auth::login($user);
            $user->load('communities');
            $user->load('preferences');
            $tokenResult = $user->createToken('Personal Access Token');
            updateUserPushToken($request, $user);

            return response()->json([
                'token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => $tokenResult->token->expires_at,
                'user' => $user,
            ]);
        }

        $nameParts = preg_split('/\s+/', trim((string) $request->name), 2, PREG_SPLIT_NO_EMPTY);
        $firstName = $nameParts[0] ?? '';
        $lastName = $nameParts[1] ?? '';

        $safePicture = OAuthAccountSecurity::sanitizeStoredAvatarUrl($request->photoUrl);

        $userData = (object) [
            'user' => (object) [
                'email' => $email,
                'givenName' => $firstName,
                'surname' => $lastName,
                'given_name' => $firstName,
                'family_name' => $lastName,
                'mail' => $email,
                'jobTitle' => null,
                'picture' => $safePicture,
            ],
            'providerId' => $request->providerId,
            'social_provider' => $canonical,
        ];

        if ($canonical === 'google') {
            $savedUser = $this->socialLoginService->googleCallback($userData);
        } elseif ($canonical === 'microsoft') {
            $savedUser = $this->socialLoginService->microsoftCallback($userData);
        } else {
            return response()->json(['message' => 'Unsupported provider'], 400);
        }

        if ($savedUser) {
            $user = $savedUser;
            Auth::login($user);
            $user->load('communities');
            $user->load('preferences');
            $tokenResult = $user->createToken('Personal Access Token');
            updateUserPushToken($request, $user);

            return response()->json([
                'token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => $tokenResult->token->expires_at,
                'user' => $user,
            ]);
        }

        return response()->json(['message' => 'Unable to log you in'], 400);
    }
    
}
