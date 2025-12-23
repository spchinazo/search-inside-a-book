<?php

use App\Http\Controllers\SearchController;

Route::get('/', [SearchController::class, 'index'])->name('search.index');
Route::get('/page/{page}', [SearchController::class, 'show'])->name('page.show');
