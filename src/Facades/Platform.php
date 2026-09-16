<?php

declare(strict_types=1);

namespace CustomerJourneyPlatform\LaravelSdk\Facades;

use CustomerJourneyPlatform\LaravelSdk\Resources\CustomersResource;
use CustomerJourneyPlatform\LaravelSdk\Resources\InteractionsResource;
use Illuminate\Support\Facades\Facade;

/**
 * @method static CustomersResource customers()
 * @method static InteractionsResource interactions()
 *
 * @see \CustomerJourneyPlatform\LaravelSdk\PlatformClient
 */
class Platform extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'platform';
    }
}
