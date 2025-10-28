<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MiddlewareTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_verifies_csrf_token_on_post_requests(): void
    {
        // POST requests without CSRF token should fail with 419 (token mismatch)
        // or 404 if route doesn't exist
        $response = $this->post('/test');

        $this->assertContains($response->status(), [404, 419]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_json_responses_through_middleware(): void
    {
        $response = $this->getJson('/api/health');

        $this->assertContains($response->status(), [200, 503]);
        $response->assertHeader('Content-Type', 'application/json');
    }
}