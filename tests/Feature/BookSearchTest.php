<?php

namespace Tests\Feature;

use App\Book;
use App\BookPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BookSearchTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_search_in_a_book_with_highlights()
    {
        $book = Book::create([
            'title' => 'Eloquent JavaScript',
            'author' => 'Marijn Haverbeke',
            'total_pages' => 1,
            'file_path' => 'storage/exercise-files/Eloquent_JavaScript.json',
        ]);

        $page = BookPage::create([
            'id' => 1,
            'book_id' => $book->id,
            'page_number' => 1,
            'content' => 'JavaScript is a language for everything.',
        ]);

        // Mock Meilisearch results
        $mockResults = [
            'hits' => [
                [
                    'id' => 1,
                    'book_id' => $book->id,
                    'page_number' => 1,
                    'content' => 'JavaScript is a language for everything.',
                    '_formatted' => [
                        'content' => '<mark>JavaScript</mark> is a language for everything.'
                    ],
                    '_matchesPosition' => [
                        'content' => [
                            ['start' => 0, 'length' => 10]
                        ]
                    ]
                ]
            ],
            'total' => 1,
            'page' => 1,
            'per_page' => 10
        ];
        
        $this->mock(\App\Services\BookService::class, function ($mock) use ($book, $mockResults) {
            $mock->shouldReceive('search')
                ->once()
                ->andReturn($mockResults);
            
            $mock->shouldReceive('formatMatches')
                ->once()
                ->andReturn([['start' => 0, 'length' => 10]]);
        });

        $response = $this->getJson("/api/books/{$book->id}/search?q=JavaScript");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'current_page',
                'total',
                'per_page'
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_empty_data_if_no_results_found()
    {
        $book = Book::create([
            'title' => 'Eloquent JavaScript',
            'author' => 'Marijn Haverbeke',
            'total_pages' => 1,
            'file_path' => 'storage/exercise-files/Eloquent_JavaScript.json',
        ]);

        // Mock empty results from BookService
        $this->mock(\App\Services\BookService::class, function ($mock) {
            $mock->shouldReceive('search')
                ->once()
                ->andReturn([
                    'hits' => [],
                    'total' => 0,
                    'page' => 1,
                    'per_page' => 10
                ]);
        });

        $response = $this->getJson("/api/books/{$book->id}/search?q=NoExistente");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [],
                'total' => 0,
                'current_page' => 1
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_search_with_typo_tolerance()
    {
        $book = Book::create([
            'title' => 'Eloquent JavaScript',
            'author' => 'Marijn Haverbeke',
            'total_pages' => 1,
            'file_path' => 'storage/exercise-files/Eloquent_JavaScript.json',
        ]);

        // Mock Meilisearch fuzzy result (e.g., searching for "JavaSrpit" instead of "JavaScript")
        $mockResults = [
            'hits' => [
                [
                    'id' => 1,
                    'book_id' => $book->id,
                    'page_number' => 1,
                    'content' => 'JavaScript is a language for everything.',
                    '_formatted' => [
                        'content' => '<mark>JavaScript</mark> is a language for everything.'
                    ],
                    '_matchesPosition' => [
                        'content' => [
                            ['start' => 0, 'length' => 10]
                        ]
                    ]
                ]
            ],
            'total' => 1,
            'page' => 1,
            'per_page' => 10
        ];

        $this->mock(\App\Services\BookService::class, function ($mock) use ($mockResults) {
            $mock->shouldReceive('search')
                ->once()
                ->andReturn($mockResults);
            
            $mock->shouldReceive('formatMatches')
                ->once()
                ->andReturn([['start' => 0, 'length' => 10]]);
        });

        $response = $this->getJson("/api/books/{$book->id}/search?q=JavaSrpit");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        'page_number' => 1,
                        'snippet' => '<mark>JavaScript</mark> is a language for everything.',
                    ]
                ],
                'total' => 1
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_get_a_specific_page()
    {
        $book = Book::create([
            'title' => 'Eloquent JavaScript',
            'author' => 'Marijn Haverbeke',
            'total_pages' => 1,
            'file_path' => 'storage/exercise-files/Eloquent_JavaScript.json',
        ]);

        $page = BookPage::create([
            'book_id' => $book->id,
            'page_number' => 1,
            'content' => 'JavaScript is a language for everything.',
        ]);

        $response = $this->getJson("/api/books/{$book->id}/pages/1");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'page_number' => 1,
                    'content' => 'JavaScript is a language for everything.',
                ]
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_404_if_book_does_not_exist()
    {
        $response = $this->getJson("/api/books/999/search?q=test");

        $response->assertStatus(404)
            ->assertJson([
                'error' => 'Book not found'
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_404_if_page_does_not_exist()
    {
        $book = Book::create([
            'title' => 'Eloquent JavaScript',
            'author' => 'Marijn Haverbeke',
            'total_pages' => 1,
            'file_path' => 'storage/exercise-files/Eloquent_JavaScript.json',
        ]);

        $response = $this->getJson("/api/books/{$book->id}/pages/99");

        $response->assertStatus(404)
            ->assertJson([
                'error' => 'Page not found'
            ]);
    }
}
