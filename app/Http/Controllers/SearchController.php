<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SearchController extends Controller
{
    /**
     * Retorna o conteúdo completo de uma página pelo número.
     * @param int $numero
     * @return \Illuminate\Http\JsonResponse
     */
    public function pagina($numero)
    {
        $path = storage_path('exercise-files/Eloquent_JavaScript_clean.json');
        if (!file_exists($path)) {
            return response()->json([
                'error' => 'Archivo de datos no encontrado.'
            ], 500);
        }
        $json = file_get_contents($path);
        $pages = json_decode($json, true);
        if (!is_array($pages)) {
            return response()->json([
                'error' => 'Error al leer el archivo JSON: ' . json_last_error_msg()
            ], 500);
        }
        foreach ($pages as $pagina) {
            if (isset($pagina['page']) && $pagina['page'] == $numero) {
                // Forçar UTF-8 válido
                $pagina['text_content'] = mb_convert_encoding($pagina['text_content'], 'UTF-8', 'UTF-8');
                return response()->json($pagina);
            }
        }
        return response()->json([
            'error' => 'Página no encontrada.'
        ], 404);
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

            $path = storage_path('exercise-files/Eloquent_JavaScript_clean.json');
            if (!file_exists($path)) {
                return response()->json([
                    'error' => 'Archivo de datos no encontrado.'
                ], 500);
            }


            $json = file_get_contents($path);
            $pages = json_decode($json, true);
            if (!is_array($pages)) {
                \Log::error('Falha ao decodificar JSON', [
                    'json_error' => json_last_error_msg(),
                    'json_sample' => substr($json, 0, 200)
                ]);
                return response()->json([
                    'error' => 'Error al leer el archivo JSON: ' . json_last_error_msg()
                ], 500);
            }

            $resultados = [];
            foreach ($pages as $pagina) {
                if (isset($pagina['text_content']) && stripos($pagina['text_content'], $query) !== false) {
                    // Extraer un fragmento de contexto
                    $texto = $pagina['text_content'];
                    $pos = stripos($texto, $query);
                    $contexto = substr($texto, max(0, $pos - 30), strlen($query) + 60);
                    // Forçar UTF-8 válido
                    $contexto = mb_convert_encoding($contexto, 'UTF-8', 'UTF-8');
                    $resultados[] = [
                        'pagina' => $pagina['page'] ?? null,
                        'contexto' => $contexto
                    ];
                }
            }

            return response()->json([
                'resultados' => $resultados,
                'total' => count($resultados)
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
