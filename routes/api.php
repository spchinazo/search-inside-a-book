<?php

use Illuminate\Http\Request;
use App\Http\Controllers\BookSearchController;

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

// Book search endpoints
Route::get('/book/search', [BookSearchController::class, 'search'])->name('api.book.search');
Route::get('/book/page/{pageNumber}', [BookSearchController::class, 'getPage'])->name('api.book.page');
Route::get('/book/stats', [BookSearchController::class, 'stats'])->name('api.book.stats');
