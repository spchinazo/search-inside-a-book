<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Book;
use App\Models\BookPage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookPageModelTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_belongs_to_a_book(): void
    {
        $book = Book::factory()->create(['title' => 'Test Book']);
        $page = BookPage::factory()->create(['book_id' => $book->id]);

        $this->assertInstanceOf(Book::class, $page->book);
        $this->assertEquals('Test Book', $page->book->title);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_correct_scout_key(): void
    {
        $page = BookPage::factory()->create(['id' => 123]);

        $this->assertEquals(123, $page->getScoutKey());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_converts_to_searchable_array_correctly(): void
    {
        $book = Book::factory()->create();
        $page = BookPage::factory()->create([
            'book_id' => $book->id,
            'page_number' => 5,
            'text_content' => 'Sample content',
        ]);

        $searchable = $page->toSearchableArray();

        $this->assertIsArray($searchable);
        $this->assertArrayHasKey('id', $searchable);
        $this->assertArrayHasKey('book_id', $searchable);
        $this->assertArrayHasKey('page_number', $searchable);
        $this->assertArrayHasKey('text_content', $searchable);
        
        $this->assertIsInt($searchable['id']);
        $this->assertIsInt($searchable['book_id']);
        $this->assertIsInt($searchable['page_number']);
        $this->assertEquals(5, $searchable['page_number']);
        $this->assertEquals('Sample content', $searchable['text_content']);
    }
}