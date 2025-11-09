<?php

use Illuminate\Http\Request;
use App\Http\Controllers\SearchWebController;

// API: Busca páginas por término
Route::get('/search', [SearchWebController::class, 'apiSearch']);
// API: Devuelve el contenido completo de una página
Route::get('/page/{numero}', [SearchWebController::class, 'apiPage']);
