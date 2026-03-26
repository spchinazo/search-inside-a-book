<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PageApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_it_returns_page_content(): void
    {
        $response = $this->getJson('/api/pages/2');

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => ['page', 'content'],
            'meta' => ['total_pages'],
        ]);

        $this->assertSame(2, $response->json('data.page'));
        $this->assertNotEmpty($response->json('data.content'));
    }

    public function test_it_returns_404_for_invalid_page(): void
    {
        $response = $this->getJson('/api/pages/0');
        $response->assertStatus(404);
    }
}
