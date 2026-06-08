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
use App\Http\Controllers\Admin\HealthAssetsAdminController;
use App\Http\Controllers\AuthorsController;
use App\Http\Controllers\ExpertsController;
use App\Http\Controllers\FaqsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocaleSwitchController;
use App\Http\Controllers\PublicationsController;
use App\Http\Controllers\Admin\ResourcesController;
use App\Http\Controllers\AreasController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\ForumsController;
use App\Http\Controllers\ThemesController;

use App\Http\Controllers\Admin\GeoAreasController;
use App\Http\Controllers\Admin\FileTypesController;
use App\Http\Controllers\Admin\AssetTypesController;
use App\Http\Controllers\Admin\ForumsAdminController;
use App\Http\Controllers\Admin\HealthThemesController;
use App\Http\Controllers\Admin\LogsController;
use App\Http\Controllers\Admin\SearchLogAdminController;
use App\Http\Controllers\Admin\MapsController;
use App\Http\Controllers\Admin\MetricsController;
use App\Http\Controllers\Admin\PrivacyAdminController;
use App\Http\Controllers\Admin\QuizController;
use App\Http\Controllers\Admin\QuotesController;
use App\Http\Controllers\Admin\SubHealthThemesController;
use App\Http\Controllers\Admin\ParticipantBadgeManagementController;
use App\Http\Controllers\Admin\StorageManagementController;
use App\Http\Controllers\Admin\FederatedHubsController;
use App\Http\Controllers\HubMediaController;
use App\Http\Controllers\Admin\PublicationSubCategoryController;
use App\Http\Controllers\Admin\TagsController;
use App\Http\Controllers\Admin\LicensesController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\LanguageManagementController;
use App\Http\Controllers\Admin\SiteLanguageController;
use App\Http\Controllers\Admin\ToolsAdminController;
use App\Http\Controllers\AdminUnitFrontEndController;
use App\Http\Controllers\AIController;
use App\Http\Controllers\PdfChatController;
use App\Http\Controllers\PdfChatExportController;
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
use App\Http\Controllers\FederatedBrowseController;
use App\Http\Controllers\ToolsController;
use App\Http\Controllers\CoursesController;
use App\Http\Controllers\Admin\AdminCoursesController;
use App\Http\Controllers\CommunitiesController;
use App\Http\Controllers\Admin\DashboardsController;
use App\Http\Controllers\Admin\AdminEventsController;
use App\Http\Controllers\Admin\MailingListController;
use App\Http\Controllers\Admin\MessagingController;
use App\Http\Controllers\Admin\RssFeedController;
use App\Http\Controllers\Admin\RssStagingController;
use App\Models\User;
use App\Jobs\SendMailJob;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Admin\ContentRequestAdminController;
use App\Http\Controllers\ContentRequestReferralController;
use App\Http\Controllers\ContentRequestTrackController;
use App\Http\Controllers\HealthTopicsController;
use App\Http\Controllers\EventsController as PublicEventsController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\DocsController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\SitemapController;


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

Auth::routes(['verify' => true, 'reset' => false]);

/*
|--------------------------------------------------------------------------
| API Docs helpers
|--------------------------------------------------------------------------
| Swagger UI (`/docs`) is provided by l5-swagger. This endpoint serves the
| OpenAPI JSON with a dynamic `servers` URL sourced from env/current host.
| Path must NOT be under `docs/spec/*` — that prefix is served by l5-swagger
| and would shadow this route (404 in production).
*/
Route::get('docs/openapi-dynamic.json', [DocsController::class, 'openApiJson'])->name('docs.openapi.dynamic');
Route::get('hub-media/{path}', [HubMediaController::class, 'show'])->where('path', '.*')->name('hub.media');

/*
| Password reset: Laravel expects the token in the path (/password/reset/{token}).
| Legacy emails used ?token= which hit the "forgot email" route. We register
| password.request manually so ?token= redirects to the real reset form.
*/
Route::get('password/reset', function (\Illuminate\Http\Request $request) {
    if ($request->filled('token')) {
        $params = ['token' => $request->query('token')];
        if ($request->filled('email')) {
            $params['email'] = $request->query('email');
        }

        return redirect()->route('password.reset', $params);
    }

    return app(ForgotPasswordController::class)->showLinkRequestForm($request);
})->name('password.request');

Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

//Route::get('/test', [TestController::class, 'chat'])->name('test');
Route::get('/favicon.ico', [CommonController::class, 'favicon'])->name('favicon');
Route::get('/robots.txt', [RobotsController::class, 'show'])->name('robots');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.index');
Route::get('/sitemaps/{name}.xml', [SitemapController::class, 'section'])->where('name', '[a-z0-9\-]+')->name('sitemap.section');
Route::post('/locale/apply', [LocaleSwitchController::class, 'apply'])->name('locale.apply');
Route::get('/locale/{locale}', [LocaleSwitchController::class, 'switch'])->where('locale', '[a-z]{2}')->name('locale.switch');
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/endtour', [CommonController::class, 'endtour'])->name('endtour');
Route::get('/endtour', [CommonController::class, 'endtour'])->name('endtour.get'); // Backward compatibility

Route::get('/logout', function () {
    Auth::logout();
    clear_cache();
    return redirect()->route('home');
})->name('logout.get');

if(states_enabled()):

    Route::group(["prefix" => "countries"], function () {
            Route::get('/', [CountriesController::class, 'index'])->name('countries');
            Route::get('/map-data', [CountriesController::class, 'mapData'])->name('countries.map-data');
            Route::get('/indicator-summaries', [CountriesController::class, 'indicatorSummaries'])->name('countries.indicator-summaries');
            Route::get('/details/{slug}', [CountriesController::class, 'country'])->where('slug', '[\w\-]+')->name('countries.details');
            Route::get('/details', [CountriesController::class, 'country']);
    });
else:
    Route::group(["prefix" => "adminunits"], function () {
        Route::get('/', [AdminUnitFrontEndController::class, 'index'])->name('adminunits');
        Route::get('/map-data', [AdminUnitFrontEndController::class, 'mapData'])->name('adminunits.map-data');
        Route::get('/details', [AdminUnitFrontEndController::class, 'show']);
    });

