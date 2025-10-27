<?php

use App\Http\Controllers\Api\BookSearchController;
use App\Http\Controllers\Api\HealthController;
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

// Search route: mais agressivo pois consome Meilisearch
Route::middleware('throttle:60,1')->group(function () {
    // Search within a specific book
    Route::get('/books/{book}/search', [BookSearchController::class, 'search'])->name('books.search');
});

// Read-only routes: rate limiting mais permissivo pois usam cache
Route::middleware('throttle:120,1')->group(function () {
    // List all books (uses 60min cache)
    Route::get('/books', [BookSearchController::class, 'index'])->name('books.index');
    
    // Full page content (uses 60min cache)
    Route::get('/pages/{page}', [BookSearchController::class, 'showPage'])->name('pages.show');

    Route::get('/health', HealthController::class)->name('health');
});