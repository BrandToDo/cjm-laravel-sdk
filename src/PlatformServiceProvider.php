<?php

declare(strict_types=1);

namespace CustomerJourneyPlatform\LaravelSdk;

use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\ServiceProvider;

class PlatformServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/platform.php', 'platform');

        $this->app->singleton(PlatformClient::class, function ($app) {
            $config = $app['config']->get('platform');

            $http = new GuzzleClient([
                'timeout' => ((int) ($config['timeout_ms'] ?? 10000)) / 1000,
            ]);

            return new PlatformClient(
                http: $http,
                apiKey: (string) ($config['api_key'] ?? ''),
                baseUrl: (string) ($config['base_url'] ?? ''),
            );
        });

        $this->app->alias(PlatformClient::class, 'platform');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/platform.php' => config_path('platform.php'),
            ], 'platform-config');
        }
    }
}
