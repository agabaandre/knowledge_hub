<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\ExpertsApiController;
use App\Http\Controllers\Api\LookupApiController;
use App\Http\Controllers\Api\MembersApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PublicationsApiController;
use App\Http\Controllers\ExpertsController;
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


Route::group(["prefix" =>"auth"],function(){
    Route::post('/get_token', [AuthApiController::class,"login"]);
    Route::post('/refresh_token', [AuthApiController::class,"refresh"]);
});

Route::group(['middleware' => 'api'],function(){

Route::apiResource("publications",PublicationsApiController::class);

Route::group(["prefix" =>"members"],function(){
    Route::get('/', [MembersApiController::class,"member_states"]);
});

Route::apiResource("experts",ExpertsApiController::class);

Route::group(["prefix" =>"lookup"],function(){
    Route::get('/filetypes', [LookupApiController::class,"file_types"]);
});

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

