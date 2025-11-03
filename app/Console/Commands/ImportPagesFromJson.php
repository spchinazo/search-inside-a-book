<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportPagesFromJson extends Command
{
    /**
     * El nombre y la firma del comando de consola.
     *
     * @var string
     */
    protected $signature = 'app:import-pages-from-json {--file= : Ruta del archivo JSON (opcional)}';

    /**
     * La descripción del comando de consola.
     *
     * @var string
     */
    protected $description = 'Importa páginas desde un archivo JSON a la tabla pages.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->option('file') ?: storage_path('exercise-files/Eloquent_JavaScript_clean.json');
        if (!file_exists($file)) {
            $this->error("Archivo JSON no encontrado: $file");
            return 1;
        }

        $json = file_get_contents($file);
        // Forzar UTF-8 en el contenido bruto
        $json = mb_convert_encoding($json, 'UTF-8', 'UTF-8');
        $pages = json_decode($json, true);
        if (!is_array($pages)) {
            $this->error('Error al decodificar el JSON: ' . json_last_error_msg());
            return 1;
        }

        $count = 0;
        foreach ($pages as $pagina) {
            if (!isset($pagina['page']) || !isset($pagina['text_content'])) {
                continue;
            }
            // Forzar UTF-8 en el texto antes de guardar
            $text_content = mb_convert_encoding($pagina['text_content'], 'UTF-8', 'UTF-8');
            \App\Models\Page::updateOrCreate(
                ['page' => $pagina['page']],
                ['text_content' => $text_content]
            );
            $count++;
        }
        $this->info("Importación completada. Total de páginas importadas: $count");
        return 0;
    }
}
