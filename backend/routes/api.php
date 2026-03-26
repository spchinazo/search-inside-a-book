<?php

use Illuminate\Http\Request;
use App\Http\Controllers\SearchController;

Route::get('/search', [SearchController::class, 'search']);
Route::get('/pages/{page}', [SearchController::class, 'page'])->whereNumber('page');
