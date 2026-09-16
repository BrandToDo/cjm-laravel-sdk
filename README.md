# Customer Journey Platform — Laravel SDK

A Laravel package wrapping the Customer Journey Platform sync API (`/api/v1/customers`, `/api/v1/interactions` — see `docs/api.md` in the main app's repo). Companion to the [JS/TS SDK](../customer-journey-platform/sdk) in that repo — same API, same convenience-wrapper-only scope: **no business logic, every method maps to one REST call.**

Internal use only — not published to Packagist. Source: [github.com/BrandToDo/cjm-laravel-sdk](https://github.com/BrandToDo/cjm-laravel-sdk) (**private repo** — every consumer needs GitHub access to it, see Authentication below). Install it as a VCS repository.

## Installation

In the Laravel app that will consume this SDK, add it as a VCS repository in `composer.json`:

```json
{
    "repositories": [
        { "type": "vcs", "url": "https://github.com/BrandToDo/cjm-laravel-sdk.git" }
    ],
    "require": {
        "customer-journey-platform/laravel-sdk": "^0.1"
    }
}
```

```bash
composer require customer-journey-platform/laravel-sdk:^0.1
php artisan vendor:publish --tag=platform-config
```

### Authentication (required — the repo is private)

Composer needs GitHub credentials before it can fetch a private repo, or `composer require` fails with a 404/access-denied even for someone who can see the repo fine in a browser. Two options:

- **SSH** (simplest for local dev): if the consuming machine can already `git clone git@github.com:BrandToDo/cjm-laravel-sdk.git` — i.e. that machine's SSH key is added to the GitHub account/org — Composer picks it up automatically. No extra config needed.
- **GitHub OAuth token** (needed for CI, or anyone without SSH set up): generate a [personal access token](https://github.com/settings/tokens) with `repo` scope (for a private repo, classic tokens need the full `repo` scope; a fine-grained token needs at least read access to Contents on this repo), then:

  ```bash
  composer config --global github-oauth.github.com <token>
  ```

  In CI, set `COMPOSER_AUTH` as an env var instead of writing to disk:

  ```bash
  COMPOSER_AUTH='{"github-oauth":{"github.com":"<token>"}}'
  ```

**Working on this SDK and the consuming app side by side on the same machine?** Swap the repository entry for a path one instead, so local edits are picked up without a push/require cycle:

```json
{ "repositories": [{ "type": "path", "url": "../customer-journey-platform-laravel-sdk" }] }
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
