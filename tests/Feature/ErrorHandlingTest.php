<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Book;
use App\Http\Requests\SearchBookRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ErrorHandlingTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_404_for_invalid_routes(): void
    {
        $response = $this->getJson('/api/nonexistent');

        $response->assertStatus(404);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_404_for_nonexistent_book(): void
    {
        $response = $this->getJson('/api/books/99999/search?q=test');

        $response->assertStatus(404);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_404_for_nonexistent_page(): void
    {
        $response = $this->getJson('/api/pages/99999');

        $response->assertStatus(404);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_handles_validation_errors_properly(): void
    {
        $book = Book::factory()->create();

        $response = $this->getJson("/api/books/{$book->id}/search");

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_proper_json_error_format(): void
    {
        $response = $this->getJson('/api/nonexistent');

        $response->assertHeader('Content-Type', 'application/json');
    }

    // SearchBookRequest validation tests
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_authorizes_all_requests(): void
    {
        $request = new SearchBookRequest();

        $this->assertTrue($request->authorize());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_validates_query_is_required(): void
    {
        $request = new SearchBookRequest();
        $validator = Validator::make([], $request->rules());

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('q', $validator->errors()->toArray());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_validates_query_minimum_length(): void
    {
        $request = new SearchBookRequest();
        $validator = Validator::make(['q' => 'a'], $request->rules());

        $this->assertFalse($validator->passes());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_validates_query_maximum_length(): void
    {
        $request = new SearchBookRequest();
        $validator = Validator::make(['q' => str_repeat('a', 201)], $request->rules());

        $this->assertFalse($validator->passes());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_validates_page_must_be_integer(): void
    {
        $request = new SearchBookRequest();
        $validator = Validator::make(['q' => 'test', 'page' => 'abc'], $request->rules());

        $this->assertFalse($validator->passes());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_validates_page_must_be_at_least_one(): void
    {
        $request = new SearchBookRequest();
        $validator = Validator::make(['q' => 'test', 'page' => 0], $request->rules());

        $this->assertFalse($validator->passes());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_passes_validation_with_valid_data(): void
    {
        $request = new SearchBookRequest();
        $validator = Validator::make(['q' => 'test query', 'page' => 2], $request->rules());

        $this->assertTrue($validator->passes());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_has_custom_error_messages(): void
    {
        $request = new SearchBookRequest();
        $messages = $request->messages();

        $this->assertArrayHasKey('q.required', $messages);
        $this->assertArrayHasKey('q.min', $messages);
        $this->assertArrayHasKey('q.max', $messages);
        $this->assertArrayHasKey('page.integer', $messages);
        $this->assertArrayHasKey('page.min', $messages);

        $this->assertEquals('The search query is required.', $messages['q.required']);
    }
}