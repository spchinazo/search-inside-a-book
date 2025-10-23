<?php

namespace Database\Seeders;

use App\Book;
use App\BookPage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the book
        $book = Book::create([
            'title' => 'Eloquent JavaScript',
            'author' => 'Marijn Haverbeke',
            'description' => 'A Modern Introduction to Programming - 3rd Edition',
        ]);

        // Load and parse the JSON file
        $jsonPath = storage_path('exercise-files/Eloquent_JavaScript.json');
        
        if (!file_exists($jsonPath)) {
            $this->command->error('Eloquent_JavaScript.json file not found in storage/exercise-files/');
            return;
        }

        $pagesData = json_decode(file_get_contents($jsonPath), true);

        if (!$pagesData) {
            $this->command->error('Failed to parse JSON file');
            return;
        }

        $this->command->info('Loading ' . count($pagesData) . ' pages...');

        // Create book pages
        foreach ($pagesData as $pageData) {
            BookPage::create([
                'book_id' => $book->id,
                'page_number' => $pageData['page'],
                'text_content' => $pageData['text_content'],
            ]);
        }

        $this->command->info('Book and pages loaded successfully!');
    }
}
