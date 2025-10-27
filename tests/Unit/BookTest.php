<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Book;
use App\Models\BookPage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_has_fillable_attributes(): void
    {
        $book = new Book();

        $this->assertEquals(
            ['title', 'author', 'description'],
            $book->getFillable()
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_create_a_book_with_fillable_attributes(): void
    {
        $book = Book::create([
            'title' => 'Test Book',
            'author' => 'Test Author',
            'description' => 'Test Description',
        ]);

        $this->assertDatabaseHas('books', [
            'title' => 'Test Book',
            'author' => 'Test Author',
            'description' => 'Test Description',
        ]);

        $this->assertEquals('Test Book', $book->title);
        $this->assertEquals('Test Author', $book->author);
        $this->assertEquals('Test Description', $book->description);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_be_created_with_factory(): void
    {
        $book = Book::factory()->create();

        $this->assertInstanceOf(Book::class, $book);
        $this->assertNotNull($book->id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_has_timestamps(): void
    {
        $book = Book::factory()->create();

        $this->assertNotNull($book->created_at);
        $this->assertNotNull($book->updated_at);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_has_many_pages_relationship(): void
    {
        $book = Book::factory()->create();

        BookPage::factory()->count(3)->create([
            'book_id' => $book->id,
        ]);

        $this->assertCount(3, $book->pages);
        $this->assertInstanceOf(BookPage::class, $book->pages->first());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_access_pages_through_relationship(): void
    {
        $book = Book::factory()->create();

        $page1 = BookPage::factory()->create([
            'book_id' => $book->id,
            'page_number' => 1,
        ]);

        $page2 = BookPage::factory()->create([
            'book_id' => $book->id,
            'page_number' => 2,
        ]);

        $pages = $book->pages()->orderBy('page_number')->get();

        $this->assertCount(2, $pages);
        $this->assertEquals(1, $pages[0]->page_number);
        $this->assertEquals(2, $pages[1]->page_number);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function pages_relationship_returns_collection(): void
    {
        $book = Book::factory()->create();

        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Collection::class,
            $book->pages
        );
    }
}
