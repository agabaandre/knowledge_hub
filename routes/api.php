<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\ExpertsApiController;
use App\Http\Controllers\Api\ForumsApiController;
use App\Http\Controllers\Api\LookupApiController;
use App\Http\Controllers\Api\MembersApiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PublicationsApiController;
use App\Http\Controllers\Api\AIApiController;
use App\Http\Controllers\Api\AssistantApiController;
use App\Http\Controllers\Api\EventsApiController; // Use the new Event API Controller
use App\Http\Controllers\Api\CommunitiesApiController;
use App\Http\Controllers\Api\PushNotificationsApiController;
use App\Http\Controllers\Api\CoursesApiController;
use App\Http\Controllers\Api\HomeApiController;
use App\Http\Controllers\Api\HealthTopicsApiController;
use App\Http\Controllers\Api\MeApiController;
use App\Http\Controllers\Api\CountriesApiController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Publications Routes

Route::post('login', [AuthApiController::class, 'login']);
Route::post('register', [AuthApiController::class, 'register']);
Route::post('/forgot-password', [AuthApiController::class, 'forgotPassword']);
Route::get('/refresh', [AuthApiController::class, 'refresh']);
Route::post('/social-login', [AuthApiController::class, 'socialLogin']);


Route::group(['middleware' => 'auth:api'],function(){
    Route::post('/profile/update', [AuthApiController::class, 'updateProfile']);
    Route::post('/change-password', [AuthApiController::class, 'changePassword']);
    Route::get('/logout', [AuthApiController::class, 'logout']);
    Route::get('/profile', [AuthApiController::class, 'profile']);
    Route::get('/me', [MeApiController::class, 'library']);
    Route::get('/me/chats', [MeApiController::class, 'chats']);
    Route::get('/me/forums', [MeApiController::class, 'forums']);
 });

Route::group(["prefix" =>"members"],function(){
    Route::get('/', [MembersApiController::class,"member_states"]);
});

Route::get('publications/sections/recommended', [PublicationsApiController::class, 'sectionRecommended'])->middleware('auth.passport');
Route::get('publications/sections/top-searches', [PublicationsApiController::class, 'sectionTopSearches'])->middleware('auth.passport');
Route::get('publications/sections/flagship-initiatives', [PublicationsApiController::class, 'sectionFlagshipInitiatives'])->middleware('auth.passport');
Route::get("publications",[PublicationsApiController::class,"index"])->middleware('auth.passport');;
Route::get("publications/{id}",[PublicationsApiController::class,"show"])->where('id', '[0-9]+');;
Route::get('home', [HomeApiController::class, 'index'])->middleware('auth.passport');

Route::get('health-topics', [HealthTopicsApiController::class, 'index']);
Route::get('health-topics/{id}', [HealthTopicsApiController::class, 'showTopic'])->whereNumber('id');
Route::get('health-emergencies', [HealthTopicsApiController::class, 'emergenciesIndex']);
Route::get('health-emergencies/{id}', [HealthTopicsApiController::class, 'showEmergency'])->whereNumber('id');

Route::get('countries', [CountriesApiController::class, 'index']);

Route::group(['middleware' => 'auth:api','prefix'=>"publications"],function(){
    Route::get('/published', [PublicationsApiController::class, 'my_publications']);
    Route::get('/favourites', [PublicationsApiController::class, 'favourites']);
    Route::match(['get', 'post'], '/add_favourite', [PublicationsApiController::class, 'add_favourite']);
    Route::match(['get', 'post'], '/remove_favourite', [PublicationsApiController::class, 'remove_favourite']);
    Route::post('/favourite', [PublicationsApiController::class, 'add_favourite']);
    Route::delete('/favourite/{publicationId}', [PublicationsApiController::class, 'remove_favourite'])->whereNumber('publicationId');
    Route::post('/like/{publicationId}', [PublicationsApiController::class, 'add_favourite'])->whereNumber('publicationId');
    Route::post('/unlike/{publicationId}', [PublicationsApiController::class, 'remove_favourite'])->whereNumber('publicationId');
    Route::post('/comment', [PublicationsApiController::class, 'comment']);
    Route::post('/content-request', [PublicationsApiController::class, 'content_request']);
    Route::post('/', [PublicationsApiController::class, 'store']);
    Route::post('/{id}', [PublicationsApiController::class, 'update'])->whereNumber('id');
});

Route::get("forums",[ForumsApiController::class,"index"]);
Route::get('forums/me', [ForumsApiController::class, 'myForums'])->middleware('auth:api');
Route::get("forums/{id}",[ForumsApiController::class,"show"])->whereNumber('id');
Route::group(['middleware' => 'auth:api'],function(){
    Route::post("forums/comment/like",[ForumsApiController::class,"likeComment"]);
    Route::post("forums/comment",[ForumsApiController::class,"comment"]);
    Route::post("forums/{id}/join",[ForumsApiController::class,"join"])->whereNumber('id');
    Route::post("forums/{id}/like",[ForumsApiController::class,"like"])->whereNumber('id');
    Route::post("forums",[ForumsApiController::class,"store"]);
});

Route::get("experts",[ExpertsApiController::class,'index']);

