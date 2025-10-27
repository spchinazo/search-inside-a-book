<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Providers\AppServiceProvider;
use App\Providers\AuthServiceProvider;
use App\Providers\EventServiceProvider;

class ServiceProvidersTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function service_providers_are_registered(): void
    {
        $this->assertNotNull($this->app->getProvider(AppServiceProvider::class));
        $this->assertNotNull($this->app->getProvider(AuthServiceProvider::class));
        $this->assertNotNull($this->app->getProvider(EventServiceProvider::class));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function application_boots_successfully(): void
    {
        $this->assertTrue($this->app->isBooted());
    }
}