<?php

declare(strict_types=1);

namespace CustomerJourneyPlatform\LaravelSdk\Data;

/** Returned by CustomersResource::get() — the list/create/update responses
 *  don't include customAttributeValues, matching the API. */
final class CustomerDetail
{
    /** @param CustomerAttributeValue[] $customerAttributeValues */
    public function __construct(
        public readonly Customer $customer,
        public readonly array $customerAttributeValues,
    ) {
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        $values = $data['customerAttributeValues'] ?? [];

        return new self(
            customer: Customer::fromArray($data),
            customerAttributeValues: array_map(
                static fn (array $value): CustomerAttributeValue => CustomerAttributeValue::fromArray($value),
                $values,
            ),
        );
    }
}
