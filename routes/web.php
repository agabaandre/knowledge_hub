<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\AccessGroupsController;
use App\Http\Controllers\Admin\AdminUnitsController;
use App\Http\Controllers\Admin\AuthorsAdminController;
use App\Http\Controllers\Admin\CommsOfPracticeController;
use App\Http\Controllers\Admin\DataRecordsAdminController;
use App\Http\Controllers\Admin\ExpertsAdminController;
use App\Http\Controllers\Admin\FactsAdminController;
use App\Http\Controllers\Admin\FaqsAdminController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\AssetsController;
use App\Http\Controllers\AuthorsController;
use App\Http\Controllers\ExpertsController;
use App\Http\Controllers\FaqsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicationsController;
use App\Http\Controllers\Admin\ResourcesController;
use App\Http\Controllers\AreasController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\ForumsController;
use App\Http\Controllers\ThemesController;

use App\Http\Controllers\Admin\GeoAreasController;
use App\Http\Controllers\Admin\FileTypesController;
use App\Http\Controllers\Admin\ForumsAdminController;
use App\Http\Controllers\Admin\HealthThemesController;
use App\Http\Controllers\Admin\LogsController;
use App\Http\Controllers\Admin\MetricsController;
use App\Http\Controllers\Admin\PrivacyAdminController;
use App\Http\Controllers\Admin\QuizController;
use App\Http\Controllers\Admin\QuotesController;
use App\Http\Controllers\Admin\SubHealthThemesController;
use App\Http\Controllers\Admin\TagsController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ToolsAdminController;
use App\Http\Controllers\AdminUnitFrontEndController;
use App\Http\Controllers\DataRecordsController;
use App\Http\Controllers\FactsController;
use App\Http\Controllers\GraphController;
use App\Http\Controllers\KpiController;
use App\Http\Controllers\QuestionsController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\CountriesController;
use App\Http\Controllers\ToolsController;

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


Auth::routes(['verify' => true]);

Route::get('/test', [TestController::class, 'index'])->name('test');

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/endtour', [CommonController::class, 'endtour'])->name('endtour');

Route::get('/logout', function () {
    Auth::logout();
    clear_cache();
    return redirect()->route('home');
});


if(states_enabled()):

    Route::group(["prefix" => "countries"], function () {

            Route::get('/', [CountriesController::class, 'index'])->name('countries');
            Route::get('/details', [CountriesController::class, 'country']);

    });

else:

    Route::group(["prefix" => "adminunits"], function () {

        Route::get('/', [AdminUnitFrontEndController::class, 'index'])->name('adminunits');
        Route::get('/details', [AdminUnitFrontEndController::class, 'show']);

    });

endif;


Route::post('/registration', [AuthController::class, 'register'])->name('registration');

Route::get('/privacy', [CommonController::class, 'privacy'])->name('privacy');

Route::group(["prefix" => "browse"], function () {

    Route::get("themes", [ThemesController::class, 'index']);

    Route::get("subthemes", [ThemesController::class, 'subthemes']);
    Route::put('subthemes/update', [ThemesController::class, 'subthemes/update'])->name('subthemes.update');

    Route::get("authors", [AuthorsController::class, 'index']);
    Route::get("areas", [AreasController::class, "index"]);

});

Route::group(["prefix" => "records"], function () {

    Route::get("/", [PublicationsController::class, 'search']);
    Route::get("/resource", [PublicationsController::class, 'show']);
    Route::get("/search", [PublicationsController::class, 'search']);
    Route::get("/subtheme", [PublicationsController::class, 'subtheme_pubs']);
    Route::get("/autocomplete", [PublicationsController::class, 'autocomplete']);
    Route::get("/shortened", [PublicationsController::class, 'shortened']);
    Route::post("/comment", [PublicationsController::class, 'comment']);

});

Route::group(["prefix" => "authors"], function () {

    Route::get("/", [AuthorsController::class, 'index']);
    Route::get("publications", [PublicationsController::class, 'author_pubs']);
});

Route::group(["prefix" => "healthassets"], function () {

    Route::get("/", [AssetsController::class, 'index']);
    Route::get("/detail", [AssetsController::class, 'details']);
});

Route::group(["prefix" => "faqs"], function () {

    Route::get("/", [FaqsController::class, 'index']);
});


Route::group(["prefix" => "publications"], function () {

    Route::get("/", [PublicationsController::class, 'index']);
    Route::get("/add_favourite", [PublicationsController::class, 'add_favourite']);
    Route::get("/remove_favourite", [PublicationsController::class, 'remove_favourite']);

});
Route::get("/tools", [ToolsController::class, 'index', 'middleware' => ['auth', 'web']])->name('tools');
Route::get("/verify", [AccountController::class, 'verifyAccount'])->name('account_verify');

