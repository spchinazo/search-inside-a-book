<?php

namespace Tests\Feature;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookSearchApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Page::create([
            'page_number' => 100,
            'text_content' => 'JavaScript es un lenguaje de programación usado para desarrollo web.',
        ]);
    }

    public function test_search_endpoint_returns_results(): void
    {
        $response = $this->getJson('/api/search?q=JavaScript');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'results' => [
                    '*' => ['page_number', 'snippet', 'rank'], // Esto indica que esperamos un array de resultados con estos campos
                ],
                'total',
                'query',
            ]);
    }

    public function test_search_endpoint_requires_minimum_query_length(): void
    {
        $response = $this->getJson('/api/search?q=J');

        $response->assertStatus(200)
            ->assertJson([
                'results' => [],
                'total' => 0,
            ]);
    }

    public function test_get_page_endpoint_returns_page_data(): void
    {
        $response = $this->getJson('/api/pages/100');

        $response->assertStatus(200)
            ->assertJson([
                'page_number' => 100,
            ])
            ->assertJsonStructure([
                'text_content',
                'pdf_urf',
            ]);
    }

    public function test_get_page_endpoint_returns_404_for_nonexistent_page(): void
    {
        $response = $this->getJson('/api/pages/9999');

        $response->assertStatus(404)
            ->assertJson([
                'error' => 'Página no encontrada',
            ]);
    }
}
