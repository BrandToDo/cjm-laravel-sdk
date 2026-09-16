<?php

declare(strict_types=1);

namespace CustomerJourneyPlatform\LaravelSdk\Data;

final class PaginatedCustomers
{
    /** @param Customer[] $customers */
    public function __construct(
        public readonly array $customers,
        public readonly Pagination $pagination,
    ) {
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            customers: array_map(
                static fn (array $customer): Customer => Customer::fromArray($customer),
                $data['customers'] ?? [],
            ),
            pagination: Pagination::fromArray($data['pagination'] ?? []),
        );
    }
}
