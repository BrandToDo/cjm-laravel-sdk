<?php

declare(strict_types=1);

namespace CustomerJourneyPlatform\LaravelSdk\Data;

/** Only present on GET /api/v1/customers/:externalId, which includes it. */
final class CustomerAttributeValue
{
    public function __construct(
        public readonly string $id,
        public readonly string $customerId,
        public readonly string $attributeDefinitionId,
        public readonly string $value,
        public readonly ?string $valueText,
        public readonly ?float $valueNumber,
        public readonly ?string $valueDate,
        public readonly ?bool $valueBoolean,
        public readonly string $attributeKey,
        public readonly string $attributeLabel,
        public readonly string $attributeFieldType,
    ) {
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        $definition = $data['attributeDefinition'] ?? [];

        return new self(
            id: (string) $data['id'],
            customerId: (string) $data['customerId'],
            attributeDefinitionId: (string) $data['attributeDefinitionId'],
            value: (string) $data['value'],
            valueText: $data['valueText'] ?? null,
            valueNumber: isset($data['valueNumber']) ? (float) $data['valueNumber'] : null,
            valueDate: $data['valueDate'] ?? null,
            valueBoolean: $data['valueBoolean'] ?? null,
            attributeKey: (string) ($definition['key'] ?? ''),
            attributeLabel: (string) ($definition['label'] ?? ''),
            attributeFieldType: (string) ($definition['fieldType'] ?? ''),
        );
    }
}
