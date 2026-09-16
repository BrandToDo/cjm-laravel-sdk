<?php

declare(strict_types=1);

namespace CustomerJourneyPlatform\LaravelSdk\Resources;

use CustomerJourneyPlatform\LaravelSdk\Data\Customer;
use CustomerJourneyPlatform\LaravelSdk\Data\CustomerDetail;
use CustomerJourneyPlatform\LaravelSdk\Data\PaginatedCustomers;
use CustomerJourneyPlatform\LaravelSdk\PlatformClient;

final class CustomersResource
{
    public function __construct(private readonly PlatformClient $client)
    {
    }

    /**
     * POST /api/v1/customers — upserts by externalId. Calling this
     * repeatedly with the same externalId updates rather than duplicates.
     *
     * @param array{externalId: string, name: string, email?: string, status?: string, customAttributes?: array<string, string|int|float|bool>} $input
     */
    public function create(array $input): Customer
    {
        return Customer::fromArray($this->client->request('POST', '/api/v1/customers', $input));
    }

    /**
     * PATCH /api/v1/customers/:externalId — partial update, 404 if the
     * customer doesn't exist.
     *
     * @param array{name?: string, email?: string, status?: string, externalId?: string} $input
     */
    public function update(string $externalId, array $input): Customer
    {
        return Customer::fromArray(
            $this->client->request('PATCH', '/api/v1/customers/' . rawurlencode($externalId), $input),
        );
    }

    /** GET /api/v1/customers/:externalId — 404 if not found. Includes custom attribute values, unlike list(). */
    public function get(string $externalId): CustomerDetail
    {
        return CustomerDetail::fromArray(
            $this->client->request('GET', '/api/v1/customers/' . rawurlencode($externalId)),
        );
    }

    /** @param array{page?: int, pageSize?: int, search?: string} $params */
    public function list(array $params = []): PaginatedCustomers
    {
        $query = http_build_query(array_filter($params, static fn ($value) => $value !== null));
        $path = '/api/v1/customers' . ($query !== '' ? '?' . $query : '');

        return PaginatedCustomers::fromArray($this->client->request('GET', $path));
    }
}
