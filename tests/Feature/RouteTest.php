<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Book;
use App\Models\BookPage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RouteTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function api_endpoints_return_valid_responses(): void
    {
        $book = Book::factory()->create();
        $page = BookPage::factory()->create(['book_id' => $book->id]);

        // Test books list endpoint
        $response = $this->getJson('/api/books');
        $this->assertNotEquals(404, $response->status());
        $response->assertHeader('Content-Type', 'application/json');

        // Test book search endpoint
        $response = $this->getJson("/api/books/{$book->id}/search?q=test");
        $this->assertNotEquals(404, $response->status());
        $response->assertHeader('Content-Type', 'application/json');

        // Test page show endpoint
        $response = $this->getJson("/api/pages/{$page->id}");
        $this->assertEquals(200, $response->status());
        $response->assertHeader('Content-Type', 'application/json');

        // Test health check endpoint
        $response = $this->getJson('/api/health');
        $this->assertContains($response->status(), [200, 503]);
        $response->assertHeader('Content-Type', 'application/json');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function undefined_routes_return_404(): void
    {
        $response = $this->getJson('/api/undefined-route');

        $this->assertEquals(404, $response->status());
        $response->assertHeader('Content-Type', 'application/json');
    }
}
