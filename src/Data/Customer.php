<?php

declare(strict_types=1);

namespace CustomerJourneyPlatform\LaravelSdk\Data;

/**
 * Mirrors the `Customer` Prisma model as returned by the API — the API
 * returns its database records directly, not a hand-shaped DTO. Keep in
 * sync with prisma/schema.prisma and the JS SDK's src/types.ts on the
 * platform repo.
 */
final class Customer
{
    public function __construct(
        public readonly string $id,
        public readonly string $organizationId,
        public readonly ?string $externalId,
        public readonly string $name,
        public readonly string $email,
        public readonly string $status,
        public readonly ?int $ticketCount,
        public readonly ?float $mrr,
        public readonly ?int $usageScore,
        public readonly string $syncedAt,
        public readonly ?array $rawAttributes,
        public readonly string $source,
        public readonly ?string $currentJourneyStageId,
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
            externalId: $data['externalId'] ?? null,
            name: (string) $data['name'],
            email: (string) $data['email'],
            status: (string) $data['status'],
            ticketCount: $data['ticketCount'] ?? null,
            mrr: isset($data['mrr']) ? (float) $data['mrr'] : null,
            usageScore: $data['usageScore'] ?? null,
            syncedAt: (string) $data['syncedAt'],
            rawAttributes: $data['rawAttributes'] ?? null,
            source: (string) $data['source'],
            currentJourneyStageId: $data['currentJourneyStageId'] ?? null,
            createdAt: (string) $data['createdAt'],
            updatedAt: (string) $data['updatedAt'],
        );
    }
}