endif;


Route::post('/registration', [AuthController::class, 'register'])->name('registration');
Route::get('/privacy', [CommonController::class, 'privacy'])->name('privacy');
Route::group(["prefix" => "browse"], function () {

    Route::get("themes", [ThemesController::class, 'index']);
    Route::get("subthemes", [ThemesController::class, 'subthemes']);
    Route::put('subthemes/update', [ThemesController::class, 'subthemes/update'])->name('subthemes.update');
    Route::get("authors", [AuthorsController::class, 'index'])->name('browse.authors');
    // Redirect legacy areas route to countries page
    Route::get("areas", function(){ return redirect('countries'); });

});

Route::get('/federated', [FederatedBrowseController::class, 'index'])->name('federation.browse');

Route::group(["prefix" => "records"], function () {

    Route::get("/tag/{slug}", [PublicationsController::class, 'searchByTag'])->where('slug', '[\w\-]+')->name('records.tag');
    Route::get("/", [PublicationsController::class, 'search']);
    Route::get("/resource/{slug}", [PublicationsController::class, 'show'])->where('slug', '[\w\-]+');
    Route::get("/resource", [PublicationsController::class, 'show']);
    Route::get("/search/fragment", [PublicationsController::class, 'searchFragment']);
    Route::get("/search", [PublicationsController::class, 'search']);
    Route::get("/subtheme", [PublicationsController::class, 'subtheme_pubs']);
    Route::get("/autocomplete", [PublicationsController::class, 'autocomplete']);
    Route::get("/shortened", [PublicationsController::class, 'shortened']);
    Route::post("/comment", [PublicationsController::class, 'comment']);
});

// Public Events
Route::get('/events/{id}', [PublicEventsController::class, 'show'])->name('public.events.show');

Route::group(["prefix" => "authors"], function () {
    Route::get("/", [AuthorsController::class, 'index']);
    Route::get("publications/{slug}/badge-communities", [PublicationsController::class, 'authorBadgeCommunities'])
        ->where('slug', '[\w\-]+')
        ->name('authors.badge-communities');
    Route::get("publications/{slug}", [PublicationsController::class, 'author_pubs'])
        ->where('slug', '[\w\-]+')
        ->name('authors.publications');
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
    Route::any("/request-content", [PublicationsController::class, 'request_content'])->name('content-request');
    Route::match(['get', 'post'], "/add_favourite", [PublicationsController::class, 'add_favourite']);
    Route::match(['get', 'post'], "/remove_favourite", [PublicationsController::class, 'remove_favourite']);
});

// Content request referral: requester tracking (token link) and hub user / CoP discussion
Route::get('/content-request/track/{token}', [ContentRequestTrackController::class, 'show'])->name('content-request.track');
Route::post('/content-request/track/{token}', [ContentRequestTrackController::class, 'storeMessage'])
    ->middleware('throttle:30,1')
    ->name('content-request.track.message');

Route::middleware(['auth', 'web'])->group(function () {
    Route::get('/content-request/referral/{contentRequest}/discuss', [ContentRequestReferralController::class, 'discuss'])
        ->name('content-request.referral.discuss');
    Route::post('/content-request/referral/{contentRequest}/discuss', [ContentRequestReferralController::class, 'storeMessage'])
        ->middleware('throttle:60,1')
        ->name('content-request.referral.discuss.message');
    Route::post('/content-request/referral/{contentRequest}/mark-processed', [ContentRequestReferralController::class, 'markProcessed'])
        ->middleware('throttle:20,1')
        ->name('content-request.referral.mark-processed');
});

// Health Topics routes
Route::group(["prefix" => "health-topics"], function () {
    Route::get("/", [HealthTopicsController::class, 'index'])->name('health-topics.index');
    Route::get("/{key}", [HealthTopicsController::class, 'show'])->where('key', '[\w\-]+')->name('health-topics.show');
});

Route::get("/verify", [AccountController::class, 'verifyAccount'])->name('account_verify');

Route::group(["prefix" => "tools",'middleware' => ['auth', 'web']], function () {
    Route::get("/", [ToolsController::class, 'index'])->name('tools');
    Route::get("/flexmonster", [ToolsController::class, 'fleximonster'])->name('tools.flexmonster');
    Route::get("/excel", [ToolsController::class, 'excel'])->name('tools.excel');
});

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
    Route::get("/my-forums", [ForumsController::class, 'myForums'])->name('account.my-forums');
    Route::get("/my-discussions", [ForumsController::class, 'myDiscussions'])->name('account.my-discussions');
    Route::get("/my-discussions/{forum}/edit", [ForumsController::class, 'editMyDiscussion'])->name('account.my-discussions.edit');
    Route::post("/my-discussions/{forum}/resubmit", [ForumsController::class, 'saveMyDiscussion'])->name('account.my-discussions.resubmit');
    Route::get("/my-communities", [CommunitiesController::class, 'myCommunities'])->name('account.my-communities');
    Route::get("/chats", [AccountController::class, 'chats'])->name('account.chats');
    Route::post("/chats/delete", [AccountController::class, 'deleteChat'])->name('account.chats.delete');

});


