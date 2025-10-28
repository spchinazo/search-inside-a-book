<?php
// Script para limpar e regravar o arquivo JSON forçando UTF-8 puro
// Uso: php clean_json.php

$input = __DIR__ . '/storage/exercise-files/Eloquent_JavaScript.json';
$output = $input . '.cleaned';

$json = file_get_contents($input);
if ($json === false) {
    die("Erro ao ler o arquivo original.\n");
}
// Remove BOM se existir
$json = preg_replace('/\xEF\xBB\xBF/', '', $json);
// Remove caracteres de controle inválidos
$json = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $json);
// Força UTF-8 ignorando inválidos
$json = iconv('UTF-8', 'UTF-8//IGNORE', $json);
// Valida se é um JSON válido
$data = json_decode($json, true);
if ($data === null) {
    die("Ainda há erro de JSON: " . json_last_error_msg() . "\n");
}
// Regrava o arquivo limpo
file_put_contents($output, json_encode($data, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
echo "Arquivo limpo salvo em: $output\n";
