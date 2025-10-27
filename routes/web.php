<?php

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

// API Documentation
Route::get('/docs', function () {
    return view('swagger');
})->name('swagger.ui');

Route::get('/docs.json', function () {
    return response()->file(storage_path('api-docs/api-docs.json'));
})->name('swagger.json');

Route::get('/docs.yaml', function () {
    return response()->file(storage_path('api-docs/api-docs.yaml'));
})->name('swagger.yaml');
