<?php

declare(strict_types=1);

namespace CustomerJourneyPlatform\LaravelSdk;

use CustomerJourneyPlatform\LaravelSdk\Exceptions\ApiException;
use CustomerJourneyPlatform\LaravelSdk\Resources\CustomersResource;
use CustomerJourneyPlatform\LaravelSdk\Resources\InteractionsResource;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Convenience wrapper only — no business logic. Every method maps directly
 * to one REST call against /api/v1/*. If the API changes, this SDK is the
 * first thing updated to match, never the other way around.
 */
final class PlatformClient
{
    private readonly CustomersResource $customersResource;
    private readonly InteractionsResource $interactionsResource;

    public function __construct(
        private readonly ClientInterface $http,
        private readonly string $apiKey,
        private readonly string $baseUrl,
    ) {
        $this->customersResource = new CustomersResource($this);
        $this->interactionsResource = new InteractionsResource($this);
    }

    public function customers(): CustomersResource
    {
        return $this->customersResource;
    }

    public function interactions(): InteractionsResource
    {
        return $this->interactionsResource;
    }

    /**
     * Internal request method — every resource method calls this. Handles
     * the Authorization header, JSON parsing, and mapping any non-2xx
     * response to an ApiException.
     *
     * @param array<string, mixed>|null $body
     * @return array<string, mixed>
     */
    public function request(string $method, string $path, ?array $body = null): array
    {
        try {
            $response = $this->http->request($method, rtrim($this->baseUrl, '/') . $path, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => $body,
                'http_errors' => false,
            ]);
        } catch (GuzzleException $e) {
            throw new ApiException('Network error calling ' . $path . ': ' . $e->getMessage(), 0, previous: $e);
        }

        $raw = (string) $response->getBody();
        $data = $raw !== '' ? json_decode($raw, true) : null;

        $status = $response->getStatusCode();
        if ($status < 200 || $status >= 300) {
            $message = is_array($data) && isset($data['error']) ? (string) $data['error'] : $response->getReasonPhrase();

            throw new ApiException($message, $status, $data);
        }

        return is_array($data) ? $data : [];
    }
}