Route::group(["prefix" => "account", 'middleware' => ['auth', 'web']], function () {

    Route::get("/", [AccountController::class, 'profile'])->name('account.profile');
    Route::get("/favourites", [AccountController::class, 'favourites'])->name('account.favourites');
    Route::get("/publish", [AccountController::class, 'publish'])->name('account.publish');
    Route::get("/publications", [AccountController::class, 'publications'])->name('account.publications');
    Route::get("/publications/delete", [AccountController::class, 'delete_publication'])->name('account.pub_delete');
    Route::get("/publications/favdelete", [AccountController::class, 'remove_favourite'])->name('account.fav_delete');
    Route::get("/publications/edit", [AccountController::class, 'edit_publication'])->name('account.publications.edit');
    Route::post("/publication", [AccountController::class, 'submit_publication'])->name('account.publication');
    Route::get("/newversion", [AccountController::class, 'create_version'])->name('account.newversion');
    Route::post("/summary", [AccountController::class, 'submit_summary'])->name('account.summary');
    Route::get("/summarize", [AccountController::class, 'create_summary'])->name('account.summarize');
    Route::post("/update", [AuthController::class, 'update_profile'])->name('account.update');
    Route::post("/secureme", [AuthController::class, 'update_password'])->name('account.auth_update');

});


