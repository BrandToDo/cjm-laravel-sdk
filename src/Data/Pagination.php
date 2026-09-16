<?php

declare(strict_types=1);

namespace CustomerJourneyPlatform\LaravelSdk\Data;

final class Pagination
{
    public function __construct(
        public readonly int $page,
        public readonly int $pageSize,
        public readonly int $total,
        public readonly int $totalPages,
    ) {
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            page: (int) $data['page'],
            pageSize: (int) $data['pageSize'],
            total: (int) $data['total'],
            totalPages: (int) $data['totalPages'],
        );
    }
}
