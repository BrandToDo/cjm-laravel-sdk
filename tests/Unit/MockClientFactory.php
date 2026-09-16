<?php

declare(strict_types=1);

namespace CustomerJourneyPlatform\LaravelSdk\Tests\Unit;

use CustomerJourneyPlatform\LaravelSdk\PlatformClient;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

/**
 * Builds a PlatformClient backed by a Guzzle MockHandler queue instead of a
 * real socket — these tests verify request construction and response
 * mapping in isolation, without depending on a running instance of the
 * Next.js app in the sibling repo. See tests/Integration for the
 * live-server counterpart.
 */
final class MockClientFactory
{
    /**
     * @param Response[] $responses
     * @return array{0: PlatformClient, 1: MockHandler}
     */
    public static function make(array $responses): array
    {
        $mock = new MockHandler($responses);
        $http = new GuzzleClient(['handler' => HandlerStack::create($mock)]);

        $client = new PlatformClient($http, 'test-key', 'http://localhost:9999');

        return [$client, $mock];
    }
}
