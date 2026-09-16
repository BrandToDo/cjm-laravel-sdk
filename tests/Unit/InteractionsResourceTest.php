<?php

declare(strict_types=1);

namespace CustomerJourneyPlatform\LaravelSdk\Tests\Unit;

use CustomerJourneyPlatform\LaravelSdk\Exceptions\ApiException;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class InteractionsResourceTest extends TestCase
{
    public function test_create_maps_the_raw_interaction_response(): void
    {
        $body = [
            'id' => 'int_1',
            'organizationId' => 'org_1',
            'customerId' => 'cust_db_1',
            'authorId' => null,
            'note' => 'Called to follow up on billing question.',
            'categoryId' => 'cat_1',
            'status' => 'open',
            'followUpDate' => null,
            'assignedToId' => null,
            'source' => 'api',
            'createdAt' => '2026-09-16T00:00:00.000Z',
            'updatedAt' => '2026-09-16T00:00:00.000Z',
        ];

        [$client] = MockClientFactory::make([
            new Response(201, ['Content-Type' => 'application/json'], json_encode($body)),
        ]);

        $interaction = $client->interactions()->create([
            'customerExternalId' => 'cust_1',
            'note' => 'Called to follow up on billing question.',
            'category' => 'Billing',
        ]);

        self::assertSame('Called to follow up on billing question.', $interaction->note);
        self::assertSame('open', $interaction->status);
    }

    public function test_create_throws_404_api_exception_when_customer_does_not_exist(): void
    {
        [$client] = MockClientFactory::make([
            new Response(404, ['Content-Type' => 'application/json'], json_encode([
                'error' => 'Customer not found for the given customerExternalId',
            ])),
        ]);

        try {
            $client->interactions()->create([
                'customerExternalId' => 'does-not-exist',
                'note' => 'hi',
                'category' => 'Support',
            ]);
            self::fail('Expected ApiException was not thrown.');
        } catch (ApiException $e) {
            self::assertSame(404, $e->status);
        }
    }

    public function test_list_maps_interactions_without_pagination(): void
    {
        $body = ['interactions' => [[
            'id' => 'int_1',
            'organizationId' => 'org_1',
            'customerId' => 'cust_db_1',
            'authorId' => null,
            'note' => 'hi',
            'categoryId' => 'cat_1',
            'status' => 'open',
            'followUpDate' => null,
            'assignedToId' => null,
            'source' => 'api',
            'createdAt' => '2026-09-16T00:00:00.000Z',
            'updatedAt' => '2026-09-16T00:00:00.000Z',
        ]]];

        [$client] = MockClientFactory::make([
            new Response(200, ['Content-Type' => 'application/json'], json_encode($body)),
        ]);

        $result = $client->interactions()->list('cust_1');

        self::assertCount(1, $result->interactions);
        self::assertSame('int_1', $result->interactions[0]->id);
    }
}
