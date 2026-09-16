<?php

declare(strict_types=1);

namespace CustomerJourneyPlatform\LaravelSdk\Data;

/** GET /api/v1/interactions isn't paginated on the API side yet. */
final class InteractionList
{
    /** @param Interaction[] $interactions */
    public function __construct(
        public readonly array $interactions,
    ) {
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            interactions: array_map(
                static fn (array $interaction): Interaction => Interaction::fromArray($interaction),
                $data['interactions'] ?? [],
            ),
        );
    }
}
