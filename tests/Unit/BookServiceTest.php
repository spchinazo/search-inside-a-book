<?php

namespace Tests\Unit;

use App\Book;
use App\BookPage;
use App\Services\BookService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $bookService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bookService = new BookService();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_get_a_page_from_a_book()
    {
        $book = Book::create([
            'title' => 'Eloquent JavaScript',
            'author' => 'Marijn Haverbeke',
            'total_pages' => 5,
            'file_path' => 'path/to/file.json',
        ]);

        $page = BookPage::create([
            'book_id' => $book->id,
            'page_number' => 3,
            'content' => 'This is the content of page 3.',
        ]);

        $result = $this->bookService->getPage($book, 3);

        $this->assertEquals($page->id, $result->id);
        $this->assertEquals(3, $result->page_number);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_null_if_page_does_not_exist()
    {
        $book = Book::create([
            'title' => 'Eloquent JavaScript',
            'author' => 'Marijn Haverbeke',
            'total_pages' => 5,
            'file_path' => 'path/to/file.json',
        ]);

        $result = $this->bookService->getPage($book, 10);

        $this->assertNull($result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_format_matches_positions()
    {
        $matchesPosition = [
            'content' => [
                ['start' => 10, 'length' => 5],
                ['start' => 20, 'length' => 3],
            ]
        ];

        $formatted = $this->bookService->formatMatches($matchesPosition);

        $this->assertCount(2, $formatted);
        $this->assertEquals(10, $formatted[0]['start']);
        $this->assertEquals(5, $formatted[0]['length']);
    }
}
