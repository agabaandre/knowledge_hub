<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Repositories\UsersRepository;
use App\Services\SocialLoginService;
use App\Support\OAuthAccountSecurity;
use Auth;
use Carbon\Carbon;
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
     *     description="Same fields as web `POST /registration` (register form). Password min length 8. Send `preferences` as sub-theme IDs (at least one). If `job_missing` is true/1, `job` from the dropdown is optional; otherwise `job` is required unless `job_title_custom` is provided. Use multipart for `photo`. JSON-encoded arrays accepted for `preferences` and `communities` when sent as strings.",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"firstname","lastname","email","password","password_confirmation","phone","country_id","preferences"},
     *                 @OA\Property(property="firstname", type="string"),
     *                 @OA\Property(property="lastname", type="string"),
     *                 @OA\Property(property="email", type="string", format="email"),
     *                 @OA\Property(property="phone", type="string", description="Min 10 characters"),
     *                 @OA\Property(property="password", type="string", format="password", description="Min 8 characters"),
     *                 @OA\Property(property="password_confirmation", type="string", format="password"),
     *                 @OA\Property(property="country_id", type="integer"),
     *                 @OA\Property(property="job", type="string", description="Job title from lookup list (name or id); omit when job_missing=1"),
     *                 @OA\Property(property="job_missing", type="boolean", description="My job title is missing from the list"),
     *                 @OA\Property(property="job_title_custom", type="string", description="Custom job title when job_missing"),
     *                 @OA\Property(property="preferences", type="array", description="Sub-theme IDs (Your Interests)", @OA\Items(type="integer")),
     *                 @OA\Property(property="communities", type="array", description="Preferred community IDs to join", @OA\Items(type="integer")),
     *                 @OA\Property(property="organization_name", type="string"),
     *                 @OA\Property(property="orcid", type="string", maxLength=19),
     *                 @OA\Property(property="langauge", type="string", description="Site language code (same spelling as web form, e.g. en)"),
     *                 @OA\Property(property="subscribe", type="boolean", description="Newsletter opt-in (also accepts is_subscribed)"),
     *                 @OA\Property(property="is_subscribed", type="boolean"),
     *                 @OA\Property(property="photo", type="string", format="binary", description="Profile photo")
     *             )
     *         ),
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"firstname","lastname","email","password","password_confirmation","phone","country_id","preferences"},
     *                 @OA\Property(property="firstname", type="string"),
     *                 @OA\Property(property="lastname", type="string"),
     *                 @OA\Property(property="email", type="string"),
     *                 @OA\Property(property="phone", type="string"),
     *                 @OA\Property(property="password", type="string"),
     *                 @OA\Property(property="password_confirmation", type="string"),
     *                 @OA\Property(property="country_id", type="integer"),
     *                 @OA\Property(property="job", type="string"),
     *                 @OA\Property(property="job_missing", type="boolean"),
     *                 @OA\Property(property="job_title_custom", type="string"),
     *                 @OA\Property(property="preferences", type="array", @OA\Items(type="integer")),
     *                 @OA\Property(property="communities", type="array", @OA\Items(type="integer")),
     *                 @OA\Property(property="organization_name", type="string"),
     *                 @OA\Property(property="orcid", type="string"),
     *                 @OA\Property(property="langauge", type="string"),
     *                 @OA\Property(property="subscribe", type="boolean"),
     *                 @OA\Property(property="is_subscribed", type="boolean")
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
        $this->normalizeRegistrationRequest($request);

        $request->validate([
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['required', 'string', 'min:10'],
            'country_id' => ['required', 'integer', 'exists:country,id'],
            'preferences' => ['required', 'array', 'min:1'],
            'preferences.*' => ['integer', 'exists:sub_thematic_area,id'],
            'job' => [
                Rule::excludeIf(fn () => $request->boolean('job_missing')),
                'required_without:job_title_custom',
                'nullable',
                'string',
                'max:255',
            ],
            'job_title_custom' => ['nullable', 'string', 'max:255'],
            'job_missing' => ['sometimes', 'boolean'],
            'communities' => ['sometimes', 'array'],
            'communities.*' => ['integer', 'exists:community_of_practices,id'],
            'organization_name' => ['nullable', 'string', 'max:255'],
            'orcid' => ['nullable', 'string', 'max:19'],
            'langauge' => ['nullable', 'string', 'max:32'],
            'subscribe' => ['sometimes'],
            'is_subscribed' => ['sometimes', 'boolean'],
            'photo' => ['sometimes', 'file', 'image', 'max:5120'],
        ]);

        if ($request->boolean('is_subscribed') || $request->boolean('subscribe')) {
            $request->merge(['subscribe' => 'on']);
        }

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
     *     description="Authenticate with the account email and password. Returns a bearer token.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"email","password"},
     *                 @OA\Property(property="email", type="string", format="email"),
     *                 @OA\Property(property="password", type="string", format="password")
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
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $loginEmail = $request->input('email');

        if (!Auth::attempt(['email' => $loginEmail, 'password' => $request->password])) {
            return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }

        $user = Auth::user();

        if ($user->is_social_login) {
            Auth::logout();
            $providerLabel = ($user->social_provider !== null && $user->social_provider !== '')
                ? OAuthAccountSecurity::providerDisplayName((string) $user->social_provider)
                : 'social';

            return response()->json([
                'message' => 'This account uses '.$providerLabel.' sign-in. Use social login in the app, or contact support to add a password.',
            ], 403);
        }

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
     *     summary="Get current user profile",
     *     description="Returns the authenticated user's profile (same fields as the web account page). Optional `id` query parameter loads another user only if the caller has `alter_access_levels` permission.",
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=false,
     *         description="Optional user id (admins / users with alter_access_levels only)",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="Full user row (first_name, last_name, email, phone_number, job_title, organization_name, orcid, country_id, langauge, theme_preference, is_subscribed, is_social_login, photo URL, access_level, country, author, communities, preferences, etc.) plus preference_subtheme_ids, level_id (same as access_level_id), and community_ids"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden (viewing another user without permission)"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function profile(Request $request)
    {
        $auth = $request->user();
        $requestedId = $request->query('id');

        if ($requestedId !== null && $requestedId !== '') {
            $requestedId = (int) $requestedId;
            if ($requestedId !== (int) $auth->id) {
                if (! $auth->can('alter_access_levels')) {
                    return response()->json(['status' => 403, 'message' => 'Forbidden'], 403);
                }
            }
            $user = User::find($requestedId);
        } else {
            $user = $auth;
        }

        if (! $user) {
            return response()->json(['status' => 404, 'message' => 'User not found'], 404);
        }

        return response()->json([
            'status' => 200,
            'data' => $this->profilePayload($user),
        ]);
    }

    /**
     * Serialize user for GET /api/profile and successful profile updates.
     */
    private function profilePayload(User $user): array
    {
        $u = User::query()
            ->with(['preferences', 'country', 'author', 'access_level', 'communities'])
            ->find($user->id);

        if (! $u) {
            return [];
        }

        $payload = $u->makeHidden(['password', 'remember_token'])->toArray();
        // Mirror web account form / update payload naming for clients
        $payload['preference_subtheme_ids'] = $u->preferences->pluck('subtheme_id')->values()->all();
        $payload['level_id'] = $u->access_level_id;
        $payload['community_ids'] = $u->communities->pluck('id')->values()->all();

        return $payload;
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
     *         description="Same logical fields as the web account form (`/account`). The authenticated user is always updated; do not send `id`. Use multipart when uploading `photo`.",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="first_name", type="string"),
     *                 @OA\Property(property="last_name", type="string"),
     *                 @OA\Property(property="firstname", type="string", description="Alias for first_name"),
     *                 @OA\Property(property="lastname", type="string", description="Alias for last_name"),
     *                 @OA\Property(property="email", type="string", format="email"),
     *                 @OA\Property(property="phone_number", type="string"),
     *                 @OA\Property(property="phone", type="string", description="Alias for phone_number"),
     *                 @OA\Property(property="country_id", type="integer"),
     *                 @OA\Property(property="job", type="string"),
     *                 @OA\Property(property="job_title", type="string"),
     *                 @OA\Property(property="job_title_custom", type="string"),
     *                 @OA\Property(property="organization_name", type="string"),
     *                 @OA\Property(property="orcid", type="string"),
     *                 @OA\Property(property="langauge", type="string", description="Language code (matches web form spelling)"),
     *                 @OA\Property(property="theme_preference", type="string", enum={"light","dark","system"}),
     *                 @OA\Property(property="is_subscribed", type="boolean"),
     *                 @OA\Property(property="level_id", type="integer", description="Access level; only applied if user has alter_access_levels"),
     *                 @OA\Property(property="password", type="string", format="password"),
     *                 @OA\Property(property="password_confirmation", type="string", format="password"),
     *                 @OA\Property(property="preferences", type="array",
     *                     @OA\Items(type="integer"),
     *                     description="Subtheme ids (same as web preferences[])",
     *                     example={1, 2, 3}
     *                 ),
     *                 @OA\Property(
     *                     property="photo",
     *                     type="string",
     *                     format="binary",
     *                     description="Profile photo file (stored like web account upload)"
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
        $userId = $request->user()->id;

        $this->validate($request, [
            'first_name' => 'sometimes|string|max:255',
            'firstname' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'lastname' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'password' => 'sometimes|string|min:6|confirmed',
            'phone_number' => 'sometimes|nullable|string|max:64',
            'phone' => 'sometimes|nullable|string|max:64',
            'job' => 'sometimes|nullable|string|max:255',
            'job_title' => 'sometimes|nullable|string|max:255',
            'job_title_custom' => 'sometimes|nullable|string|max:255',
            'organization_name' => 'sometimes|nullable|string|max:255',
            'orcid' => 'sometimes|nullable|string|max:64',
            'country_id' => 'sometimes|nullable|integer|exists:country,id',
            'langauge' => 'sometimes|nullable|string|max:32',
            'theme_preference' => 'sometimes|nullable|in:light,dark,system',
            'is_subscribed' => 'sometimes|boolean',
            'level_id' => 'sometimes|nullable|integer',
            'preferences' => 'sometimes|array',
            'preferences.*' => 'integer',
            'photo' => 'sometimes|file|image|max:2048',
        ]);

        $request->merge(['id' => $userId]);

        $saved = $this->usersRepo->save($request);

        if (! $saved) {
            return response()->json([
                'message' => 'Request failed try again',
                'status' => 400,
                'data' => null,
            ], 400);
        }

        return response()->json([
            'message' => 'Profile update successfully',
            'status' => 200,
            'data' => $this->profilePayload($saved),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/social-login",
     *     operationId="SocialLogin",
     *     tags={"Authentication"},
     *     summary="Social login (Google, Microsoft, LinkedIn)",
     *     description="Exchange a verified identity from your mobile or web OAuth flow for a **Passport bearer token**.\n\n**How it works**\n1. In your app, complete sign-in with the provider SDK (Google Sign-In, Microsoft MSAL, LinkedIn OpenID Connect).\n2. Read **email** and **display name** from the IdP user profile (and ideally **subject id** + **photo URL**).\n3. `POST` JSON to this endpoint with `provider` set to `google`, `microsoft`, `linkedin`, or `linkedin-openid`.\n4. Use the returned `token` as `Authorization: Bearer <token>` on other `/api/*` routes.\n\n**Providers**\n- **Google** — `provider`: `google`. Use the email and name from Google Sign-In / OAuth userinfo.\n- **Microsoft** — `provider`: `microsoft`. Use Entra ID / Microsoft account email and display name from MSAL / Graph.\n- **LinkedIn** — `provider`: `linkedin` or `linkedin-openid` (both are treated as LinkedIn).\n\n**Notes**\n- `email` must be the verified address from the IdP. If the email already exists as a **password-only** account, the API returns **403**.\n- If the email exists under a **different** social provider, the API returns **403** with a message to use the original provider.\n- New users are created on first successful call; existing social users receive a new token.\n\nTry the **Examples** dropdown in Swagger UI (`google_signin`, `microsoft_msal`, `linkedin_openid`).",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Payload built from your OAuth / OIDC user profile after successful provider sign-in.",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"provider", "email", "name"},
     *                 @OA\Property(
     *                     property="provider",
     *                     type="string",
     *                     enum={"google", "microsoft", "linkedin", "linkedin-openid"},
     *                     example="google",
     *                     description="Identity provider key accepted by the API."
     *                 ),
     *                 @OA\Property(property="email", type="string", format="email", example="jane.doe@gmail.com", description="Verified email from the IdP."),
     *                 @OA\Property(property="name", type="string", example="Jane Doe", description="Full display name; server splits into first/last when creating a user."),
     *                 @OA\Property(property="photoUrl", type="string", format="uri", nullable=true, description="Optional profile image URL (https recommended; localhost blocked in production)."),
     *                 @OA\Property(property="providerId", type="string", nullable=true, description="Optional stable subject from the IdP (Google `sub`, Microsoft `oid`, LinkedIn person URN, etc.).")
     *             ),
     *             @OA\Examples(
     *                 example="google_signin",
     *                 summary="Google Sign-In",
     *                 description="After Google OAuth / Google Sign-In for Android, iOS, or web — send email, name, and optional sub + picture from userinfo.",
     *                 value={"provider":"google","email":"jane.doe@gmail.com","name":"Jane Doe","providerId":"109876543210987654321","photoUrl":"https://lh3.googleusercontent.com/a-/AOh14GgExampleProfilePhoto"}
     *             ),
     *             @OA\Examples(
     *                 example="microsoft_msal",
     *                 summary="Microsoft (MSAL / Entra)",
     *                 description="After Microsoft sign-in — use mail and displayName from Microsoft Graph / token claims; oid as providerId when available.",
     *                 value={"provider":"microsoft","email":"jane.doe@contoso.com","name":"Jane Doe","providerId":"a1b2c3d4-e5f6-7890-abcd-ef1234567890","photoUrl":"https://graph.microsoft.com/v1.0/me/photo/$value"}
     *             ),
     *             @OA\Examples(
     *                 example="linkedin_openid",
     *                 summary="LinkedIn (OIDC)",
     *                 description="Use `linkedin` or `linkedin-openid`. Send email and name from LinkedIn's OpenID Connect userinfo; person id or URN as providerId if available.",
     *                 value={"provider":"linkedin-openid","email":"jane.doe@company.org","name":"Jane Doe","providerId":"urn:li:person:AbCdEfGhIj","photoUrl":"https://media.licdn.com/dms/image/v2/example-profile-photo-endpoint"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Passport access token and user",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.example..."),
     *             @OA\Property(property="token_type", type="string", example="Bearer"),
     *             @OA\Property(property="expires_at", type="string", format="date-time", nullable=true),
     *             @OA\Property(property="user", type="object", description="User model including communities and preferences relations.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request or unable to complete login"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Account exists with password only, or registered with a different social provider"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error (e.g. invalid email)"
     *     )
     * )
     */
    public function socialLogin(Request $request)
    {
        if ($request->has('provider')) {
            $request->merge([
                'provider' => strtolower(trim((string) $request->input('provider'))),
            ]);
        }

        $request->validate([
            'provider' => 'required|string|in:google,microsoft,linkedin,linkedin-openid',
            'email' => 'required|string|email',
            'name' => 'required|string',
            'photoUrl' => 'nullable|string|max:2048',
            'providerId' => 'nullable|string|max:255',
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
            }
            if (! $existing->email_verified_at) {
                $existing->email_verified_at = Carbon::now();
            }
            if (! $existing->is_verified) {
                $existing->is_verified = 1;
            }
            if ($existing->status != 1) {
                $existing->status = 1;
            }
            $existing->save();

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
        } elseif ($canonical === 'linkedin') {
            $linkedinPayload = (object) [
                'user' => (object) [
                    'email' => $email,
                    'firstName' => $firstName,
                    'lastName' => $lastName,
                    'localizedFirstName' => $firstName,
                    'localizedLastName' => $lastName,
                    'headline' => null,
                    'profilePicture' => $safePicture,
                    'picture' => $safePicture,
                ],
            ];
            $savedUser = $this->socialLoginService->linkedinCallback($linkedinPayload);
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

    /**
     * Decode JSON array strings for multipart clients; normalize job_missing to boolean (matches web checkbox semantics).
     */
    private function normalizeRegistrationRequest(Request $request): void
    {
        foreach (['preferences', 'communities'] as $key) {
            if (! $request->has($key)) {
                continue;
            }
            $v = $request->input($key);
            if (is_string($v)) {
                $t = trim($v);
                if ($t !== '' && str_starts_with($t, '[')) {
                    $decoded = json_decode($t, true);
                    if (is_array($decoded)) {
                        $request->merge([$key => $decoded]);
                    }
                }
            }
        }

        if ($request->has('job_missing')) {
            $jm = $request->input('job_missing');
            $truthy = $jm === true || $jm === 1 || $jm === '1' || $jm === 'true' || $jm === 'on' || $jm === 'yes';
            $request->merge(['job_missing' => $truthy]);
        }
    }
}
