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

        $response = $this->getJson("/api/pages/{$page->id}");

        $response->assertStatus(200)
            ->assertJson([
                'page_number' => 42,
                'content' => 'This is the full content of page 42.',
                'book_title' => 'Test Book',
            ])
            ->assertJsonStructure([
                'page_number',
                'content',
                'book_title',
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_caches_page_content(): void
    {
        $book = Book::factory()->create();
        $page = BookPage::factory()->create(['book_id' => $book->id]);

        // Clear cache
        Cache::forget("full_page_content_{$page->id}");

        // First request - should hit database
        $response1 = $this->getJson("/api/pages/{$page->id}");
        $response1->assertStatus(200);

        // Second request - should hit cache
        $this->assertTrue(Cache::has("full_page_content_{$page->id}"));

        $response2 = $this->getJson("/api/pages/{$page->id}");
        $response2->assertStatus(200);

        // Both responses should be identical
        $this->assertEquals($response1->json(), $response2->json());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_caches_page_content_after_first_request(): void
    {
        $book = Book::factory()->create();
        $page = BookPage::factory()->create(['book_id' => $book->id]);

        $cacheKey = "full_page_content_{$page->id}";
        Cache::forget($cacheKey);

        $this->getJson("/api/pages/{$page->id}");

        $this->assertTrue(Cache::has($cacheKey));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function cached_page_includes_book_relationship(): void
    {
        $book = Book::factory()->create(['title' => 'Cached Book Title']);
        $page = BookPage::factory()->create(['book_id' => $book->id]);

        $cacheKey = "full_page_content_{$page->id}";
        Cache::forget($cacheKey);

        $response = $this->getJson("/api/pages/{$page->id}");

        $cached = Cache::get($cacheKey);
        $this->assertArrayHasKey('book_title', $cached);
        $this->assertEquals('Cached Book Title', $cached['book_title']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_404_for_nonexistent_page(): void
    {
        $response = $this->getJson('/api/pages/99999');

        $response->assertStatus(404);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_includes_book_title_in_page_response(): void
    {
        $book = Book::factory()->create(['title' => 'Eloquent JavaScript']);
        $page = BookPage::factory()->create(['book_id' => $book->id]);

        $response = $this->getJson("/api/pages/{$page->id}");

        $response->assertStatus(200)
            ->assertJsonPath('book_title', 'Eloquent JavaScript');
    }
}