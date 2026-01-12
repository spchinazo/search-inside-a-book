<?php

namespace App\Console\Commands;

use App\Models\Page;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

use function count;

class ImportBookPages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'book:import {file=storage/exercise-files/Eloquent_JavaScript.json}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importar páginas de un libro desde un archivo JSON a la base de datos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = base_path($this->argument('file'));

        if (! File::exists($filePath)) {
            $this->error("Archivo no encontrado: {$filePath}");

            return 1;
        }

        $this->info("Leyendo archivo JSON desde {$filePath}...");
        $pages = json_decode(File::get($filePath), true);

        if (! $pages) {
            $this->error('Error al decodificar el archivo JSON.');

            return 1;
        }

        $this->info('Se encontraron '.count($pages).' páginas. Importando a la base de datos...');

        // Usar transaction por si algo falla
        DB::transaction(function () use ($pages) {
            Page::truncate(); // Limpiar tabla antes de importar, asumiendo que es un solo libro.

            $bar = $this->output->createProgressBar(count($pages));
            $bar->start();

            // Insertar en chunks para mejorar rendimiento
            collect($pages)->chunk(100)->each(function ($chunk) use ($bar) {
                $data = $chunk->map(fn ($page) => [
                    'page_number' => $page['page'],
                    'text_content' => $page['text_content'],
                ])->toArray();

                Page::insert($data);
                $bar->advance($chunk->count());
            });

            $bar->finish();
        });

        $this->newLine();
        $this->info('Importación completada con éxito.');

        return 0;
    }
}
