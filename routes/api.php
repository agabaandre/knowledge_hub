<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PublicationsApiController;

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

//Route::resource('publications', PublicationsApiController::class);

Route::group(["prefix" =>"publications"],function(){

    Route::resource('/', PublicationsApiController::class);
    Route::get('/{id}', [PublicationsApiController::class,"show"]);
    Route::get('/filetypes', [PublicationsApiController::class,"file_types"]);

});

