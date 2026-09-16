<?php

declare(strict_types=1);

namespace CustomerJourneyPlatform\LaravelSdk\Tests;

use CustomerJourneyPlatform\LaravelSdk\PlatformServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /** @return array<int, class-string> */
    protected function getPackageProviders($app): array
    {
        return [PlatformServiceProvider::class];
    }

    /** @return array<string, string> */
    protected function getPackageAliases($app): array
    {
        return ['Platform' => \CustomerJourneyPlatform\LaravelSdk\Facades\Platform::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('platform.api_key', 'test-key');
        $app['config']->set('platform.base_url', 'http://localhost:9999');
    }
}
