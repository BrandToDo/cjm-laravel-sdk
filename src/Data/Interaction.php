<?php

declare(strict_types=1);

namespace CustomerJourneyPlatform\LaravelSdk\Data;

final class Interaction
{
    public function __construct(
        public readonly string $id,
        public readonly string $organizationId,
        public readonly string $customerId,
        public readonly ?string $authorId,
        public readonly string $note,
        public readonly string $categoryId,
        public readonly string $status,
        public readonly ?string $followUpDate,
        public readonly ?string $assignedToId,
        public readonly string $source,
        public readonly string $createdAt,
        public readonly string $updatedAt,
    ) {
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (string) $data['id'],
            organizationId: (string) $data['organizationId'],
            customerId: (string) $data['customerId'],
            authorId: $data['authorId'] ?? null,
            note: (string) $data['note'],
            categoryId: (string) $data['categoryId'],
            status: (string) $data['status'],
            followUpDate: $data['followUpDate'] ?? null,
            assignedToId: $data['assignedToId'] ?? null,
            source: (string) $data['source'],
            createdAt: (string) $data['createdAt'],
            updatedAt: (string) $data['updatedAt'],
        );
    }
}
