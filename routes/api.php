<?php

use Illuminate\Http\Request;

use App\Http\Controllers\SearchController;

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

// Ruta de búsqueda de términos en el libro
Route::get('/search', [SearchController::class, 'search']);
// Ruta para obtener el contenido completo de una página
Route::get('/page/{numero}', [SearchController::class, 'pagina']);
