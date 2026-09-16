<?php

declare(strict_types=1);

namespace CustomerJourneyPlatform\LaravelSdk\Tests\Unit;

use CustomerJourneyPlatform\LaravelSdk\Exceptions\ApiException;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class CustomersResourceTest extends TestCase
{
    private function customerJson(array $overrides = []): array
    {
        return array_merge([
            'id' => 'cust_db_1',
            'organizationId' => 'org_1',
            'externalId' => 'cust_1',
            'name' => 'Acme Inc',
            'email' => 'a@acme.com',
            'status' => 'trial',
            'ticketCount' => null,
            'mrr' => null,
            'usageScore' => null,
            'syncedAt' => '2026-09-16T00:00:00.000Z',
            'rawAttributes' => null,
            'source' => 'api',
            'currentJourneyStageId' => null,
            'createdAt' => '2026-09-16T00:00:00.000Z',
            'updatedAt' => '2026-09-16T00:00:00.000Z',
        ], $overrides);
    }

    public function test_create_maps_the_raw_customer_response(): void
    {
        [$client] = MockClientFactory::make([
            new Response(201, ['Content-Type' => 'application/json'], json_encode($this->customerJson())),
        ]);

        $customer = $client->customers()->create(['externalId' => 'cust_1', 'name' => 'Acme Inc', 'email' => 'a@acme.com']);

        self::assertSame('cust_1', $customer->externalId);
        self::assertSame('Acme Inc', $customer->name);
        self::assertSame('trial', $customer->status);
    }

    public function test_get_maps_custom_attribute_values(): void
    {
        $body = $this->customerJson([
            'customerAttributeValues' => [[
                'id' => 'cav_1',
                'customerId' => 'cust_db_1',
                'attributeDefinitionId' => 'attr_1',
                'value' => 'Enterprise',
                'valueText' => 'Enterprise',
                'valueNumber' => null,
                'valueDate' => null,
                'valueBoolean' => null,
                'attributeDefinition' => ['id' => 'attr_1', 'key' => 'plan', 'label' => 'Plan', 'fieldType' => 'text'],
            ]],
        ]);

        [$client] = MockClientFactory::make([
            new Response(200, ['Content-Type' => 'application/json'], json_encode($body)),
        ]);

        $detail = $client->customers()->get('cust_1');

        self::assertSame('cust_1', $detail->customer->externalId);
        self::assertCount(1, $detail->customerAttributeValues);
        self::assertSame('plan', $detail->customerAttributeValues[0]->attributeKey);
        self::assertSame('Enterprise', $detail->customerAttributeValues[0]->valueText);
    }

    public function test_get_throws_api_exception_with_status_404_for_unknown_external_id(): void
    {
        [$client] = MockClientFactory::make([
            new Response(404, ['Content-Type' => 'application/json'], json_encode(['error' => 'Customer not found'])),
        ]);

        try {
            $client->customers()->get('does-not-exist');
            self::fail('Expected ApiException was not thrown.');
        } catch (ApiException $e) {
            self::assertSame(404, $e->status);
            self::assertSame('Customer not found', $e->getMessage());
        }
    }

    public function test_list_maps_customers_and_pagination(): void
    {
        $body = [
            'customers' => [$this->customerJson()],
            'pagination' => ['page' => 1, 'pageSize' => 20, 'total' => 1, 'totalPages' => 1],
        ];

        [$client] = MockClientFactory::make([
            new Response(200, ['Content-Type' => 'application/json'], json_encode($body)),
        ]);

        $result = $client->customers()->list(['search' => 'acme']);

        self::assertCount(1, $result->customers);
        self::assertSame(1, $result->pagination->total);
    }

    public function test_an_invalid_api_key_results_in_a_401_api_exception(): void
    {
        [$client] = MockClientFactory::make([
            new Response(401, ['Content-Type' => 'application/json'], json_encode(['error' => 'Invalid API key'])),
        ]);

        try {
            $client->customers()->list();
            self::fail('Expected ApiException was not thrown.');
        } catch (ApiException $e) {
            self::assertSame(401, $e->status);
        }
    }

    public function test_a_malformed_request_results_in_a_400_api_exception_with_details(): void
    {
        [$client] = MockClientFactory::make([
            new Response(400, ['Content-Type' => 'application/json'], json_encode(['error' => 'name is required'])),
        ]);

        try {
            $client->customers()->create(['externalId' => 'cust_1']);
            self::fail('Expected ApiException was not thrown.');
        } catch (ApiException $e) {
            self::assertSame(400, $e->status);
            self::assertIsArray($e->details);
            self::assertSame('name is required', $e->details['error']);
        }
    }
}
