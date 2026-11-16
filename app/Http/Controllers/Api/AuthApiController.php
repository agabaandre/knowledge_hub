<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Repositories\UsersRepository;
use App\Rules\InternationalPhoneKnownCallingCode;
use App\Rules\NotDisposableEmail;
use App\Services\SocialLoginService;
use App\Support\ApiUserDetails;
use App\Support\AuthEventLogger;
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

        if (\App\Models\BannedEmail::isBanned($request->input('email'))) {
            AuthEventLogger::log($request, 'auth.register', 'blocked', ['email' => (string) $request->input('email')]);

            return response()->json(['message' => 'This email address cannot be used to register.'], 403);
        }

        $request->validate([
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users', new NotDisposableEmail()],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['required', 'string', 'min:10'],
            'country_id' => ['required', 'integer', 'exists:country,id'],
            'preferences' => ['required', 'array', 'min:1'],
            'preferences.*' => ['integer', 'exists:sub_thematic_area,id'],
            // Rule::excludeIf needs Laravel 9+; build the equivalent on 8.x.
            'job' => $request->boolean('job_missing')
                ? ['nullable', 'string', 'max:255']
                : ['required_without:job_title_custom', 'nullable', 'string', 'max:255'],
            'job_title_custom' => ['nullable', 'string', 'max:255'],
            'job_missing' => ['sometimes', 'boolean'],
            'communities' => ['sometimes', 'array'],
            'communities.*' => ['integer', 'exists:community_of_practices,id'],
            'organization_name' => ['nullable', 'string', 'max:255'],
            'orcid' => ['nullable', 'string', 'max:19'],
            'langauge' => ['nullable', 'string', 'max:32'],
            'subscribe' => ['sometimes'],
            'is_subscribed' => ['sometimes', 'boolean'],
            'photo' => ['sometimes', 'file', 'mimes:'.\App\Support\SafeUpload::mimesRule(\App\Support\SafeUpload::IMAGES), 'max:5120'],
        ]);

        if ($request->boolean('is_subscribed') || $request->boolean('subscribe')) {
            $request->merge(['subscribe' => 'on']);
        }

        $user   = $this->usersRepo->save($request);
        AuthEventLogger::trackAccountCreation($request, $user);
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
     *     summary="User login (Passport token)",
     *     description="Authenticate with **email** and **password**. Returns a Laravel Passport access token — use it as `Authorization: Bearer {token}` on protected routes. Social-only accounts receive 403 with guidance to use social login.",
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
     *         description="Authenticated; use `token` as Bearer access token",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", description="Passport access token"),
     *             @OA\Property(property="token_type", type="string", example="Bearer"),
     *             @OA\Property(property="expires_at", type="string", format="date-time", nullable=true),
     *             @OA\Property(property="user", type="object", description="User model without password")
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
     *         description="Forbidden (e.g. social-only account)"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials or unverified account"
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
            AuthEventLogger::log($request, 'auth.login', 'failed', ['email' => (string) $loginEmail]);

            return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }

        $user = Auth::user();

        if ((int) ($user->status ?? 1) === 0 || \App\Models\BannedEmail::isBanned($user->email)) {
            Auth::logout();
            AuthEventLogger::log($request, 'auth.login', 'blocked', ['email' => (string) $loginEmail]);

            return response()->json([
                'message' => 'This account has been deactivated. Contact the hub administrators.',
            ], 403);
        }

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
        AuthEventLogger::log($request, 'auth.login', 'success', ['user_id' => $user->id]);

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
        $u = ApiUserDetails::loadForFull((int) $user->id);

        return $u ? ApiUserDetails::fullDetails($u) : [];
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
     *         description="Same field names as the web account profile form at `/account` (Personal Information). The authenticated user is always updated; do not send `id`. Use `multipart/form-data` when uploading `photo`. Password changes use `POST /api/change-password`, not this endpoint. JSON-encoded arrays are accepted for `preferences` and `communities` when sent as strings (same behaviour as `POST /api/publications` and registration).",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="first_name", type="string", description="First name (required on web when updating profile)."),
     *                 @OA\Property(property="last_name", type="string", description="Last name (required on web when updating profile)."),
     *                 @OA\Property(property="email", type="string", format="email"),
     *                 @OA\Property(property="phone_number", type="string", nullable=true, description="International format; digits (after optional +) must begin with an ITU calling code present on a `country` row with non-empty `phonecode` (includes African member states and other countries in the database). Example: +251115517700."),
     *                 @OA\Property(property="country_id", type="integer", description="Country id (required on web)."),
     *                 @OA\Property(property="job", type="string", nullable=true, description="Job title from the dropdown (same as web `job`)."),
     *                 @OA\Property(property="job_missing", type="boolean", nullable=true, description="Set true when using custom job title (same as web checkbox)."),
     *                 @OA\Property(property="job_title_custom", type="string", nullable=true, description="Custom job title when `job_missing` is true."),
     *                 @OA\Property(property="organization_name", type="string", nullable=true),
     *                 @OA\Property(property="orcid", type="string", nullable=true, maxLength=19),
     *                 @OA\Property(property="langauge", type="string", nullable=true, description="Language code (same spelling as web form field `langauge`)."),
     *                 @OA\Property(property="theme_preference", type="string", enum={"light","dark","system"}, nullable=true),
     *                 @OA\Property(property="is_subscribed", type="boolean", nullable=true, description="Subscribe to monthly updates (same as web checkbox)."),
     *                 @OA\Property(property="level_id", type="integer", nullable=true, description="Access level; only applied if the user has `alter_access_levels`."),
     *                 @OA\Property(property="preferences", type="array",
     *                     @OA\Items(type="integer"),
     *                     description="Subtheme ids (same as web `preferences[]`).",
     *                     example={1, 2, 3}
     *                 ),
     *                 @OA\Property(property="communities", type="array",
     *                     @OA\Items(type="integer"),
     *                     nullable=true,
     *                     description="Community of practice ids (same as web `communities[]`)."
     *                 ),
     *                 @OA\Property(
     *                     property="photo",
     *                     type="string",
     *                     format="binary",
     *                     description="Profile photo file (same as web account upload)."
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

        // Accept legacy aliases once, then validate only canonical names used by `/account`.
        if (! $request->filled('first_name') && $request->filled('firstname')) {
            $request->merge(['first_name' => $request->input('firstname')]);
        }
        if (! $request->filled('last_name') && $request->filled('lastname')) {
            $request->merge(['last_name' => $request->input('lastname')]);
        }
        if (! $request->filled('phone_number') && $request->filled('phone')) {
            $request->merge(['phone_number' => $request->input('phone')]);
        }

        // Multipart clients often send array fields as JSON strings (same as registration and POST /api/publications).
        $this->normalizeRegistrationRequest($request);

        $this->validate($request, [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'phone_number' => ['sometimes', 'nullable', 'string', 'max:64', new InternationalPhoneKnownCallingCode()],
            'job' => 'sometimes|nullable|string|max:255',
            'job_missing' => 'sometimes|boolean',
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
            'communities' => 'sometimes|array',
            'communities.*' => 'integer|exists:community_of_practices,id',
            'photo' => 'sometimes|file|mimes:'.\App\Support\SafeUpload::mimesRule(\App\Support\SafeUpload::IMAGES).'|max:2048',
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
     *     summary="Social login (Google, Microsoft, LinkedIn) — v2, token-verified",
     *     description="**Contract v2 (breaking change).** Exchange a **provider credential** for a Passport bearer token. The server verifies the credential with the provider and derives the identity (email, name, subject) exclusively from it — `email`/`name` request fields are no longer accepted.\n\n**How it works**\n1. Complete sign-in with the provider SDK (Google Sign-In, Microsoft MSAL, LinkedIn OIDC).\n2. Send the credential:\n- **Google** — `idToken` (the Google ID token). Verified against Google JWKS; `aud` must be one of this hub's client IDs and the Google account email must be verified.\n- **Microsoft** — `idToken` (Entra ID token, verified against Microsoft JWKS, tenant-checked) **or** `accessToken` (validated server-side via Microsoft Graph `/me`).\n- **LinkedIn** — `accessToken` (validated server-side via LinkedIn OIDC userinfo).\n3. Use the returned `token` as `Authorization: Bearer <token>`.\n\n**Account matching**\n- Users are matched on the provider subject (`sub`/`oid`), stored in `social_accounts` — not on email alone.\n- A social identity links to an existing **password** account only when the IdP email is verified **and** the hub setting allows it (off by default).\n- **Administrator accounts never sign in socially** — password (+2FA) only.\n- New users are created at the lowest access level; their publications require approval.",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Provider credential obtained by your app from the provider SDK.",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"provider"},
     *                 @OA\Property(
     *                     property="provider",
     *                     type="string",
     *                     enum={"google", "microsoft", "linkedin", "linkedin-openid"},
     *                     example="google",
     *                     description="Identity provider key accepted by the API."
     *                 ),
     *                 @OA\Property(property="idToken", type="string", nullable=true, description="OIDC ID token (required for Google; Microsoft accepts idToken or accessToken)."),
     *                 @OA\Property(property="accessToken", type="string", nullable=true, description="OAuth access token (required for LinkedIn; alternative for Microsoft).")
     *             ),
     *             @OA\Examples(
     *                 example="google_signin",
     *                 summary="Google Sign-In",
     *                 value={"provider":"google","idToken":"eyJhbGciOiJSUzI1NiIsImtpZCI6..."}
     *             ),
     *             @OA\Examples(
     *                 example="microsoft_msal",
     *                 summary="Microsoft (MSAL / Entra)",
     *                 value={"provider":"microsoft","idToken":"eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIs..."}
     *             ),
     *             @OA\Examples(
     *                 example="linkedin_openid",
     *                 summary="LinkedIn (OIDC)",
     *                 value={"provider":"linkedin-openid","accessToken":"AQXdSP8qjRnVVIPq..."}
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
     *             @OA\Property(property="is_first_time", type="boolean", example=true, description="True when the account still needs basic profile data: country, job title, or phone number is missing."),
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
            'idToken' => 'nullable|string|max:8192',
            'accessToken' => 'nullable|string|max:8192',
        ]);

        $canonical = OAuthAccountSecurity::canonicalOAuthProvider($request->provider);
        $verifier = app(\App\Support\Social\SocialTokenVerifierInterface::class);
        $linker = app(\App\Services\SocialAccountLinker::class);

        try {
            $identity = $verifier->verify(
                $canonical,
                $request->input('idToken'),
                $request->input('accessToken')
            );
        } catch (\App\Support\Social\SocialTokenVerificationException $e) {
            AuthEventLogger::log($request, 'auth.social_login', 'token_rejected', [
                'provider' => $canonical,
                'reason' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'The provider credential could not be verified.'], 401);
        }

        try {
            $user = $linker->resolveUser($identity);
        } catch (\App\Support\Social\SocialLoginDeniedException $e) {
            AuthEventLogger::log($request, 'auth.social_login', 'denied', [
                'provider' => $canonical,
                'email' => $identity->email,
                'reason' => $e->getMessage(),
            ]);

            return response()->json(['message' => $e->getMessage()], $e->status);
        }

        $wasCreated = $user->wasRecentlyCreated || $user->created_at?->gt(Carbon::now()->subMinute());

        if (! $user->email_verified_at) {
            $user->email_verified_at = Carbon::now();
        }
        $user->is_verified = 1;
        if (! $user->is_social_login) {
            // Linked password account keeps its password; it simply gains social sign-in.
            $user->is_social_login = 1;
        }
        if (empty($user->social_provider)) {
            $user->social_provider = $canonical;
        }
        $user->save();

        Auth::login($user);
        $user->load('communities');
        $user->load('preferences');
        $tokenResult = $user->createToken('Personal Access Token');
        updateUserPushToken($request, $user);

        AuthEventLogger::log($request, 'auth.social_login', 'success', [
            'provider' => $canonical,
            'user_id' => $user->id,
            'email' => $identity->email,
        ]);
        if ($wasCreated) {
            AuthEventLogger::trackAccountCreation($request, $user);
        }

        return response()->json([
            'token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => $tokenResult->token->expires_at,
            'is_first_time' => $this->socialLoginNeedsProfileCompletion($user),
            'user' => $user,
        ]);
    }

    /**
     * True when social-login user should be guided through profile completion
     * (missing country, job title, or phone number).
     */
    private function socialLoginNeedsProfileCompletion(User $user): bool
    {
        if ($user->country_id === null || $user->country_id === '' || (int) $user->country_id === 0) {
            return true;
        }
        if (trim((string) ($user->phone_number ?? '')) === '') {
            return true;
        }
        if (trim((string) ($user->job_title ?? '')) === '') {
            return true;
        }

        return false;
    }

    /**
     * Decode JSON array strings for multipart clients (registration and profile update); normalize job_missing to boolean (matches web checkbox semantics).
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
