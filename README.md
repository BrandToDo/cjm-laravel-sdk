# Customer Journey Platform — Laravel SDK

A Laravel package wrapping the Customer Journey Platform sync API (`/api/v1/customers`, `/api/v1/interactions` — see `docs/api.md` in the main app's repo). Companion to the [JS/TS SDK](../customer-journey-platform/sdk) in that repo — same API, same convenience-wrapper-only scope: **no business logic, every method maps to one REST call.**

Internal use only — not published to Packagist. Install via a local path repository until there's a reason to publish it.

## Installation (internal, via path repository)

In the Laravel app that will consume this SDK:

```json
{
    "repositories": [
        { "type": "path", "url": "../customer-journey-platform-laravel-sdk" }
    ],
    "require": {
        "customer-journey-platform/laravel-sdk": "*"
    }
}
```

```bash
composer require customer-journey-platform/laravel-sdk:@dev
php artisan vendor:publish --tag=platform-config
```

Set in `.env` — there's no production deployment yet, so `PLATFORM_BASE_URL` must always be set explicitly:

```
PLATFORM_API_KEY=your-api-key
PLATFORM_BASE_URL=http://localhost:3000
```

## Usage

```php
use CustomerJourneyPlatform\LaravelSdk\Facades\Platform;

$customer = Platform::customers()->create([
    'externalId' => 'cust_1',
    'name' => 'Jane Doe',
    'email' => 'jane@example.com',
]);

Platform::interactions()->create([
    'customerExternalId' => 'cust_1',
    'note' => 'Called to follow up on billing question.',
    'category' => 'Billing',
]);

$fetched = Platform::customers()->get('cust_1');
```

Or via dependency injection instead of the facade:

```php
use CustomerJourneyPlatform\LaravelSdk\PlatformClient;

public function __construct(private PlatformClient $platform) {}

$customer = $this->platform->customers()->create([...]);
```

Errors are thrown as `CustomerJourneyPlatform\LaravelSdk\Exceptions\ApiException` (`status`, `message`, `details`) for any non-2xx response — never a generic Guzzle exception.

## Notable differences from a generic REST wrapper

Mirrors the API's actual response shapes (the raw Prisma records, not a hand-shaped DTO) — see the JS SDK's README for the same list, since both wrap the same API:

- `customers()->create()` / `update()` / `get()` return the customer data directly, not wrapped in an envelope.
- `customers()->list()` returns `PaginatedCustomers` (`customers`, `pagination`).
- `interactions()->create()` returns the interaction directly; `interactions()->list()` returns `InteractionList` (`interactions`) with **no pagination** — the API doesn't paginate this endpoint yet.
- `ApiException::$details` holds the full decoded error response body, but today that's always just `{"error": "<message>"}` — no structured per-field details, even when a Zod validation error joins multiple issues into one string.

## Development

```bash
composer install
composer test              # unit tests only (mocked HTTP, no external dependency)
```

### Unit vs. integration tests

- `tests/Unit/*` and `tests/ServiceProviderTest.php` run against a Guzzle `MockHandler` — fast, deterministic, no dependency on the Next.js app.
- `tests/Integration/LiveApiTest.php` exercises the SDK against a real running instance of the platform API. It self-skips unless both env vars are set:

  ```bash
  # in the customer-journey-platform repo
  npm run dev

  # create an API key via Settings -> API keys in the running app, then:
  PLATFORM_LIVE_TEST_BASE_URL=http://localhost:3000 \
  PLATFORM_LIVE_TEST_API_KEY=<key> \
  vendor/bin/phpunit tests/Integration
  ```

This split exists because this package lives in its own repo, separate from the Next.js app whose API it wraps — unlike the JS SDK (which lives inside that repo and can spin up the route handlers directly in-process for its tests), there's no way to boot the API from here without a cross-repo dependency, so live-instance testing is opt-in rather than part of the default `composer test` run.
