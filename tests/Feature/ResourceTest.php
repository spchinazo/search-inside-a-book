<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Book;
use App\Http\Resources\BookResource;
use App\Http\Resources\SearchResultResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

class ResourceTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_transforms_book_to_array(): void
    {
        $book = Book::factory()->create([
            'title' => 'Test Book',
            'author' => 'Test Author',
            'description' => 'Test Description',
        ]);

        $resource = new BookResource($book);
        $array = $resource->toArray(new Request());

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('title', $array);
        $this->assertArrayHasKey('author', $array);
        $this->assertArrayHasKey('description', $array);
        $this->assertArrayHasKey('search_url', $array);
        $this->assertArrayHasKey('created_at', $array);
        $this->assertArrayHasKey('updated_at', $array);

        $this->assertEquals('Test Book', $array['title']);
        $this->assertEquals('Test Author', $array['author']);
        $this->assertEquals('Test Description', $array['description']);
        $this->assertStringContainsString('/api/books/' . $book->id . '/search', $array['search_url']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_formats_search_result_with_highlighting(): void
    {
        $hit = [
            'id' => 1,
            'book_id' => 10,  // ← ADICIONADO: necessário para gerar a URL
            'page_number' => 5,
            'text_content' => 'This is a test content',
            '_formatted' => [
                'text_content' => 'This is a <em>test</em> content',
            ],
            '_rankingScore' => 0.9876,
        ];

        $resource = new SearchResultResource($hit);
        $array = $resource->toArray(new Request());

        $this->assertEquals(1, $array['id']);
        $this->assertEquals(5, $array['page_number']);
        $this->assertEquals('This is a <em>test</em> content', $array['snippet']);
        $this->assertEquals(0.9876, $array['relevance_score']);
        $this->assertStringContainsString('/api/books/10/pages/5', $array['full_page_url']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_handles_missing_formatted_content(): void
    {
        $hit = [
            'id' => 2,
            'book_id' => 1,
            'page_number' => 10,
            'text_content' => 'Original content without highlighting',
            '_rankingScore' => 0.5,
        ];

        $resource = new SearchResultResource($hit);
        $array = $resource->toArray(new Request());

        $this->assertEquals('Original content without highlighting', $array['snippet']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_truncates_long_snippets(): void
    {
        $longText = str_repeat('a', 500);

        $hit = [
            'id' => 3,
            'book_id' => 1,
            'page_number' => 15,
            'text_content' => $longText,
            '_rankingScore' => 0.7,
        ];

        $resource = new SearchResultResource($hit);
        $array = $resource->toArray(new Request());

        $this->assertLessThanOrEqual(403, strlen($array['snippet'])); // 400 + '...'
        $this->assertStringEndsWith('...', $array['snippet']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_handles_missing_ranking_score(): void
    {
        $hit = [
            'id' => 4,
            'book_id' => 1,
            'page_number' => 20,
            'text_content' => 'Content without score',
        ];

        $resource = new SearchResultResource($hit);
        $array = $resource->toArray(new Request());

        $this->assertEquals(0, $array['relevance_score']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_rounds_relevance_score_to_four_decimals(): void
    {
        $hit = [
            'id' => 5,
            'book_id' => 1,
            'page_number' => 25,
            'text_content' => 'Test content',
            '_rankingScore' => 0.123456789,
        ];

        $resource = new SearchResultResource($hit);
        $array = $resource->toArray(new Request());

        $this->assertEquals(0.1235, $array['relevance_score']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_generates_correct_restful_page_url(): void
    {
        $hit = [
            'id' => 6,
            'book_id' => 42,
            'page_number' => 100,
            'text_content' => 'Test content for URL',
            '_rankingScore' => 0.8,
        ];

        $resource = new SearchResultResource($hit);
        $array = $resource->toArray(new Request());

        $this->assertEquals('http://localhost/api/books/42/pages/100', $array['full_page_url']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_includes_all_required_fields(): void
    {
        $hit = [
            'id' => 7,
            'book_id' => 1,
            'page_number' => 30,
            'text_content' => 'Complete test',
            '_formatted' => [
                'text_content' => '<em>Complete</em> test',
            ],
            '_rankingScore' => 0.95,
        ];

        $resource = new SearchResultResource($hit);
        $array = $resource->toArray(new Request());

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('page_number', $array);
        $this->assertArrayHasKey('snippet', $array);
        $this->assertArrayHasKey('full_page_url', $array);
        $this->assertArrayHasKey('relevance_score', $array);
    }
}