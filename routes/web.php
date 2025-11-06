<?php

use App\Http\Controllers\DocumentController;

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

Route::get('/', function () {
    return view('welcome');
});

// Document routes
Route::get('/api/documents/pdf', [DocumentController::class, 'getPdf'])->name('documents.pdf');
Route::get('/api/documents/info', [DocumentController::class, 'getDocumentInfo'])->name('documents.info');
Route::get('/api/documents/search', [DocumentController::class, 'search'])->name('documents.search');