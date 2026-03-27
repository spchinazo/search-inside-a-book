<?php

use Illuminate\Http\Request;
use App\Http\Controllers\SearchController;

Route::get('/search', [SearchController::class, 'search']);
Route::get('/search/suggest', [SearchController::class, 'suggest']);
Route::get('/pages/{page}', [SearchController::class, 'page'])->whereNumber('page');
