<?php

declare(strict_types=1);

namespace CustomerJourneyPlatform\LaravelSdk\Tests;

use CustomerJourneyPlatform\LaravelSdk\Facades\Platform;
use CustomerJourneyPlatform\LaravelSdk\PlatformClient;
use CustomerJourneyPlatform\LaravelSdk\Resources\CustomersResource;

final class ServiceProviderTest extends TestCase
{
    public function test_the_platform_client_is_registered_as_a_singleton_from_config(): void
    {
        $first = $this->app->make(PlatformClient::class);
        $second = $this->app->make(PlatformClient::class);

        self::assertSame($first, $second);
    }

    public function test_the_facade_resolves_to_the_same_client(): void
    {
        self::assertInstanceOf(CustomersResource::class, Platform::customers());
    }

    public function test_the_config_file_is_publishable(): void
    {
        self::assertFileExists(__DIR__ . '/../config/platform.php');
    }
}
