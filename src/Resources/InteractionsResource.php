<?php

declare(strict_types=1);

namespace CustomerJourneyPlatform\LaravelSdk\Resources;

use CustomerJourneyPlatform\LaravelSdk\Data\Interaction;
use CustomerJourneyPlatform\LaravelSdk\Data\InteractionList;
use CustomerJourneyPlatform\LaravelSdk\PlatformClient;

final class InteractionsResource
{
    public function __construct(private readonly PlatformClient $client)
    {
    }

    /**
     * POST /api/v1/interactions — does not auto-create the customer;
     * 404s if customerExternalId doesn't match a known customer.
     *
     * @param array{customerExternalId: string, note: string, category: string, status?: string, followUpDate?: string, assignedToEmail?: string} $input
     */
    public function create(array $input): Interaction
    {
        return Interaction::fromArray($this->client->request('POST', '/api/v1/interactions', $input));
    }

    /** GET /api/v1/interactions?customerExternalId=... — most recent first, not paginated. */
    public function list(string $customerExternalId): InteractionList
    {
        $query = http_build_query(['customerExternalId' => $customerExternalId]);

        return InteractionList::fromArray($this->client->request('GET', '/api/v1/interactions?' . $query));
    }
}
