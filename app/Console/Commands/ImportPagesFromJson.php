<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportPagesFromJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-pages-from-json {--file= : Caminho do arquivo JSON (opcional)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa páginas do arquivo JSON para a tabela pages.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->option('file') ?: storage_path('exercise-files/Eloquent_JavaScript_clean.json');
        if (!file_exists($file)) {
            $this->error("Arquivo JSON não encontrado: $file");
            return 1;
        }

        $json = file_get_contents($file);
        // Forçar UTF-8 no conteúdo bruto
        $json = mb_convert_encoding($json, 'UTF-8', 'UTF-8');
        $pages = json_decode($json, true);
        if (!is_array($pages)) {
            $this->error('Erro ao decodificar o JSON: ' . json_last_error_msg());
            return 1;
        }

        $count = 0;
        foreach ($pages as $pagina) {
            if (!isset($pagina['page']) || !isset($pagina['text_content'])) {
                continue;
            }
            // Forçar UTF-8 no texto antes de salvar
            $text_content = mb_convert_encoding($pagina['text_content'], 'UTF-8', 'UTF-8');
            \App\Models\Page::updateOrCreate(
                ['page' => $pagina['page']],
                ['text_content' => $text_content]
            );
            $count++;
        }
        $this->info("Importação concluída. Total de páginas importadas: $count");
        return 0;
    }
}
