<?php

use App\Http\Controllers\Api\BookSearchController;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\BookPage;

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

Route::get('/books/{book}/search', [BookSearchController::class, 'search'])->name('books.search');

Route::get('/pages/{page}', [BookSearchController::class, 'showPage'])->name('pages.show');

Route::get('/test', function () {
    return response()->json(['status' => 'API is running']);
});