Route::group(["prefix" => "admin", 'middleware' => ['auth', 'web']], function () {

    Route::get("/", [AdminController::class, 'index'])->name('admin.index');

    if(states_enabled())
    Route::get("/rccdashboards", [GraphController::class, 'rcc_admin'])->name('admin.rccdashboards');

    Route::get("/configure", [SettingsController::class, 'index'])->name('admin.configure');
    Route::get("/configure", [SettingsController::class, 'index'])->name('admin.configure');
    Route::post("/configure", [SettingsController::class, 'store'])->name('admin.config.save');

    Route::group(["prefix" => "publications"], function () {

        Route::get("/", [ResourcesController::class, 'index']);
        Route::get("/create", [ResourcesController::class, 'create']);
        Route::get("/edit", [ResourcesController::class, 'edit']);
        Route::get("/details", [ResourcesController::class, 'details']);
        Route::post("/save", [ResourcesController::class, 'store']);
        Route::post("/approval", [ResourcesController::class, 'approval']);
        Route::get("/moderate", [ResourcesController::class, 'moderate']);
        Route::get("/delete", [ResourcesController::class, 'destroy']);
        Route::get("/approve_comment", [ResourcesController::class, 'approve_comment']);
        Route::get("/reject_comment", [ResourcesController::class, 'reject_comment']);
        Route::get("/summaries", [ResourcesController::class, 'summaries']);
        Route::get("/summary", [ResourcesController::class, 'summary']);
        Route::post("/summary_approval", [ResourcesController::class, 'summary_approval']);
    });

    //geo areas
    Route::group(["prefix" => "areas", "as" => "areas."], function () {

        Route::get("/", [GeoAreasController::class, 'index']);
        Route::post("/save", [GeoAreasController::class, 'store'])->name('store');
        Route::get("/delete", [GeoAreasController::class, 'destroy']);
    });


    //filetypes
    Route::group(["prefix" => "filetypes", "as"=> "filetypes."], function () {

        Route::get("/", [FileTypesController::class, 'index']);
        Route::post("/save", [FileTypesController::class, 'store'])->name('store');
        Route::get("/delete", [FileTypesController::class, 'destroy']);
    });


    //themes
    Route::group(["prefix" => "themes"], function () {

        Route::get("/", [HealthThemesController::class, 'index']);
        Route::post("/save", [HealthThemesController::class, 'store']);
        Route::get("/delete", [HealthThemesController::class, 'destroy']);
    });

    //subthemes
    Route::group(["prefix" => "subthemes"], function () {

        Route::get("/", [SubHealthThemesController::class, 'index']);
        Route::post("/save", [SubHealthThemesController::class, 'store']);
        Route::post("/edit", [SubHealthThemesController::class, 'edit']);
        Route::get("/delete", [SubHealthThemesController::class, 'destroy']);
    });


    //tags
    Route::group(["prefix" => "tags", 'as' => 'tags.'], function () {

        Route::get("/", [TagsController::class, 'index']);
        Route::post("/save", [TagsController::class, 'store']);
        Route::put('/update', [TagsController::class, 'update'])->name('update');
        Route::get("/delete", [TagsController::class, 'destroy']);
    });

    //quiz
    Route::group(["prefix" => "quiz"], function () {

        Route::get("/", [QuizController::class, 'index']);
        Route::post("/save", [QuizController::class, 'store']);
        Route::get("/delete", [QuizController::class, 'destroy']);
        Route::get("/answers", [QuizController::class, 'answers']);
        Route::post("/save_answer", [QuizController::class, 'save_answer']);
        Route::get("/answer/delete", [QuizController::class, 'delete_answer']);
    });

    //filetypes

    Route::group(["prefix"=>"forums"],function(){

        Route::get("/",[ForumsAdminController::class,'index']);
        Route::get("/delete",[ForumsAdminController::class,'destroy']);
        Route::get("/moderate",[ForumsAdminController::class,'moderation']);
        Route::post("/approve",[ForumsAdminController::class,'approve']);
        Route::post("/reject",[ForumsAdminController::class,'reject']);
        Route::get("/details", [ForumsAdminController::class, 'details']);

    });

    //authors
    Route::group(["prefix" => "authors"], function () {

        Route::any("/", [AuthorsAdminController::class, 'index']);
        Route::get("/delete", [AuthorsAdminController::class, 'destroy']);
    });

    //authors
    Route::group(["prefix" => "kpi"], function () {

        Route::get("/", [KpiController::class, 'index']);
        Route::get("/data", [KpiController::class, 'data']);
        Route::post("/save", [KpiController::class, 'save']);
        Route::post("/save_data", [KpiController::class, 'save_data']);
        Route::get("/delete", [KpiController::class, 'destroy']);
    });

    //quotes
    Route::group(["prefix" => "quotes"], function () {

        Route::get("/", [QuotesController::class, 'index']);
        Route::post("/save", [QuotesController::class, 'store']);
        Route::get("/delete", [QuotesController::class, 'destroy']);
    });

    //privacy
    Route::group(["prefix" => "privacy"], function () {

        Route::get("/", [PrivacyAdminController::class, 'index']);
        Route::post("/save", [PrivacyAdminController::class, 'store']);
    });

    //facts
    Route::group(["prefix" => "facts", 'as' => 'facts.'], function () {

        Route::get("/", [FactsAdminController::class, 'index']);
        Route::post("/save", [FactsAdminController::class, 'store'])->name('store');
        Route::get("/delete", [FactsAdminController::class, 'destroy']);
    });

    //faq
    Route::group(["prefix" => "faqs"], function () {

        Route::get("/", [FaqsAdminController::class, 'index']);
        Route::post("/save", [FaqsAdminController::class, 'store']);
        Route::get("/delete", [FaqsAdminController::class, 'destroy']);
    });


    //experts
    Route::group(["prefix" => "experts"], function () {

        Route::get("/", [ExpertsAdminController::class, 'index']);
        Route::post("/save", [ExpertsAdminController::class, 'store']);
        Route::get("/delete", [ExpertsAdminController::class, 'destroy']);

        Route::group(["prefix" => "types"], function () {

            Route::get("/", [ExpertsAdminController::class, 'types']);
            Route::post("/save", [ExpertsAdminController::class, 'save_type']);
            Route::get("/delete", [ExpertsAdminController::class, 'delete_type']);

        });

    });


    //accessgroup
    Route::group(["prefix" => "accessgroups"], function () {

        Route::get("/", [AccessGroupsController::class, 'index']);
        Route::post("/save", [AccessGroupsController::class, 'store']);
        Route::get("/delete", [AccessGroupsController::class, 'destroy']);
    });

    //commsofpractice
    Route::group(["prefix" => "commsofpractice"], function () {

        Route::any("/", [CommsOfPracticeController::class, 'index']);
        Route::post("/save", [CommsOfPracticeController::class, 'store']);
        Route::get("/delete", [CommsOfPracticeController::class, 'destroy']);
    });

    //AdminUnits
       Route::group(["prefix" => "adminunits"], function () {

        Route::get("/", [AdminUnitsController::class, 'index']);
        Route::post("/save", [AdminUnitsController::class, 'store']);
        Route::get("/delete", [AdminUnitsController::class, 'destroy']);
    });


    //data records
    Route::group(["prefix" => "datarecords"], function () {

        Route::get("/", [DataRecordsAdminController::class, 'index']);
        Route::get("/create", [DataRecordsAdminController::class, 'create']);
        Route::post("/save", [DataRecordsAdminController::class, 'store']);
        Route::get("/edit", [DataRecordsAdminController::class, 'edit']);
        Route::get("/delete", [DataRecordsAdminController::class, 'destroy']);


        Route::group(["prefix" => "categories"], function () {

            Route::get("/", [DataRecordsAdminController::class, 'categories']);
            Route::post("/save", [DataRecordsAdminController::class, 'save_category']);
            Route::get("/delete", [DataRecordsAdminController::class, 'delete_category']);

            Route::get("/subcategories", [DataRecordsAdminController::class, 'subcategories']);
            Route::post("/savesub", [DataRecordsAdminController::class, 'save_subcategory']);

            Route::get('/ajax/subcategories', [DataRecordsAdminController::class, 'getSubcategories'])->name('get-subcategories');
        });
    });



    Route::group(["prefix" => "logs"], function () {

        Route::get("/access", [LogsController::class, 'index']);
        Route::get("/user", [LogsController::class, 'trail']);
    });

    Route::group(["prefix" => "metrics"], function () {

        Route::get("/", [MetricsController::class, 'index']);
    });

    Route::group(["prefix" => "tools"], function () {

        Route::any("/", [ToolsAdminController::class, 'index']);
    });
});

