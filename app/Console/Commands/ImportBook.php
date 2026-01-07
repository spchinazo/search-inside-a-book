<?php

namespace App\Console\Commands;

use App\Book;
use App\BookPage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImportBook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'book:import {file : The path to the JSON file} 
                            {--title= : The title of the book} 
                            {--author= : The author of the book}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import book content from a JSON file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('file');

        if (!File::exists($filePath)) {
            $this->error("El archivo no existe: {$filePath}");
            return 1;
        }

        $jsonContent = File::get($filePath);
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error("Error al decodificar el JSON: " . json_last_error_msg());
            return 1;
        }

        $title = $this->option('title') ?? File::name($filePath);
        $author = $this->option('author') ?? 'Unknown';

        $this->info("Importando el libro: {$title} de {$author}...");

        $book = Book::create([
            'title' => $title,
            'author' => $author,
            'total_pages' => count($data),
            'file_path' => $filePath,
        ]);

        $bar = $this->output->createProgressBar(count($data));
        $bar->start();

        foreach ($data as $pageData) {
            BookPage::create([
                'book_id' => $book->id,
                'page_number' => $pageData['page'],
                'content' => $pageData['text_content'],
            ]);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Importación completada con éxito.");

        return 0;
    }
}
