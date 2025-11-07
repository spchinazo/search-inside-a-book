<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Page;

class SearchController extends Controller
{
    /**
     * Retorna o conteúdo completo de uma página pelo número.
     * @param int $numero
     * @return \Illuminate\Http\JsonResponse
     */
    public function pagina($numero)
    {
        $pagina = Page::where('page', $numero)->first();
        if (!$pagina) {
            return response()->json([
                'error' => 'Página no encontrada.'
            ], 404);
        }
        // Forçar UTF-8 válido
        $pagina->text_content = mb_convert_encoding($pagina->text_content, 'UTF-8', 'UTF-8');
        return response()->json($pagina);
    }
    /**
     * Realiza una búsqueda de un término en el libro Eloquent JavaScript.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        try {
            $query = $request->input('query');
            if (!$query) {
                return response()->json([
                    'error' => 'Debe proporcionar un término de búsqueda en el parámetro "query".'
                ], 400);
            }

            // Parâmetros de paginação
            $page = (int) $request->input('page', 1);
            $perPage = (int) $request->input('per_page', 10);
            $offset = ($page - 1) * $perPage;

            // Busca no banco
            $queryBuilder = Page::where('text_content', 'ILIKE', "%$query%")
                ->orderBy('page');

            $total = $queryBuilder->count();
            $resultados = $queryBuilder->offset($offset)->limit($perPage)->get();

            // Montar resposta com fragmento de contexto
            $response = [];
            foreach ($resultados as $pagina) {
                $texto = $pagina->text_content;
                $pos = stripos($texto, $query);
                $contexto = $pos !== false ? substr($texto, max(0, $pos - 30), strlen($query) + 60) : '';
                $contexto = mb_convert_encoding($contexto, 'UTF-8', 'UTF-8');
                $response[] = [
                    'pagina' => $pagina->page,
                    'contexto' => $contexto
                ];
            }

            return response()->json([
                'resultados' => $response,
                'total' => $total,
                'pagina_atual' => $page,
                'por_pagina' => $perPage
            ]);
        } catch (\Throwable $e) {
            \Log::error('Error en SearchController: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'error' => 'Error interno en el servidor.',
                'exception' => $e->getMessage(),
            ], 500);
        }
    }
}
