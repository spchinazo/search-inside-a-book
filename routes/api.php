<?php

use App\Http\Controllers\Api\BookSearchController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\SwaggerController;
use Illuminate\Support\Facades\Route;

// Search route
Route::middleware('throttle:60,1')->group(function () {
    Route::get('/books/{book}/search', [BookSearchController::class, 'search'])->name('books.search');
});

// Read-only routes
Route::middleware('throttle:120,1')->group(function () {
    Route::get('/books', [BookSearchController::class, 'index'])->name('books.index');

    Route::get('/books/{book}/pages/{pageNumber}', [BookSearchController::class, 'showPage'])
        ->name('books.pages.show');

    Route::get('/health', HealthController::class)->name('health');
});

// Swagger Documentation
Route::get('/docs', [SwaggerController::class, 'index'])->name('api.swagger');
Route::get('/docs/json', [SwaggerController::class, 'json'])->name('api.swagger.json');