<?php

namespace Database\Seeders\Data;

use App\Exceptions\GenericException;
use App\Models\Book;
use App\Models\BookPage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BooksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = [
            [
                'title' => 'Eloquent JavaScript',
                'lang'  => 'en',
                'isbn'  => '1234567890',
                'path'  => 'books/1.pdf',
                'disk'  => 'exercise-files',
                'front' => 'Eloquent_JavaScript_pages/page-001.png',
                'pages_route' => 'storage/exercise-files/Eloquent_JavaScript.json'
            ]
        ];
        try {
            DB::beginTransaction();
            foreach ($books as $bookAssoc) {
                $book = Book::create([
                    'title' => $bookAssoc['title'],
                    'lang'  => $bookAssoc['lang'],
                    'isbn'  => $bookAssoc['isbn'],
                    'path'  => $bookAssoc['path'],
                    'front' => $bookAssoc['front'],
                    'disk'  => $bookAssoc['disk'],
                ]);
    
                $json = base_path($bookAssoc['pages_route']);
                $pages = json_decode(file_get_contents($json), true);
                if (!$pages) {
                    throw new GenericException('No se pudo leer el archivo de paginas', 404);
                }
                foreach ($pages as $page) {
                    $pageNumber = $page['page'];

                    $filename = 'page-' . str_pad($pageNumber, 3, '0', STR_PAD_LEFT) . '.png';
                    BookPage::create([
                        'book_id' => $book->id,
                        'page_number' => $pageNumber,
                        'content' => $page['text_content'],
                        'path' => 'Eloquent_JavaScript_pages/'.$filename,
                        'disk' => $book->disk,
                        'status' => 'processed',
                    ]);
                }
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

    }
}
