<?php

use App\Http\Controllers\InstallController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'install.guest'])->prefix('install')->name('install.')->group(function () {
    Route::get('/', [InstallController::class, 'index'])->name('index');
    Route::get('/database', [InstallController::class, 'showDatabase'])->name('database');
    Route::post('/database', [InstallController::class, 'storeDatabase'])->name('database.store');
    Route::get('/site', [InstallController::class, 'showSite'])->name('site');
    Route::post('/site', [InstallController::class, 'storeSite'])->name('site.store');
    Route::get('/mail', [InstallController::class, 'showMail'])->name('mail');
    Route::post('/mail', [InstallController::class, 'storeMail'])->name('mail.store');
    Route::get('/admin', [InstallController::class, 'showAdmin'])->name('admin');
    Route::post('/admin', [InstallController::class, 'storeAdmin'])->name('admin.store');
    Route::get('/complete', [InstallController::class, 'complete'])->name('complete');
});