Route::group(["prefix" =>"lookup"],function(){
    Route::get('/resource-types', [LookupApiController::class,"resource_types"]);
    Route::get('/themes', [LookupApiController::class,"themes"]);
    Route::get('/regions', [LookupApiController::class,"regions"]);
    Route::get('/sub_themes', [LookupApiController::class,"sub_themes"]);
    Route::get('/jobs', [LookupApiController::class,"jobs"]);
    Route::get('/preferences', [LookupApiController::class,"preferences"]);
    Route::get('/communities', [LookupApiController::class,"communities"]);
    Route::get('/file-categories', [LookupApiController::class,"file_categories"]);
    Route::get('/authors', [LookupApiController::class,"authors"]);
    Route::get('/resource-categories', [LookupApiController::class,"resource_categories"]);
    Route::get('/licenses', [LookupApiController::class, 'licenses']);
    Route::get('/static-links', [LookupApiController::class, 'staticLinks']);
    Route::get('/settings', [LookupApiController::class,"settings"]);
});

Route::get('/log',function(){

$userIp = "45.56.197.35";// $_SERVER['REMOTE_ADDR']; // Get the user's IP address
$apiUrl = "http://ipinfo.io/{$userIp}/json"; // Construct the query URL

    // Use file_get_contents to fetch the data
    $response = file_get_contents($apiUrl);
    $geoData = json_decode($response, true); // Decode the JSON response

    echo explode('/',$geoData['timezone'])[0];

    die(json_encode( $geoData));

    if (!empty($geoData['country'])) {
        echo "Country: " . $geoData['country']; // Print the user's country
    } else {
        echo "Country could not be determined.";
    }


    if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        $langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        $primaryLang = explode('-', $langs[0]);

        if (!empty($primaryLang[0])) {
            echo "Accept-Language " . $primaryLang[0];
        } else {
            echo "Could not determine country from Accept-Language header.";
        }
    } else {
        echo "Accept-Language header not set.";
    }

});


Route::group(['prefix' => 'ai', 'middleware' => 'auth:api'], function () {
    Route::post('/summarise', [AIApiController::class, 'summarise']);
    Route::post('/summarise-file', [AIApiController::class, 'summariseFile']);
    Route::post('/chat', [AssistantApiController::class, 'chat']);
    Route::post('/assistant/session', [AssistantApiController::class, 'session']);
    Route::post('/assistant/message', [AssistantApiController::class, 'message']);
});

Route::prefix('events')->group(function () {
    Route::get('/', [EventsApiController::class, 'index'])->name('events.index');
    Route::get('/{id}', [EventsApiController::class, 'show'])->name('events.show');

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('/', [EventsApiController::class, 'store'])->name('events.store');
});

});

Route::prefix('communities')->group(function () {
    Route::get('/', [CommunitiesApiController::class, 'index'])->name('communities.index');

    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('/me', [CommunitiesApiController::class, 'myCommunities'])->name('communities.me');
        Route::get('/invitations/accept/{token}', [CommunitiesApiController::class, 'acceptInvitation'])->name('communities.invitations.accept');

        Route::post('/{id}/join', [CommunitiesApiController::class, 'join'])->where('id', '[0-9]+')->name('communities.join');
        Route::post('/{id}/leave', [CommunitiesApiController::class, 'leave'])->where('id', '[0-9]+')->name('communities.leave');
        Route::get('/{id}/members', [CommunitiesApiController::class, 'members'])->where('id', '[0-9]+')->name('communities.members');
        Route::get('/{id}/publications', [CommunitiesApiController::class, 'publications'])->where('id', '[0-9]+')->name('communities.publications');
        Route::get('/{id}/forums', [CommunitiesApiController::class, 'forums'])->where('id', '[0-9]+')->name('communities.forums');
        Route::get('/{id}/events', [CommunitiesApiController::class, 'events'])->where('id', '[0-9]+')->name('communities.events.index');
        Route::post('/{id}/events', [CommunitiesApiController::class, 'storeCommunityEvent'])->where('id', '[0-9]+')->name('communities.events.store');
        Route::post('/{id}/invites', [CommunitiesApiController::class, 'inviteColleagues'])->where('id', '[0-9]+')->name('communities.invites');
        Route::post('/{id}/member-status', [CommunitiesApiController::class, 'updateMemberStatus'])->where('id', '[0-9]+')->name('communities.member-status');

        Route::post('/{communityId}/members', [CommunitiesApiController::class, 'addMember'])->where('communityId', '[0-9]+')->name('communities.addMember');

        Route::get('/{id}', [CommunitiesApiController::class, 'show'])->where('id', '[0-9]+')->name('communities.show');
    });
});


Route::prefix('push-notifications')->group(function() {
    
    Route::get('/', [PushNotificationsApiController::class, 'index']);
    
    Route::group(['middleware' => 'auth:api'], function() {
        Route::post('/mark-as-read', [PushNotificationsApiController::class, 'markAsRead']);
        Route::get('/unread-count', [PushNotificationsApiController::class, 'getUnreadCount']);
        Route::get('/user', [PushNotificationsApiController::class, 'getByUser'])->middleware('auth:api');
    });
});

Route::prefix('courses')->group(function () {
    Route::get('/', [CoursesApiController::class, 'index']);
    Route::get('/{id}', [CoursesApiController::class, 'show']);
});
