<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\ExpertsApiController;
use App\Http\Controllers\Api\ForumsApiController;
use App\Http\Controllers\Api\LookupApiController;
use App\Http\Controllers\Api\MembersApiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PublicationsApiController;
use App\Http\Controllers\TestController;

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
///,


Route::post('login', [AuthApiController::class, 'login']);
Route::post('register', [AuthApiController::class, 'register']);

Route::group(['middleware' => 'auth:api'],function(){
    Route::post('/refresh_token', [AuthApiController::class,"refresh"]);
    Route::get('/logout', [AuthApiController::class,"logout"]);
 });

Route::group(["prefix" =>"members"],function(){
    Route::get('/', [MembersApiController::class,"member_states"]);
});


Route::get("publications",[PublicationsApiController::class,"index"])->middleware('auth.passport');;
Route::get("publications/{id}",[PublicationsApiController::class,"show"])->where('id', '[0-9]+');;

Route::group(['middleware' => 'auth:api','prefix'=>"publications"],function(){
    Route::post("/",[PublicationsApiController::class,"store"]);
    Route::get("/published",[PublicationsApiController::class,"my_publications"]);
    Route::post("/comment",[PublicationsApiController::class,"comment"]);
    Route::get("/favourites",[PublicationsApiController::class,"favourites"]);
    Route::get("/add_favourite",[PublicationsApiController::class,"add_favourite"]);
});

Route::get("forums",[ForumsApiController::class,"index"]);
Route::get("forums/{id}",[ForumsApiController::class,"show"]);
Route::group(['middleware' => 'auth:api'],function(){
    Route::post("forums",[ForumsApiController::class,"store"]);
    Route::get("forums/comment",[ForumsApiController::class,"comment"]);
});

Route::get("experts",[ExpertsApiController::class,'index']);

Route::group(["prefix" =>"lookup"],function(){
    Route::get('/resource-types', [LookupApiController::class,"resource_types"]);
    Route::get('/themes', [LookupApiController::class,"themes"]);
    Route::get('/sub_themes', [LookupApiController::class,"sub_themes"]);
    Route::get('/jobs', [LookupApiController::class,"jobs"]);
    Route::get('/preferences', [LookupApiController::class,"preferences"]);
    Route::get('/communities', [LookupApiController::class,"communities"]);
    Route::get('/file-categories', [LookupApiController::class,"file_categories"]);
    Route::get('/authors', [LookupApiController::class,"authors"]);
    Route::get('/resource-categories', [LookupApiController::class,"resource_categories"]);
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

Route::get('/test',[TestController::class,"index"]);

