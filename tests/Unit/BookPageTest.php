<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\BookPage;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookPageTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_has_fillable_attributes(): void
    {
        $page = new BookPage();
        
        $this->assertEquals(
            ['book_id', 'page_number', 'text_content'],
            $page->getFillable()
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_implements_should_queue(): void
    {
        $page = new BookPage();
        
        $this->assertInstanceOf(
            \Illuminate\Contracts\Queue\ShouldQueue::class,
            $page
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_has_searchable_trait(): void
    {
        $this->assertTrue(
            method_exists(BookPage::class, 'search')
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_uses_default_queue(): void
    {
        $page = new BookPage();
        
        $this->assertEquals('default', $page->queue);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_casts_searchable_array_values_to_integers(): void
    {
        $book = Book::factory()->create();
        $page = BookPage::factory()->create([
            'book_id' => $book->id,
            'page_number' => '5', // String
        ]);

        $searchable = $page->toSearchableArray();

        $this->assertIsInt($searchable['id']);
        $this->assertIsInt($searchable['book_id']);
        $this->assertIsInt($searchable['page_number']);
    }
}