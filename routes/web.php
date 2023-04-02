<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AssetsController;
use App\Http\Controllers\AuthorsController;
use App\Http\Controllers\ExpertsController;
use App\Http\Controllers\FaqsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicationsController;
use Illuminate\Support\Facades\Auth;
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


Auth::routes();

Route::get('/logout',function(){
    Auth::logout();
    redirect()->route('home');
});


Route::get('/', [HomeController::class,'index'])->name('home');


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

Route::group(["prefix"=>"faqs"],function(){
    Route::get("/",[FaqsController::class,'index']);
});

Route::group(["prefix"=>"experts"],function(){
    Route::get("/",[ExpertsController::class,'index']);
});

Route::group(["prefix"=>"publications"],function(){
    Route::get("/",[PublicationsController::class,'index']);
    Route::get("/add_favourite",[PublicationsController::class,'add_favourite']);
    Route::get("/remove_favourite",[PublicationsController::class,'remove_favourite']);
});

Route::group(["prefix"=>"account"],function(){

    Route::get("/",[AccountController::class,'profile'])->name('account.profile');
    Route::get("/favourites",[AccountController::class,'favourites'])->name('account.favourites');
    Route::get("/publish",[AccountController::class,'publish'])->name('account.publish');
    Route::get("/publications",[AccountController::class,'publications'])->name('account.publications');
    Route::get("publications/delete",[AccountController::class,'remove_favourite'])->name('account.pub_delete');
    Route::post("/publication",[AccountController::class,'submit_publication'])->name('account.publication');
    Route::get("/newversion",[AccountController::class,'create_version'])->name('account.newversion');

});