Route::group(["prefix" => "admin", 'middleware' => ['auth', 'web']], function () {

    Route::get("/", [AdminController::class, 'index'])->name('admin.index');
    Route::get("/dashboard", [AdminController::class, 'dashboards'])->name('admin.dashboard');
    if(states_enabled())
    Route::get("/rccdashboards", [GraphController::class, 'rcc_admin'])->name('admin.rccdashboards');
    Route::get("/rccdashboards/data", [GraphController::class, 'rcc_data'])->name('admin.rccdashboards.data');
    Route::get('/dashboards', [DashboardsController::class, 'details'])->name('admin.dashboard.details');
    Route::get('/storage-management', [StorageManagementController::class, 'index'])->name('admin.storage.index');
    Route::post('/storage-management', [StorageManagementController::class, 'update'])->name('admin.storage.update');
    Route::post('/storage-management/test-connection', [StorageManagementController::class, 'testConnection'])->name('admin.storage.test');
    Route::post('/storage-management/test-offsite-backup', [StorageManagementController::class, 'testOffsiteConnection'])->name('admin.storage.test-offsite');
    Route::post('/storage-management/offsite-backup', [StorageManagementController::class, 'runOffsiteBackup'])->name('admin.storage.offsite-backup');
    Route::get('/storage-management/browse', [StorageManagementController::class, 'browse'])->name('admin.storage.browse');
    Route::get('/storage-management/publication-references', [StorageManagementController::class, 'publicationReferences'])->name('admin.storage.publication-references');
    Route::get('/storage-management/browse-backups', [StorageManagementController::class, 'browseBackups'])->name('admin.storage.browse-backups');
    Route::post('/storage-management/backup', [StorageManagementController::class, 'runBackup'])->name('admin.storage.backup');
    Route::post('/storage-management/backup-env', [StorageManagementController::class, 'runEnvBackup'])->name('admin.storage.backup-env');
    Route::get('/storage-management/download-env-backup', [StorageManagementController::class, 'downloadEnvBackup'])->name('admin.storage.download-env-backup');
    Route::post('/storage-management/restore', [StorageManagementController::class, 'restoreBackup'])->name('admin.storage.restore');
    Route::get('/storage-management/backup-tables', [StorageManagementController::class, 'backupTablesInDirectory'])->name('admin.storage.backup-tables');
    Route::post('/storage-management/migrate', [StorageManagementController::class, 'migrate'])->name('admin.storage.migrate');
    Route::post('/storage-management/migrate-host', [StorageManagementController::class, 'migrateHost'])->name('admin.storage.migrate-host');
    Route::post('/storage-management/purge-legacy', [StorageManagementController::class, 'purgeLegacy'])->name('admin.storage.purge-legacy');
    Route::get('/storage-management/migration-status', [StorageManagementController::class, 'migrationStatus'])->name('admin.storage.migration-status');
    Route::get('/storage-management/system-metrics', [StorageManagementController::class, 'systemMetrics'])->name('admin.storage.system-metrics');

    Route::get('/maps', [MapsController::class, 'index'])->name('admin.maps.index');
    Route::get('/maps/topology/status', [MapsController::class, 'topologyVersionStatus'])->name('admin.maps.topology.status');
    Route::post('/maps/topology/check', [MapsController::class, 'checkTopologyVersion'])->name('admin.maps.topology.check');
    Route::post('/maps/topology/apply', [MapsController::class, 'applyTopologyVersion'])->name('admin.maps.topology.apply');
    Route::post('/maps/topology/revert/{id}', [MapsController::class, 'revertTopologyVersion'])->name('admin.maps.topology.revert')->where('id', '[0-9]+');
    Route::get('/maps/definitions/search', [MapsController::class, 'searchDefinitions'])->name('admin.maps.definitions.search');
    Route::get('/maps/create', [MapsController::class, 'create'])->name('admin.maps.create');
    Route::get('/maps/preview', [MapsController::class, 'preview'])->name('admin.maps.preview');
    Route::get('/maps/{id}/edit', [MapsController::class, 'edit'])->name('admin.maps.edit')->where('id', '[0-9]+');
    Route::post('/maps', [MapsController::class, 'store'])->name('admin.maps.store');
    Route::post('/maps/assignments', [MapsController::class, 'saveAssignments'])->name('admin.maps.assignments');
    Route::delete('/maps/{id}', [MapsController::class, 'destroy'])->name('admin.maps.destroy')->where('id', '[0-9]+');

    Route::get('/federated-hubs', [FederatedHubsController::class, 'index'])->name('admin.federation.index');
    Route::post('/federated-hubs', [FederatedHubsController::class, 'store'])->name('admin.federation.store');
    Route::put('/federated-hubs/{hub}', [FederatedHubsController::class, 'update'])->name('admin.federation.update');
    Route::delete('/federated-hubs/{hub}', [FederatedHubsController::class, 'destroy'])->name('admin.federation.destroy');
    Route::post('/federated-hubs/{hub}/connect', [FederatedHubsController::class, 'connect'])->name('admin.federation.connect');
    Route::post('/federated-hubs/{hub}/sync', [FederatedHubsController::class, 'sync'])->name('admin.federation.sync');
    Route::post('/federated-hubs/sync-central', [FederatedHubsController::class, 'syncFromCentral'])->name('admin.federation.sync-central');
    Route::post('/federated-hubs/refresh-central-token', [FederatedHubsController::class, 'refreshCentralToken'])->name('admin.federation.refresh-central');
    Route::post('/federated-hubs/test-central', [FederatedHubsController::class, 'testCentral'])->name('admin.federation.test-central');
    Route::get('/federated-content/pending', [\App\Http\Controllers\Admin\FederatedContentAdminController::class, 'pending'])->name('admin.federation.pending-content');
    Route::post('/federated-content/review', [\App\Http\Controllers\Admin\FederatedContentAdminController::class, 'bulkReview'])->name('admin.federation.content-review');

    Route::get("/configure", [SettingsController::class, 'index'])->name('admin.configure');
    Route::post("/configure", [SettingsController::class, 'store'])->name('admin.config.save');
    Route::post("/configure/sso", [SettingsController::class, 'storeSso'])->name('admin.config.sso.save');
    Route::post("/configure/clear-cache", [SettingsController::class, 'clearCache'])->name('admin.config.clear-cache');
    Route::post("/configure/send-profile-reminders", [SettingsController::class, 'sendProfileReminders'])->name('admin.config.send-profile-reminders');
    Route::post("/configure/custom-font", [SettingsController::class, 'storeCustomFont'])->name('admin.config.custom-font.store');
    Route::post("/configure/custom-font/delete/{id}", [SettingsController::class, 'deleteCustomFont'])->name('admin.config.custom-font.delete');
    Route::get("/configure/export-config", [SettingsController::class, 'exportConfig'])->name('admin.config.export');
    Route::post("/configure/import-config", [SettingsController::class, 'importConfig'])->name('admin.config.import');

    Route::get('/language-management', [LanguageManagementController::class, 'index'])->name('admin.language-management.index');
    Route::get('/language-management/grid', [LanguageManagementController::class, 'grid'])->name('admin.language-management.grid');
    Route::post('/language-management/ai-translate', [LanguageManagementController::class, 'aiTranslate'])->name('admin.language-management.ai-translate');
    Route::post('/language-management', [LanguageManagementController::class, 'update'])->name('admin.language-management.update');

    Route::get('/site-languages', [SiteLanguageController::class, 'index'])->name('admin.site-languages.index');
    Route::post('/site-languages', [SiteLanguageController::class, 'store'])->name('admin.site-languages.store');
    Route::put('/site-languages/{siteLanguage}', [SiteLanguageController::class, 'update'])->name('admin.site-languages.update');
    Route::delete('/site-languages/{siteLanguage}', [SiteLanguageController::class, 'destroy'])->name('admin.site-languages.destroy');

    // Notification endpoints
    Route::get("/notifications/pending-counts", [\App\Http\Controllers\Admin\NotificationController::class, 'getPendingCounts'])->name('admin.notifications.counts');
    Route::get("/notifications/pending-items", [\App\Http\Controllers\Admin\NotificationController::class, 'getPendingItems'])->name('admin.notifications.items');

    Route::get("/events", [AdminEventsController::class, 'index'])->name('admin.events');

    Route::prefix('events')->name('admin.events.')->group(function () {
        Route::get('/', [AdminEventsController::class, 'index'])->name('index');
        Route::get('/create', [AdminEventsController::class, 'create'])->name('create');
        Route::post('/save', [AdminEventsController::class, 'store'])->name('store');
        Route::get('/{id}', [AdminEventsController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [AdminEventsController::class, 'edit'])->name('edit');
        Route::put('/{id}/save', [AdminEventsController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminEventsController::class, 'destroy'])->name('destroy');
    });

    Route::group(["prefix" => "publications"], function () {

        Route::get("/", [ResourcesController::class, 'index']);
        Route::get("/pending", [ResourcesController::class, 'pending']);
        Route::get("/create", [ResourcesController::class, 'create']);
        Route::get("/edit", [ResourcesController::class, 'edit']);
        Route::get("/details", [ResourcesController::class, 'details']);
        Route::post("/save", [ResourcesController::class, 'store']);
        Route::post("/approval", [ResourcesController::class, 'approval']);
        Route::post("/bulk-approval", [ResourcesController::class, 'bulkApproval'])->name('admin.publications.bulk-approval');
        Route::get("/moderate", [ResourcesController::class, 'moderate']);
        Route::get("/delete", [ResourcesController::class, 'destroy']);
        Route::get("/approve_comment", [ResourcesController::class, 'approve_comment']);
        Route::get("/reject_comment", [ResourcesController::class, 'reject_comment']);
        Route::get("/summaries", [ResourcesController::class, 'summaries']);
        Route::get("/summary", [ResourcesController::class, 'summary']);
        Route::post("/summary_approval", [ResourcesController::class, 'summary_approval']);
        Route::post("/import", [ResourcesController::class, 'import']);
        Route::get("/import-template", [ResourcesController::class, 'import_template']);
        Route::post("/bulk-action", [ResourcesController::class, 'bulkAction'])->name('admin.publications.bulk-action');
        Route::post("/toggle-featured", [ResourcesController::class, 'toggleFeatured'])->name('admin.publications.toggle-featured');
        Route::post("/toggle-active", [ResourcesController::class, 'toggleActive'])->name('admin.publications.toggle-active');

    });

    Route::get('/participant-badges', [ParticipantBadgeManagementController::class, 'index'])
        ->name('admin.participant-badges.index');
    Route::get('/participant-badges/job-status', [ParticipantBadgeManagementController::class, 'jobStatus'])
        ->name('admin.participant-badges.job-status');
    Route::get('/participant-badges/audit', [ParticipantBadgeManagementController::class, 'audit'])
        ->name('admin.participant-badges.audit');
    Route::post('/participant-badges/run-award-job', [ParticipantBadgeManagementController::class, 'runAwardJob'])
        ->name('admin.participant-badges.run-award-job');
    Route::post('/participant-badges/award', [ParticipantBadgeManagementController::class, 'award'])
        ->name('admin.participant-badges.award');
    Route::delete('/participant-badges/lifetime/{userLifetimeBadge}', [ParticipantBadgeManagementController::class, 'revoke'])
        ->name('admin.participant-badges.revoke');

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

    //assettypes
    Route::group(["prefix" => "assettypes", "as"=> "assettypes."], function () {

        Route::get("/", [AssetTypesController::class, 'index']);
        Route::post("/save", [AssetTypesController::class, 'store'])->name('store');
        Route::get("/delete", [AssetTypesController::class, 'destroy']);
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

    // Sub Categories (publication form - after Categories in dropdown lists)
    Route::group(["prefix" => "subcategories", "as" => "admin.subcategories."], function () {
        Route::get("/", [PublicationSubCategoryController::class, 'index'])->name('index');
        Route::post("/", [PublicationSubCategoryController::class, 'store'])->name('store');
        Route::put("/{id}", [PublicationSubCategoryController::class, 'update'])->name('update');
        Route::get("/delete", [PublicationSubCategoryController::class, 'destroy'])->name('destroy');
    });

    //tags
    Route::group(["prefix" => "tags", 'as' => 'tags.'], function () {

        Route::get("/", [TagsController::class, 'index']);
        Route::post("/save", [TagsController::class, 'store']);
        Route::put('/update', [TagsController::class, 'update'])->name('update');
        Route::get("/delete", [TagsController::class, 'destroy']);
        Route::post('/ai-generate', [TagsController::class, 'aiGenerate'])->name('ai-generate');
        Route::post('/ai-import', [TagsController::class, 'aiImport'])->name('ai-import');
        Route::post('/ai-describe', [TagsController::class, 'aiDescribe'])->name('ai-describe');
        Route::post('/ai-apply-overviews', [TagsController::class, 'aiApplyOverviews'])->name('ai-apply-overviews');
        Route::post('/deduplicate', [TagsController::class, 'deduplicate'])->name('deduplicate');
    });

    //licenses
    Route::group(["prefix" => "licenses", 'as' => 'admin.licenses.'], function () {
        Route::get("/", [LicensesController::class, 'index'])->name('index');
        Route::get("/create", [LicensesController::class, 'create'])->name('create');
        Route::post("/save", [LicensesController::class, 'store'])->name('store');
        Route::get("/{id}/edit", [LicensesController::class, 'edit'])->name('edit');
        Route::put("/{id}", [LicensesController::class, 'update'])->name('update');
        Route::delete("/{id}", [LicensesController::class, 'destroy'])->name('destroy');
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
        Route::get("/approved",[ForumsAdminController::class,'approved']);
        Route::get("/rejected",[ForumsAdminController::class,'rejected']);
        Route::get("/delete",[ForumsAdminController::class,'destroy']);
        Route::get("/moderate",[ForumsAdminController::class,'moderation']);
        Route::any("/approve",[ForumsAdminController::class,'approve']);
        Route::post("/reject",[ForumsAdminController::class,'reject']);
        Route::get("/approve-comment", [ForumsAdminController::class, 'approveComment'])->name('admin.forums.approve-comment');
        Route::get("/reject-comment", [ForumsAdminController::class, 'rejectComment'])->name('admin.forums.reject-comment');
        Route::get("/details", [ForumsAdminController::class, 'details']);
        Route::post("/moderation/update-pending", [ForumsAdminController::class, 'updatePending'])->name('admin.forums.moderation.update-pending');
        Route::post("/moderation/grammar-assist", [ForumsAdminController::class, 'grammarAssist'])->name('admin.forums.moderation.grammar-assist');

    });

    //authors
    Route::group(["prefix" => "authors"], function () {
        Route::get("/", [AuthorsAdminController::class, 'index']);
        Route::post("/store", [AuthorsAdminController::class, 'store']);
        Route::post("/merge", [AuthorsAdminController::class, 'merge']);
        Route::post("/delete", [AuthorsAdminController::class, 'destroy']);
        Route::get("/delete", [AuthorsAdminController::class, 'destroy']);
        Route::get("/{author}", [AuthorsAdminController::class, 'show'])->whereNumber('author');
    });

    //authors
    Route::group(["prefix" => "kpi"], function () {

        Route::get("/", [KpiController::class, 'index'])->middleware('permission:manage_kpis');
        Route::get("/data", [KpiController::class, 'data'])->middleware('permission:manage_kpis');
        Route::get("/subject-areas", [\App\Http\Controllers\KpiSubjectAreaController::class, 'index'])->middleware('permission:manage_kpis');
        Route::post("/subject-areas/save", [\App\Http\Controllers\KpiSubjectAreaController::class, 'save'])->middleware('permission:manage_kpis');
        Route::get("/subject-areas/get", [\App\Http\Controllers\KpiSubjectAreaController::class, 'get'])->middleware('permission:manage_kpis');
        Route::get("/subject-areas/delete", [\App\Http\Controllers\KpiSubjectAreaController::class, 'destroy'])->middleware('permission:manage_kpis');
        Route::post("/subject-areas/dedupe/auto", [\App\Http\Controllers\KpiSubjectAreaController::class, 'dedupeAuto'])->middleware('permission:manage_kpis');
        Route::post("/subject-areas/dedupe/merge", [\App\Http\Controllers\KpiSubjectAreaController::class, 'merge'])->middleware('permission:manage_kpis');
        Route::post("/owid/discover", [KpiController::class, 'discoverOwid'])->middleware('permission:manage_kpis');
        Route::post("/owid/sync", [KpiController::class, 'syncOwid'])->middleware('permission:manage_kpis');
        Route::post("/owid/sync-one", [KpiController::class, 'syncOne'])->middleware('permission:manage_kpis');
        Route::post("/owid/fresh-fetch", [KpiController::class, 'freshFetch'])->middleware('permission:manage_kpis');
        Route::post("/owid/approve-defaults", [KpiController::class, 'approveDefaults'])->middleware('permission:manage_kpis');
        Route::post("/owid/generate-narrations", [KpiController::class, 'generateNarrations'])->middleware('permission:manage_kpis');
        Route::get("/duplicates", [KpiController::class, 'duplicates'])->middleware('permission:manage_kpis');
        Route::get("/dedupe/scan", [KpiController::class, 'duplicateScan'])->middleware('permission:manage_kpis');
        Route::post("/dedupe/indicators/auto", [KpiController::class, 'dedupeIndicatorsAuto'])->middleware('permission:manage_kpis');
        Route::post("/dedupe/indicators/merge", [KpiController::class, 'mergeIndicators'])->middleware('permission:manage_kpis');
        Route::post("/settings/save", [KpiController::class, 'saveSettings'])->middleware('permission:manage_kpis');
        Route::get("/owid/task/{id}", [KpiController::class, 'taskStatus'])->middleware('permission:manage_kpis');
        Route::post("/approve", [KpiController::class, 'approve'])->middleware('permission:manage_kpis');
        Route::post("/bulk-action", [KpiController::class, 'bulkAction'])->middleware('permission:manage_kpis');
        Route::post("/recall", [KpiController::class, 'recall'])->middleware('permission:manage_kpis');
        Route::get("/get", [KpiController::class, 'get']);
        Route::get("/get_data", [KpiController::class, 'get_data']);
        Route::post("/save", [KpiController::class, 'save'])->middleware('permission:manage_kpis');
        Route::post("/save_data", [KpiController::class, 'save_data'])->middleware('permission:manage_kpis');
        Route::post("/update_data", [KpiController::class, 'update_data'])->middleware('permission:manage_kpis');
        Route::get("/delete", [KpiController::class, 'destroy'])->middleware('permission:manage_kpis');
        Route::get("/delete_data", [KpiController::class, 'destroy_data'])->middleware('permission:manage_kpis');
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
        Route::post('/refresh-openai', [FactsAdminController::class, 'refreshOpenAi'])->name('refresh-openai');
        Route::get("/delete", [FactsAdminController::class, 'destroy']);
    });

    //faq
    Route::group(["prefix" => "faqs"], function () {

        Route::get("/", [FaqsAdminController::class, 'index']);
        Route::get("/get", [FaqsAdminController::class, 'get']);
        Route::post("/save", [FaqsAdminController::class, 'store']);
        Route::get("/delete", [FaqsAdminController::class, 'destroy']);
    });


    //experts
    Route::group(["prefix" => "experts"], function () {

        Route::get("/", [ExpertsAdminController::class, 'index']);
        Route::post("/save", [ExpertsAdminController::class, 'store']);
        Route::get("/delete", [ExpertsAdminController::class, 'destroy']);
        Route::get("/job-titles-by-isco", [ExpertsAdminController::class, 'getJobTitlesByIsco']);

        Route::group(["prefix" => "types"], function () {

            Route::get("/", [ExpertsAdminController::class, 'types']);
            Route::post("/save", [ExpertsAdminController::class, 'save_type']);
            Route::get("/delete", [ExpertsAdminController::class, 'delete_type']);

        });

    });

    //healthassets
    Route::group(["prefix" => "healthassets"], function () {
        Route::get("/", [HealthAssetsAdminController::class, 'index']);
        Route::get("/detail", [HealthAssetsAdminController::class, 'details']);
    });


    //accessgroup
    Route::group(["prefix" => "accessgroups"], function () {

        Route::get("/", [AccessGroupsController::class, 'index']);
        Route::post("/save", [AccessGroupsController::class, 'store']);
        Route::get("/delete", [AccessGroupsController::class, 'destroy']);
    });

    //commsofpractice (POST routes before GET /{id} so send_invitation etc. are not matched as id)
    Route::group(["prefix" => "commsofpractice"], function () {

        Route::any("/", [CommsOfPracticeController::class, 'index']);
        Route::any("/moderate", [CommsOfPracticeController::class, 'moderate']);
        Route::post("/save", [CommsOfPracticeController::class, 'store']);
        Route::get("/delete", [CommsOfPracticeController::class, 'destroy']);
        Route::get('/get', [CommsOfPracticeController::class, 'getOne']);
        Route::post("/member_action", [CommsOfPracticeController::class, 'memberAction'])->name('admin.commsofpractice.memberAction');
        Route::post("/add_member", [CommsOfPracticeController::class, 'addMember'])->name('admin.commsofpractice.addMember');
        Route::post("/delete_member", [CommsOfPracticeController::class, 'deleteMember'])->name('admin.commsofpractice.deleteMember');
        Route::post("/send_invitation", [CommsOfPracticeController::class, 'sendInvitation'])->name('admin.commsofpractice.sendInvitation');
        Route::post("/resend_invitation", [CommsOfPracticeController::class, 'resendInvitation'])->name('admin.commsofpractice.resendInvitation');
        Route::post("/delete_invitation", [CommsOfPracticeController::class, 'deleteInvitation'])->name('admin.commsofpractice.deleteInvitation');
        Route::post("/bulk_invite", [CommsOfPracticeController::class, 'bulkInviteFromCsv'])->name('admin.commsofpractice.bulkInvite');
        Route::get("/participants", [CommsOfPracticeController::class, 'participants'])->name('admin.commsofpractice.participants');
        Route::get('/{id}', [CommsOfPracticeController::class, 'show'])->name('admin.commsofpractice.details');
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
            Route::post("/update", [DataRecordsAdminController::class, 'update_category']);
            Route::get("/delete", [DataRecordsAdminController::class, 'delete_category']);

            Route::get('/ajax/subcategories', [DataRecordsAdminController::class, 'getSubcategories'])->name('get-subcategories');
        });
    });



    Route::group(["prefix" => "logs"], function () {

        Route::get("/access", [LogsController::class, 'index']);
        Route::get("/user", [LogsController::class, 'trail']);
    });

    Route::get('/search-logs', [SearchLogAdminController::class, 'index'])->name('admin.search-logs.index');

    Route::group(["prefix" => "metrics"], function () {

        Route::get("/", [MetricsController::class, 'index']);
        Route::get("/live", [MetricsController::class, 'live']);
    });

    Route::group(["prefix" => "mailing_list"], function () {
        Route::get("/", [MailingListController::class, 'index'])->name('admin.mailing_list.index');
        Route::post("/", [MailingListController::class, 'store'])->name('admin.mailing_list.store');
        Route::put("/{id}", [MailingListController::class, 'update'])->name('admin.mailing_list.update');
        Route::delete("/{id}", [MailingListController::class, 'destroy'])->name('admin.mailing_list.destroy');
        Route::post("/send-email", [MailingListController::class, 'sendEmail'])->name('admin.mailing_list.sendEmail');
        Route::get("/export", [MailingListController::class, 'export'])->name('admin.mailing_list.export');
        Route::post("/bulk-action", [MailingListController::class, 'bulkAction'])->name('admin.mailing_list.bulkAction');
    });

    Route::group(["prefix" => "rss-feeds", "as" => "admin.rss_feeds."], function () {
        Route::get("/", [RssFeedController::class, 'index'])->name('index');
        Route::get("/create", [RssFeedController::class, 'create'])->name('create');
        Route::post("/", [RssFeedController::class, 'store'])->name('store');
        Route::post("/fetch-now", [RssFeedController::class, 'fetchNow'])->name('fetchNow');
        Route::get("/fetch-progress/{runId}", [RssFeedController::class, 'fetchProgress'])->name('fetchProgress');
        Route::get("/{id}/edit", [RssFeedController::class, 'edit'])->name('edit');
        Route::put("/{id}", [RssFeedController::class, 'update'])->name('update');
        Route::delete("/{id}", [RssFeedController::class, 'destroy'])->name('destroy');
    });

    Route::group(["prefix" => "rss-staging", "as" => "admin.rss_staging."], function () {
        Route::get("/", [RssStagingController::class, 'index'])->name('index');
        Route::get("/{id}/edit", [RssStagingController::class, 'edit'])->name('edit');
        Route::post("/{id}/reject", [RssStagingController::class, 'reject'])->name('reject');
    });

    Route::group(["prefix" => "tools"], function () {
        Route::get("/", [ToolsAdminController::class, 'index']);
        Route::post("/store", [ToolsAdminController::class, 'store']);
        Route::get("/delete", [ToolsAdminController::class, 'destroy']);
    });

    //accessgroup
    Route::group(["prefix" => "courses"], function () {

        Route::get("/", [AdminCoursesController::class, 'index'])->name('admin.courses.index');
        Route::get('/integrations', [AdminCoursesController::class, 'integrations'])->name('admin.courses.integrations');
        Route::post('/integrations', [AdminCoursesController::class, 'saveIntegrations'])->name('admin.courses.integrations.save');
        Route::get('/ai-config', [AdminCoursesController::class, 'aiConfig'])->name('admin.courses.ai-config');
        Route::post('/ai-config', [AdminCoursesController::class, 'saveAiConfig'])->name('admin.courses.ai-config.save');
        Route::get('/sitemap', [AdminCoursesController::class, 'sitemap'])->name('admin.courses.sitemap');
        Route::post('/sitemap/generate', [AdminCoursesController::class, 'generateSitemap'])->name('admin.courses.sitemap.generate');
        Route::post("/store", [AdminCoursesController::class, 'store']);
        Route::post("/import", [AdminCoursesController::class, 'import']);
        Route::get("/delete", [AdminCoursesController::class, 'destroy']);
        Route::get('/details/{id}', [CoursesController::class, 'showDetails'])->name('admin.courses.details');
    });
});

// Admin Messaging routes
Route::prefix('admin/messaging')->name('admin.messaging.')->middleware(['auth'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\MessagingController::class, 'index'])->name('index');
    Route::post('/send', [\App\Http\Controllers\Admin\MessagingController::class, 'sendMessage'])->name('send');
});

