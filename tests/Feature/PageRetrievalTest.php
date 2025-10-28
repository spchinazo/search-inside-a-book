<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Book;
use App\Models\BookPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

class PageRetrievalTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_retrieves_full_page_content(): void
    {
        $book = Book::factory()->create(['title' => 'Test Book']);

        $page = BookPage::factory()->create([
            'book_id' => $book->id,
            'page_number' => 42,
            'text_content' => 'This is the full content of page 42.',
        ]);

        $response = $this->getJson("/api/books/{$book->id}/pages/42");

        $response->assertStatus(200)
            ->assertJson([
                'book_id' => $book->id,
                'page_number' => 42,
                'content' => 'This is the full content of page 42.',
                'book_title' => 'Test Book',
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_caches_page_content(): void
    {
        $book = Book::factory()->create();
        $page = BookPage::factory()->create([
            'book_id' => $book->id,
            'page_number' => 5,
        ]);

        $cacheKey = "full_page_content_{$book->id}_{$page->page_number}";
        Cache::forget($cacheKey);

        $response1 = $this->getJson("/api/books/{$book->id}/pages/{$page->page_number}");
        $response1->assertStatus(200);

        $this->assertTrue(Cache::has($cacheKey));

        $response2 = $this->getJson("/api/books/{$book->id}/pages/{$page->page_number}");
        $response2->assertStatus(200);

        $this->assertEquals($response1->json(), $response2->json());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_caches_page_content_after_first_request(): void
    {
        $book = Book::factory()->create();
        $page = BookPage::factory()->create([
            'book_id' => $book->id,
            'page_number' => 10,
        ]);

        $cacheKey = "full_page_content_{$book->id}_{$page->page_number}";
        Cache::forget($cacheKey);

        $this->getJson("/api/books/{$book->id}/pages/{$page->page_number}");

        $this->assertTrue(Cache::has($cacheKey));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function cached_page_includes_book_relationship(): void
    {
        $book = Book::factory()->create(['title' => 'Cached Book Title']);
        $page = BookPage::factory()->create([
            'book_id' => $book->id,
            'page_number' => 15,
        ]);

        $cacheKey = "full_page_content_{$book->id}_{$page->page_number}";
        Cache::forget($cacheKey);

        $response = $this->getJson("/api/books/{$book->id}/pages/{$page->page_number}");

        $cached = Cache::get($cacheKey);
        $this->assertArrayHasKey('book_title', $cached);
        $this->assertEquals('Cached Book Title', $cached['book_title']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_404_for_nonexistent_page(): void
    {
        $book = Book::factory()->create();
        
        $response = $this->getJson("/api/books/{$book->id}/pages/99999");

        $response->assertStatus(404);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_404_for_nonexistent_book(): void
    {
        $response = $this->getJson("/api/books/99999/pages/1");

        $response->assertStatus(404);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_includes_book_title_in_page_response(): void
    {
        $book = Book::factory()->create(['title' => 'Eloquent JavaScript']);
        $page = BookPage::factory()->create([
            'book_id' => $book->id,
            'page_number' => 20,
        ]);

        $response = $this->getJson("/api/books/{$book->id}/pages/{$page->page_number}");

        $response->assertStatus(200)
            ->assertJsonPath('book_title', 'Eloquent JavaScript');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_includes_book_id_in_page_response(): void
    {
        $book = Book::factory()->create();
        $page = BookPage::factory()->create([
            'book_id' => $book->id,
            'page_number' => 25,
        ]);

        $response = $this->getJson("/api/books/{$book->id}/pages/{$page->page_number}");

        $response->assertStatus(200)
            ->assertJsonPath('book_id', $book->id);
    }
}