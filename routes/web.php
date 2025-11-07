<?php

use Illuminate\Support\Facades\View;
use App\Http\Controllers\SearchWebController;

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
// Dashboard principal
Route::get('/dashboard', function () {
	return View::make('dashboard');
})->name('dashboard');

Route::get('/', [SearchWebController::class, 'index'])->name('search.index');
Route::get('/web/page/{numero}', [SearchWebController::class, 'show'])->name('search.show');