// Mailing List route (also accessible at /mailing_list)
Route::get('/mailing_list', [MailingListController::class, 'index'])->middleware(['auth'])->name('mailing_list.index');

//permissions and access control
Route::group(['prefix' => 'permissions', 'middleware' => ['auth', 'web']], function () {

    Route::get('/',  [PermissionController::class, 'permissions'])->name('permissions.permissions');
    Route::get('/roles',  [PermissionController::class, 'index'])->name('permissions.roles');
    Route::post('/role',  [PermissionController::class, 'saveRole'])->name('permissions.role');
    Route::post('/permission',  [PermissionController::class, 'createPermission'])->name('permissions.permission');
    Route::post('/torole',  [PermissionController::class, 'permissionsToRole'])->name('permissions.torole');
    Route::get('/users',  [PermissionController::class, 'users'])->name('permissions.users');
    Route::get('/user',  [PermissionController::class, 'users'])->name('permissions.filerusers');
    Route::post('/saveuser',  [PermissionController::class, 'saveUser'])->name('permissions.saveuser');
    Route::post('/userrole',  [PermissionController::class, 'roleToUser'])->name('permissions.userrole');

    Route::get('/changepass',  [PermissionController::class, 'changePassword'])->name('permissions.changepass');
    Route::post('/reset',  [PermissionController::class, 'resetUser'])->name('permissions.reset');

    Route::post('/delete',  [PermissionController::class, 'deleteUser'])->name('permissions.delete');
    Route::post('/send-verification',  [PermissionController::class, 'sendVerification'])->name('permissions.sendverification');
    Route::post('/verify-user',  [PermissionController::class, 'verifyUser'])->name('permissions.verifyuser');
    Route::any('/trail',  [PermissionController::class, 'trail'])->name('permissions.trail');
    Route::any('/profile',  [PermissionController::class, 'profile'])->name('permissions.profile');
});


