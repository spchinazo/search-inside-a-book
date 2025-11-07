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
     * Extrai um trecho de contexto ao redor do termo buscado.
     *
     * @param string $text Texto completo da página
     * @param string $term Termo a ser buscado
     * @param int $contextLength Quantidade de caracteres antes e depois
     * @return string Trecho de contexto com o termo destacado
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
        // Destacar o termo encontrado
        return preg_replace('/(' . preg_quote($term, '/') . ')/i', '<mark>$1</mark>', $snippet);
    }
}
