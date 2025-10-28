<?php
// clean_utf8_json.php
// Uso: php clean_utf8_json.php

$input = 'storage/exercise-files/Eloquent_JavaScript.json';
$output = 'storage/exercise-files/Eloquent_JavaScript_clean.json';

$json = file_get_contents($input);
// Remove BOM se existir
$json = preg_replace('/\xEF\xBB\xBF/', '', $json);
// Decodifica ignorando caracteres inválidos
$data = json_decode($json, true, 512, JSON_INVALID_UTF8_IGNORE);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "Erro ao decodificar JSON: ".json_last_error_msg()."\n";
    exit(1);
}
// Re-encoda para UTF-8 puro
$clean = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
file_put_contents($output, $clean);
echo "Arquivo limpo salvo em $output\n";
