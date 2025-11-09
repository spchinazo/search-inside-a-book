<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'page',
        'text_content',
    ];
    /**
     * Extrae un fragmento de contexto alrededor del término buscado.
     *
     * @param string $text Texto completo de la página
     * @param string $term Término a buscar
     * @param int $contextLength Cantidad de caracteres antes y después
     * @return string Fragmento de contexto con el término resaltado
     */
    public static function extractSnippet($text, $term, $contextLength = 30)
    {
        $pos = stripos($text, $term);
        if ($pos === false) {
            return '';
        }
        $start = max(0, $pos - $contextLength);
        $length = strlen($term) + $contextLength * 2;
        $snippet = substr($text, $start, $length);
    // Resaltar el término encontrado
        return preg_replace('/(' . preg_quote($term, '/') . ')/i', '<mark>$1</mark>', $snippet);
    }
}
