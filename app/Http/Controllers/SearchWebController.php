<?php


namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Page;

class SearchWebController extends Controller
{
    /**
     * Exibe o formulário de busca e os resultados (se houver).
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
     * Exibe o conteúdo completo de uma página.
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
     * Retorna um fragmento de contexto com o termo destacado.
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