/////






//permissions and access control
Route::group(['prefix' => 'permissions', 'middleware' => ['auth', 'web']], function () {

    Route::get('/',  [PermissionController::class, 'permissions'])->name('permissions.permissions');
    Route::get('/roles',  [PermissionController::class, 'index'])->name('permissions.roles');
    Route::post('/role',  [PermissionController::class, 'saveRole'])->name('permissions.role');
    Route::post('/permission',  [PermissionController::class, 'createPermission'])->name('permissions.permission');
    Route::post('/torole',  [PermissionController::class, 'permissionsToRole'])->name('permissions.torole');
    Route::get('/users',  [PermissionController::class, 'users'])->name('permissions.users');
    Route::post('/user',  [PermissionController::class, 'users'])->name('permissions.filerusers');
    Route::post('/saveuser',  [PermissionController::class, 'saveUser'])->name('permissions.saveuser');
    Route::post('/userrole',  [PermissionController::class, 'roleToUser'])->name('permissions.userrole');

    Route::get('/changepass',  [PermissionController::class, 'changePassword'])->name('permissions.changepass');
    Route::post('/reset',  [PermissionController::class, 'resetUser'])->name('permissions.reset');

    Route::post('/delete',  [PermissionController::class, 'deleteUser'])->name('permissions.delete');
    Route::any('/trail',  [PermissionController::class, 'trail'])->name('permissions.trail');
    Route::any('/profile',  [PermissionController::class, 'profile'])->name('permissions.profile');
});


Route::group(["prefix" => "forums"], function () {

    Route::get("/", [ForumsController::class, 'index'])->name('forums.index');
    Route::get("/create", [ForumsController::class, 'create'])->name('forums.create');
    Route::get("/thread", [ForumsController::class, 'thread'])->name('forums.thread');
    Route::get("/join", [ForumsController::class, 'join'])->name('forums.join');
    Route::post("/comment", [ForumsController::class, 'comment'])->name('forums.comment');
    Route::post("/publish", [ForumsController::class, 'publish'])->name('forums.publish');

});

//facts
Route::group(["prefix" => "facts"], function () {

    Route::get("/fact", [FactsController::class, 'details']);
});

Route::group(["prefix" => "quiz"], function () {

    Route::post("/save_stat", [QuestionsController::class, 'save_stats'])->name('quiz.savestat');
});

Route::group(["prefix" => "categories"], function () {

    Route::get("/workforce",  [ExpertsController::class, 'index']);
    Route::get("/phassets",   [AssetsController::class, 'index']);
    Route::get("/inititaives", [DataRecordsController::class, 'index']);
    Route::get("/research_dev", [DataRecordsController::class, 'index']);
    Route::get("/data_stats",  [DataRecordsController::class, 'index']);
    Route::get("/healthindicators",  [DataRecordsController::class, 'index']);

    Route::get("data/detail",  [DataRecordsController::class, 'details']);
});

Route::group(["prefix" => "dashboards"], function () {

    Route::get("/",  [GraphController::class, 'index']);
    Route::get("/kpi",  [GraphController::class, 'kpi_comparison']);
    Route::get("/kpi_comparison_data",  [GraphController::class, 'kpi_comparison_data']);
});

// Route::get("/test", function(){
//     dd(Hash::make("1234"));
// });
