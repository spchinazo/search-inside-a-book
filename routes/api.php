<?php

use Illuminate\Http\Request;
use App\Http\Controllers\SearchWebController;

// API: Busca páginas pelo termo
Route::get('/search', [SearchWebController::class, 'apiSearch']);
// API: Retorna o conteúdo completo de uma página
Route::get('/page/{numero}', [SearchWebController::class, 'apiPage']);
