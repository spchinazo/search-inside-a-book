<?php

namespace App\Console\Commands;

use App\Book;
use App\BookPage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportBooks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'books:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $files = Storage::disk('exercise')->files('/');
        $book_pages = [];

        foreach ($files as $file) {
            if (!str_ends_with($file, '.json')) continue;

            $book_name = pathinfo($file, PATHINFO_FILENAME);

            $book_name = Str::replace('_', ' ', $book_name);
            $book_name = Str::title($book_name);
            $book_slug = Str::slug($book_name);

            Book::create([
                'title' => $book_name,
                'slug' => $book_slug
            ]);
        }

        //Solo hay un libro por eso obtenemos el primero
        $book = Book::first();

        foreach ($files as $file) {
            if (!str_ends_with($file, '.json')) continue;

            $book_name = pathinfo($file, PATHINFO_FILENAME);
            $book_slug = Str::slug($book_name);

            $json = json_decode(Storage::disk('exercise')->get($file), true);

            foreach ($json as $item) {
                $book_pages[] = [
                    'book_id' => $book->id,
                    'page' => $item['page'],
                    'content' => $item['text_content'],
                ];
            }
        }

        BookPage::insert($book_pages);
    }
}
