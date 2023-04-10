<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\AssetsController;
use App\Http\Controllers\AuthorsController;
use App\Http\Controllers\ExpertsController;
use App\Http\Controllers\FaqsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicationsController;
use App\Http\Controllers\Admin\ResourcesController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\ForumsController;
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


Route::get('/', [HomeController::class,'index'])->name('home');

Route::get('/logout',function(){
    Auth::logout();
    return redirect()->route('home');
});


Route::get('/privacy', [CommonController::class,'privacy'])->name('privacy');


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

Route::group(["prefix"=>"account",'middleware'=>['auth','web']],function(){

    Route::get("/",[AccountController::class,'profile'])->name('account.profile');
    Route::get("/favourites",[AccountController::class,'favourites'])->name('account.favourites');
    Route::get("/publish",[AccountController::class,'publish'])->name('account.publish');
    Route::get("/publications",[AccountController::class,'publications'])->name('account.publications');
    Route::get("publications/delete",[AccountController::class,'remove_favourite'])->name('account.pub_delete');
    Route::post("/publication",[AccountController::class,'submit_publication'])->name('account.publication');
    Route::get("/newversion",[AccountController::class,'create_version'])->name('account.newversion');

});

Route::group(["prefix"=>"admin",'middleware'=>['auth','web']],function(){

    Route::get("/",[AdminController::class,'index'])->name('admin.index');

    Route::group(["prefix"=>"publications"],function(){
        
        Route::get("/",[ResourcesController::class,'index']);
        Route::get("/create",[ResourcesController::class,'create']);
        Route::get("/edit",[ResourcesController::class,'edit']);
        Route::post("/save",[ResourcesController::class,'store']);
        Route::get("/moderate",[ResourcesController::class,'moderate']);
        Route::get("/delete",[ResourcesController::class,'destroy']);
    });

});



//permissions and access control
Route::group(['prefix' => 'permissions','middleware'=>['auth','web']], function() {

    Route::get('/',  [PermissionController::class,'permissions'])->name('permissions.permissions');
    Route::get('/roles',  [PermissionController::class,'index'])->name('permissions.roles');
    Route::post('/role',  [PermissionController::class,'saveRole'])->name('permissions.role');
    Route::post('/permission',  [PermissionController::class,'createPermission'])->name('permissions.permission');
    Route::post('/torole',  [PermissionController::class,'permissionsToRole'])->name('permissions.torole');
    Route::get('/users',  [PermissionController::class,'users'])->name('permissions.users');
    Route::post('/user',  [PermissionController::class,'users'])->name('permissions.filerusers');
    Route::post('/saveuser',  [PermissionController::class,'saveUser'])->name('permissions.saveuser');
    Route::post('/userrole',  [PermissionController::class,'roleToUser'])->name('permissions.userrole');
    
    Route::get('/changepass',  [PermissionController::class,'changePassword'])->name('permissions.changepass');
    Route::post('/reset',  [PermissionController::class,'resetUser'])->name('permissions.reset');

    Route::post('/delete',  [PermissionController::class,'deleteUser'])->name('permissions.delete');
    Route::any('/trail',  [PermissionController::class,'trail'])->name('permissions.trail');
    
});


Route::group(["prefix"=>"forums"],function(){
    Route::get("/",[ForumsController::class,'index'])->name('forums.index');
    Route::get("/thread",[ForumsController::class,'thread'])->name('forums.thread');
});


