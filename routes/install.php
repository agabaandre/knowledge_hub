<?php

use App\Http\Controllers\InstallController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'install.guest'])->prefix('install')->name('install.')->group(function () {
    Route::get('/', [InstallController::class, 'index'])->name('index');
    Route::get('/database', [InstallController::class, 'showDatabase'])->name('database');
    Route::post('/database', [InstallController::class, 'storeDatabase'])->name('database.store');
    Route::get('/storage', [InstallController::class, 'showStorage'])->name('storage');
    Route::post('/storage', [InstallController::class, 'storeStorage'])->name('storage.store');
    Route::get('/site', [InstallController::class, 'showSite'])->name('site');
    Route::post('/site', [InstallController::class, 'storeSite'])->name('site.store');
    Route::get('/central-hub', [InstallController::class, 'showCentralHub'])->name('central');
    Route::post('/central-hub', [InstallController::class, 'storeCentralHub'])->name('central.store');
    Route::post('/central-hub/test', [InstallController::class, 'testCentralHub'])->name('central.test');
    Route::get('/moodle', [InstallController::class, 'showMoodle'])->name('moodle');
    Route::post('/moodle', [InstallController::class, 'storeMoodle'])->name('moodle.store');
    Route::post('/moodle/test', [InstallController::class, 'testMoodle'])->name('moodle.test');
    Route::post('/moodle/test-frappe', [InstallController::class, 'testFrappe'])->name('moodle.test-frappe');
    Route::post('/moodle/test-openedx', [InstallController::class, 'testOpenEdx'])->name('moodle.test-openedx');
    Route::get('/mail', [InstallController::class, 'showMail'])->name('mail');
    Route::post('/mail/test', [InstallController::class, 'testMail'])->name('mail.test');
    Route::post('/mail', [InstallController::class, 'storeMail'])->name('mail.store');
    Route::get('/sso', [InstallController::class, 'showSso'])->name('sso');
    Route::post('/sso', [InstallController::class, 'storeSso'])->name('sso.store');
    Route::get('/admin', [InstallController::class, 'showAdmin'])->name('admin');
    Route::post('/admin', [InstallController::class, 'storeAdmin'])->name('admin.store');
    Route::get('/complete', [InstallController::class, 'complete'])->name('complete');
});
