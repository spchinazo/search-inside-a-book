<?php

use App\Http\Controllers\Api\BookController;
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

Route::prefix('books')->group(function () {
    Route::get('{book}/search', [BookController::class, 'search'])
        ->missing(function () {
            return response()->json([
                'error' => 'Book not found',
                'message' => 'El libro solicitado no existe.'
            ], 404);
        });

    Route::get('{book}/pages/{pageNumber}', [BookController::class, 'getPage'])
        ->missing(function () {
            return response()->json([
                'error' => 'Book not found',
                'message' => 'El libro solicitado no existe.'
            ], 404);
        });
});
