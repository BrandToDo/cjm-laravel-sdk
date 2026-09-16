<?php

declare(strict_types=1);

namespace CustomerJourneyPlatform\LaravelSdk\Exceptions;

use Exception;
use Throwable;

/**
 * Thrown for any non-2xx response from the API, so consumers can
 * `catch (ApiException $e)` instead of parsing a generic HTTP/Guzzle error.
 *
 * Note: the API's current error responses are always `{"error": "<message>"}`
 * — there is no separate structured details payload yet, even for
 * validation failures where multiple issues are joined into one message
 * string. `$details` holds the full decoded response body so nothing is
 * lost if that changes.
 */
class ApiException extends Exception
{
    public function __construct(
        string $message,
        public readonly int $status,
        public readonly mixed $details = null,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, previous: $previous);
    }
}
