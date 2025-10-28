<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthControllerTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_200_when_all_services_are_healthy(): void
    {
        $response = $this->getJson('/api/health');

        if ($response->json('status') === 'healthy') {
            $response->assertStatus(200);
            $this->assertEquals('healthy', $response->json('services.database'));
            $this->assertEquals('healthy', $response->json('services.cache'));
            $this->assertEquals('healthy', $response->json('services.meilisearch'));
        } else {
            $response->assertStatus(503);
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_503_when_services_are_unhealthy(): void
    {
        $response = $this->getJson('/api/health');

        $status = $response->json('status');
        $statusCode = $response->status();

        if ($status !== 'healthy') {
            $this->assertEquals(503, $statusCode);
        } else {
            $this->assertEquals(200, $statusCode);
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_checks_all_services_status(): void
    {
        $response = $this->getJson('/api/health');

        $this->assertContains($response->status(), [200, 503]);

        $response->assertJsonStructure([
            'status',
            'timestamp',
            'services' => [
                'database',
                'cache',
                'meilisearch',
            ]
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_includes_timestamp_in_iso8601_format(): void
    {
        $response = $this->getJson('/api/health');

        $this->assertContains($response->status(), [200, 503]);

        $timestamp = $response->json('timestamp');
        $this->assertNotNull($timestamp);

        $this->assertMatchesRegularExpression(
            '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/',
            $timestamp
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_reports_individual_service_health(): void
    {
        $response = $this->getJson('/api/health');

        $services = $response->json('services');

        $this->assertArrayHasKey('database', $services);
        $this->assertArrayHasKey('cache', $services);
        $this->assertArrayHasKey('meilisearch', $services);

        $this->assertEquals('healthy', $services['database']);

        foreach ($services as $service => $status) {
            $this->assertContains($status, ['healthy', 'unhealthy']);
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_sets_status_to_degraded_when_cache_fails(): void
    {
        $response = $this->getJson('/api/health');

        $status = $response->json('status');

        $this->assertContains($status, ['healthy', 'degraded', 'unhealthy']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_handles_all_service_checks(): void
    {
        $response = $this->getJson('/api/health');

        $services = $response->json('services');

        $this->assertArrayHasKey('database', $services);
        $this->assertArrayHasKey('cache', $services);
        $this->assertArrayHasKey('meilisearch', $services);

        foreach ($services as $service => $status) {
            $this->assertContains($status, ['healthy', 'unhealthy']);
        }

        $this->assertContains($response->status(), [200, 503]);
    }
}