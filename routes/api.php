<?php

use App\Http\Controllers\Api\SearchController;
use Illuminate\Http\Request;

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

// Book search API routes
Route::get('/book', [SearchController::class, 'getBook']);
Route::get('/search', [SearchController::class, 'search']);
Route::get('/page/{pageId}', [SearchController::class, 'getPage']);
Route::get('/page-number/{pageNumber}', [SearchController::class, 'getPageByNumber']);
