<?php

declare(strict_types=1);

namespace CustomerJourneyPlatform\LaravelSdk\Tests\Integration;

use CustomerJourneyPlatform\LaravelSdk\Exceptions\ApiException;
use CustomerJourneyPlatform\LaravelSdk\PlatformClient;
use GuzzleHttp\Client as GuzzleClient;
use PHPUnit\Framework\TestCase;

/**
 * Exercises the SDK against a real running instance of the platform's
 * Next.js app — skipped unless PLATFORM_LIVE_TEST_BASE_URL and
 * PLATFORM_LIVE_TEST_API_KEY are set, since that app lives in the sibling
 * customer-journey-platform repo and isn't something this repo can boot on
 * its own. To run locally:
 *
 *   (in customer-journey-platform) npm run dev
 *   create an API key via Settings -> API keys, then:
 *
 *   PLATFORM_LIVE_TEST_BASE_URL=http://localhost:3000 \
 *   PLATFORM_LIVE_TEST_API_KEY=<key> \
 *   vendor/bin/phpunit tests/Integration
 */
final class LiveApiTest extends TestCase
{
    private PlatformClient $client;

    protected function setUp(): void
    {
        $baseUrl = getenv('PLATFORM_LIVE_TEST_BASE_URL');
        $apiKey = getenv('PLATFORM_LIVE_TEST_API_KEY');

        if (!$baseUrl || !$apiKey) {
            self::markTestSkipped('Set PLATFORM_LIVE_TEST_BASE_URL and PLATFORM_LIVE_TEST_API_KEY to run live integration tests.');
        }

        $this->client = new PlatformClient(new GuzzleClient(), $apiKey, $baseUrl);
    }

    public function test_create_then_get_a_customer_round_trips(): void
    {
        $externalId = 'laravel-sdk-live-' . bin2hex(random_bytes(4));

        $created = $this->client->customers()->create(['externalId' => $externalId, 'name' => 'Live Test Customer']);
        self::assertSame($externalId, $created->externalId);

        $fetched = $this->client->customers()->get($externalId);
        self::assertSame($created->id, $fetched->customer->id);
    }

    public function test_an_invalid_api_key_results_in_a_401(): void
    {
        $baseUrl = getenv('PLATFORM_LIVE_TEST_BASE_URL');
        $client = new PlatformClient(new GuzzleClient(), 'not-a-real-key', $baseUrl);

        try {
            $client->customers()->list();
            self::fail('Expected ApiException was not thrown.');
        } catch (ApiException $e) {
            self::assertSame(401, $e->status);
        }
    }
}