Route::group(["prefix" => "forums"], function () {

    Route::get("/", [ForumsController::class, 'index'])->name('forums.index');
    Route::get("/create", [ForumsController::class, 'create'])->name('forums.create');
    Route::get("/thread/{slug}", [ForumsController::class, 'thread'])->where('slug', '[\w\-]+');
    Route::get("/thread", [ForumsController::class, 'thread'])->name('forums.thread');
    Route::get("/join", [ForumsController::class, 'join'])->name('forums.join');
    Route::get('/comment-attachment/{attachment}/pdf', [ForumsController::class, 'commentAttachmentPdf'])->name('forums.comment-attachment.pdf');
    Route::post("/comment", [ForumsController::class, 'comment'])->name('forums.comment');
    Route::post("/publish", [ForumsController::class, 'publish'])->name('forums.publish');
    Route::post("/like", [ForumsController::class, 'like'])->name('forums.like');
    Route::post("/comment/like", [ForumsController::class, 'likeComment'])->name('forums.comment.like');
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

Route::group(["prefix" => "ai"], function () {
    // Summarise and PDF chat require authentication
    Route::post("/summarise",  [AIController::class, 'summarise'])->middleware('auth');
    Route::post("/summarise-file",  [AIController::class, 'summariseFile'])->name('ai.summarise.file')->middleware('auth');
    Route::post("/summarise-stream",  [AIController::class, 'summariseStream'])->middleware('auth');

    // PDF chat (ChatPDF): get/create session, send message (stream or JSON)
    Route::post("/pdf-chat/session", [PdfChatController::class, 'getOrCreateSession'])->name('ai.pdf-chat.session')->middleware('auth');
    Route::post("/pdf-chat/message", [PdfChatController::class, 'sendMessage'])->name('ai.pdf-chat.message')->middleware('auth');
    Route::post("/pdf-chat/export-pdf", [PdfChatExportController::class, 'exportPdf'])->name('ai.pdf-chat.export-pdf')->middleware('auth');
    Route::post("/pdf-chat/export-word", [PdfChatExportController::class, 'exportWord'])->name('ai.pdf-chat.export-word')->middleware('auth');
});

// Summernote image upload
Route::post('/image-upload', [App\Http\Controllers\CommonController::class, 'imageUpload'])->name('image.upload');

Route::group(["prefix" => "courses"], function () {
    Route::get("/",  [CoursesController::class, 'index']);
    Route::get('/details/{id}', [CoursesController::class, 'showDetails'])->name('courses.details');
    Route::post('/fetch', [CoursesController::class, 'startFetch'])->middleware('auth')->name('courses.fetch');
    Route::get('/fetch/{id}', [CoursesController::class, 'fetchStatus'])->middleware('auth')->name('courses.fetch.status');
});

Route::group(["prefix" => "communities"], function () {
    Route::get('/', [CommunitiesController::class, 'index'])->name('community.index');
    Route::post('/join', [CommunitiesController::class, 'join'])->name('community.join');
    Route::post('/leave', [CommunitiesController::class, 'leave'])->name('community.leave');
    Route::post('/detail/{id}/invite', [CommunitiesController::class, 'inviteColleagues'])->middleware('auth')->whereNumber('id')->name('community.invite');
    Route::post('/detail/{id}/member-status', [CommunitiesController::class, 'updateMemberStatus'])->middleware('auth')->whereNumber('id')->name('community.member-status');
    Route::get('/detail/{id}/members-data', [CommunitiesController::class, 'membersData'])->middleware('auth')->whereNumber('id')->name('community.members-data');
    Route::post('/detail/{id}/events', [CommunitiesController::class, 'createCommunityEvent'])->middleware('auth')->whereNumber('id')->name('community.events.create');
    Route::get('/accept-invitation/{token}', [CommunitiesController::class, 'acceptInvitation'])->name('community.accept-invitation');
    Route::get('/detail/{key}', [CommunitiesController::class, 'detail'])->where('key', '[\w\-]+')->name('community.detail');
});

Route::middleware('throttle:oauth')->group(function () {
    Route::get('auth/microsoft', function () {
        \App\Support\SsoConfig::ensureRuntimeConfig();

        if (! \App\Support\SsoConfig::microsoftEnabled()) {
            return redirect('/login')->with('alert_class', 'danger')
                ->with('alert', 'Microsoft login is currently disabled.');
        }
        if (! \App\Support\SsoConfig::providerConfigured('microsoft')) {
            \Log::warning('Microsoft login: client_id or client_secret missing.', [
                'source' => \App\Support\SsoConfig::credentialSource(),
            ]);

            return redirect('/login')->with('alert_class', 'danger')
                ->with('alert', 'Microsoft login is not configured. Set MICROSOFT_CLIENT_ID and MICROSOFT_CLIENT_SECRET in .env (or use EXCHANGE_* if using the same Azure app).');
        }

        return Socialite::driver('microsoft')->redirect();
    });

    Route::get('auth/microsoft/callback', [AuthController::class, 'microsoftLogin']);

    Route::get('auth/google', function () {
        \App\Support\SsoConfig::ensureRuntimeConfig();

        if (! \App\Support\SsoConfig::googleEnabled()) {
            return redirect('/login')->with('alert_class', 'danger')
                ->with('alert', 'Google login is currently disabled.');
        }
        if (! \App\Support\SsoConfig::providerConfigured('google')) {
            return redirect('/login')->with('alert_class', 'danger')
                ->with('alert', 'Google login is not configured. Set GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET in .env.');
        }

        return Socialite::driver('google')->redirect();
    });

    Route::get('auth/google/callback', [AuthController::class, 'googleLogin']);

    Route::get('auth/linkedin', function () {
        \App\Support\SsoConfig::ensureRuntimeConfig();

        if (! \App\Support\SsoConfig::linkedinEnabled()) {
            return redirect('/login')->with('alert_class', 'danger')
                ->with('alert', 'LinkedIn login is currently disabled.');
        }
        if (! \App\Support\SsoConfig::providerConfigured('linkedin')) {
            return redirect('/login')->with('alert_class', 'danger')
                ->with('alert', 'LinkedIn login is not configured. Set LINKEDIN_CLIENT_ID and LINKEDIN_CLIENT_SECRET in .env.');
        }
        try {
            return Socialite::driver('linkedin-openid')->redirect();
        } catch (\Exception $e) {
            \Log::error('LinkedIn redirect error: ' . $e->getMessage());
            return redirect('/login')->with('alert_class', 'danger')
                ->with('alert', 'LinkedIn login is currently unavailable. Please try again later.');
        }
    });

    Route::get('auth/linkedin/callback', [AuthController::class, 'linkedinLogin']);
});

Route::get("/tests",function(){

    //$tokens = User::whereNotNull('fcm_token')->pluck('fcm_token')->toArray();
   //return  sendPushNotification("Title","Hello",$tokens);
   //extract_pdf_as_image("https://www.shobhituniversity.ac.in/pdf/econtent/C-Programming-Rajesh-Pandey.pdf","storage/app/public/publications/");

   //$req = ["email"=>"agabaandre@gmail.com","subject"=>"Test","body"=>"Hello"];
   //SendMailJob::dispatch($req);

});

Route::group(['prefix' => 'admin/content-requests', 'as' => 'admin.content-requests.', 'middleware' => ['auth', 'web', 'permission:view_content_requests']], function () {
    Route::get('/', [ContentRequestAdminController::class, 'index'])->name('index');
    Route::get('/create', [ContentRequestAdminController::class, 'create'])->name('create');
    Route::post('/', [ContentRequestAdminController::class, 'store'])->middleware('permission:manage_content_requests')->name('store');
    Route::get('/{id}/edit', [ContentRequestAdminController::class, 'edit'])->middleware('permission:manage_content_requests')->name('edit');
    Route::put('/{id}', [ContentRequestAdminController::class, 'update'])->middleware('permission:manage_content_requests')->name('update');
    Route::post('/{id}/process', [ContentRequestAdminController::class, 'process'])->middleware('permission:manage_content_requests')->name('process');
    Route::post('/{id}/refer', [ContentRequestAdminController::class, 'refer'])->middleware('permission:manage_content_requests')->name('refer');
    Route::delete('/{id}', [ContentRequestAdminController::class, 'destroy'])->middleware('permission:manage_content_requests')->name('destroy');
});

Route::group(["prefix" => "admin/static-links"], function () {
    Route::get('/', [\App\Http\Controllers\Admin\StaticLinksController::class, 'index'])->name('admin.static_links.index');
    Route::post('/store', [\App\Http\Controllers\Admin\StaticLinksController::class, 'store'])->name('admin.static_links.store');
    Route::put('/update/{id}', [\App\Http\Controllers\Admin\StaticLinksController::class, 'update'])->name('admin.static_links.update');
    Route::delete('/delete/{id}', [\App\Http\Controllers\Admin\StaticLinksController::class, 'destroy'])->name('admin.static_links.destroy');
});

// l5-swagger UI lives at /docs; keep old bookmarks working
Route::permanentRedirect('api/documentation', '/docs');
