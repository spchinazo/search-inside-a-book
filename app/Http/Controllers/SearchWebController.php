<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Page;

class SearchWebController extends Controller
{
    /**
     * API: Busca páginas por término y devuelve JSON.
     */
    public function apiSearch(Request $request)
    {
        $query = $request->input('query');
        $results = [];
        $pagination = null;
        if ($query) {
            $page = $request->input('page', 1);
            $perPage = 10;
            $paginator = Page::where('text_content', 'ILIKE', "%$query%")
                ->orderBy('page')
                ->paginate($perPage, ['*'], 'page', $page);
            foreach ($paginator->items() as $item) {
                $context = $this->getContextFragment($item->text_content, $query);
                $results[] = [
                    'pagina' => $item->page,
                    'contexto' => $context,
                ];
            }
            $pagination = [
                'total' => $paginator->total(),
                'pagina_atual' => $paginator->currentPage(),
                'por_pagina' => $paginator->perPage(),
            ];
        }
        return response()->json([
            'resultados' => $results,
        ] + ($pagination ?? []));
    }

    /**
     * API: Devuelve el contenido completo de una página en JSON.
     */
    public function apiPage(Request $request, $numero)
    {
        $page = Page::where('page', $numero)->first();
        if ($page) {
            return response()->json([
                'page' => $page->page,
                'text_content' => $page->text_content,
            ]);
        }
        return response()->json(['error' => 'Pagina no encontrada'], 404);
    }
    /**
     * Muestra el formulario de búsqueda y los resultados (si existen).
     */
    public function index(Request $request)
    {
        $query = $request->input('query');
        $results = null;
        $pagination = null;

        if ($query) {
            $page = $request->input('page', 1);
            $perPage = 5;
            $paginator = Page::where('text_content', 'ILIKE', "%$query%")
                ->orderBy('page')
                ->paginate($perPage, ['*'], 'page', $page);
            $results = [];
            foreach ($paginator->items() as $item) {
                // Extrai um fragmento de contexto
                $context = $this->getContextFragment($item->text_content, $query);
                $results[] = [
                    'page' => $item->page,
                    'context' => $context,
                ];
            }
            $pagination = [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
            ];
        }
        return view('search', compact('query', 'results', 'pagination'));
    }

    /**
     * Muestra el contenido completo de una página.
     */
    public function show(Request $request, $numero)
    {
        $page = Page::where('page', $numero)->first();
        if ($page) {
            return view('page', ['page' => $page]);
        }
        abort(404, 'Página no encontrada');
    }

    /**
     * Devuelve un fragmento de contexto con el término resaltado.
     */
    private function getContextFragment($text, $term, $contextLength = 60)
    {
        $pos = stripos($text, $term);
        if ($pos === false) return '';
        $start = max(0, $pos - $contextLength / 2);
        $fragment = mb_substr($text, $start, $contextLength);
        // Destaca o termo
        return str_ireplace($term, '<mark>' . $term . '</mark>', $fragment);
    }
}
