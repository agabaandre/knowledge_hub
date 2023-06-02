<?php

use App\Http\Controllers\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Subscription Routes
Route::post('/subscribe', [Subscription::class, 'subscribe']);
Route::post('/unsubscribe', [Subscription::class, 'unsubscribe']);
