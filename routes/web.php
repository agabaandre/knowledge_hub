<?php

use App\Http\Controllers\AssetsController;
use App\Http\Controllers\AuthorsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicationsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [HomeController::class,'index']);

Route::group(["prefix"=>"records"],function(){
    Route::get("show",[PublicationsController::class,'show']);
    Route::get("search",[PublicationsController::class,'search']);
    Route::get("subtheme",[PublicationsController::class,'subtheme_pubs']);
    Route::get("autocomplete",[PublicationsController::class,'autocomplete']);
});

Route::group(["prefix"=>"authors"],function(){
    Route::get("/",[AuthorsController::class,'index']);
    Route::get("publications",[PublicationsController::class,'author_pubs']);
});

Route::group(["prefix"=>"healthassets"],function(){
    Route::get("/",[AssetsController::class,'index']);
});

