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
        $this->assertStringContainsString('/api/pages/1', $array['full_page_url']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_handles_missing_formatted_content(): void
    {
        $hit = [
            'id' => 2,
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
            'page_number' => 25,
            'text_content' => 'Test content',
            '_rankingScore' => 0.123456789,
        ];

        $resource = new SearchResultResource($hit);
        $array = $resource->toArray(new Request());

        $this->assertEquals(0.1235, $array['relevance_score']);
    }
}