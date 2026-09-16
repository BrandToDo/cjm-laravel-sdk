<?php

declare(strict_types=1);

namespace CustomerJourneyPlatform\LaravelSdk\Tests\Unit;

use CustomerJourneyPlatform\LaravelSdk\Exceptions\ApiException;
use CustomerJourneyPlatform\LaravelSdk\PlatformClient;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class PlatformClientTest extends TestCase
{
    public function test_it_sends_a_bearer_authorization_header_and_json_content_type(): void
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], json_encode(['customers' => [], 'pagination' => ['page' => 1, 'pageSize' => 20, 'total' => 0, 'totalPages' => 0]])),
        ]);

        $history = [];
        $stack = HandlerStack::create($mock);
        $stack->push(Middleware::history($history));

        $client = new PlatformClient(new GuzzleClient(['handler' => $stack]), 'my-secret-key', 'http://localhost:9999');
        $client->customers()->list();

        /** @var Request $sent */
        $sent = $history[0]['request'];
        self::assertSame('Bearer my-secret-key', $sent->getHeaderLine('Authorization'));
        self::assertSame('application/json', $sent->getHeaderLine('Content-Type'));
        self::assertSame('http://localhost:9999/api/v1/customers', (string) $sent->getUri());
    }

    public function test_a_network_error_is_wrapped_as_an_api_exception(): void
    {
        $mock = new MockHandler([
            new ConnectException('Connection refused', new Request('GET', 'http://localhost:9999/api/v1/customers')),
        ]);

        $client = new PlatformClient(new GuzzleClient(['handler' => HandlerStack::create($mock)]), 'key', 'http://localhost:9999');

        try {
            $client->customers()->list();
            self::fail('Expected ApiException was not thrown.');
        } catch (ApiException $e) {
            self::assertSame(0, $e->status);
            self::assertStringContainsString('Connection refused', $e->getMessage());
        }
    }
}
