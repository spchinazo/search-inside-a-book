<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

class BooksListTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_lists_all_books(): void
    {
        Book::factory()->count(3)->create();

        $response = $this->getJson('/api/books');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'author',
                    ]
                ]
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_caches_books_list(): void
    {
        Book::factory()->count(2)->create();

        Cache::forget('all_books_list');

        // First request
        $response1 = $this->getJson('/api/books');
        $response1->assertStatus(200);

        // Verify cache was set
        $this->assertTrue(Cache::has('all_books_list'));

        // Second request should use cache
        $response2 = $this->getJson('/api/books');
        $response2->assertStatus(200);

        $this->assertEquals($response1->json(), $response2->json());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_caches_books_list_after_first_request(): void
    {
        Book::factory()->create(['title' => 'Cached Book']);

        Cache::forget('all_books_list');
        $this->assertFalse(Cache::has('all_books_list'));

        $this->getJson('/api/books');

        $this->assertTrue(Cache::has('all_books_list'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_empty_array_when_no_books_exist(): void
    {
        $response = $this->getJson('/api/books');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }
}