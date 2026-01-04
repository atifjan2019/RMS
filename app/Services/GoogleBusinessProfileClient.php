<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Exception;

class GoogleBusinessProfileClient
{
    private GoogleTokenService $tokenService;
    private string $baseUrl = 'https://mybusinessbusinessinformation.googleapis.com/v1';
    private string $accountsUrl = 'https://mybusinessaccountmanagement.googleapis.com/v1';
    private string $reviewsBaseUrl = 'https://mybusiness.googleapis.com/v4';
    private int $maxRetries = 3;
    private int $retryDelay = 1000; // milliseconds

    public function __construct(GoogleTokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    /**
     * Create an authenticated HTTP client for the tenant.
     */
    private function client(Tenant $tenant): PendingRequest
    {
        $token = $this->tokenService->getAccessToken($tenant);

        if (!$token) {
            throw new Exception('Unable to get access token for tenant');
        }

        return Http::withToken($token)
            ->timeout(30)
            ->retry($this->maxRetries, $this->retryDelay, function ($exception) {
                // Retry on transient errors
                return $exception instanceof \Illuminate\Http\Client\ConnectionException
                    || ($exception instanceof \Illuminate\Http\Client\RequestException
                        && in_array($exception->response->status(), [408, 429, 500, 502, 503, 504]));
            });
    }

    /**
     * List all GBP accounts for the authenticated user.
     */
    public function listAccounts(Tenant $tenant): array
    {
        try {
            $response = $this->client($tenant)
                ->get("{$this->accountsUrl}/accounts");

            if (!$response->successful()) {
                $this->handleError($response, 'listAccounts');
                return [];
            }

            return $response->json('accounts', []);
        } catch (Exception $e) {
            Log::error('listAccounts failed', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * List all locations for an account.
     *
     * @param string $accountName Format: accounts/{accountId}
     */
    public function listLocations(Tenant $tenant, string $accountName): array
    {
        try {
            $response = $this->client($tenant)
                ->get("{$this->baseUrl}/{$accountName}/locations", [
                    'readMask' => 'name,title,phoneNumbers,categories,storefrontAddress,websiteUri,metadata',
                ]);

            if (!$response->successful()) {
                $this->handleError($response, 'listLocations');
                return [];
            }

            return $response->json('locations', []);
        } catch (Exception $e) {
            Log::error('listLocations failed', [
                'tenant_id' => $tenant->id,
                'account' => $accountName,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * List reviews for a location.
     *
     * @param string $locationName Format: accounts/{accountId}/locations/{locationId}
     */
    public function listReviews(Tenant $tenant, string $locationName, ?string $pageToken = null): array
    {
        try {
            $params = ['pageSize' => 50];
            if ($pageToken) {
                $params['pageToken'] = $pageToken;
            }

            $response = $this->client($tenant)
                ->get("{$this->reviewsBaseUrl}/{$locationName}/reviews", $params);

            if (!$response->successful()) {
                $this->handleError($response, 'listReviews');
                return ['reviews' => [], 'nextPageToken' => null];
            }

            return [
                'reviews' => $response->json('reviews', []),
                'nextPageToken' => $response->json('nextPageToken'),
                'totalReviewCount' => $response->json('totalReviewCount', 0),
                'averageRating' => $response->json('averageRating', 0),
            ];
        } catch (Exception $e) {
            Log::error('listReviews failed', [
                'tenant_id' => $tenant->id,
                'location' => $locationName,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Reply to a review.
     *
     * @param string $reviewName Format: accounts/{accountId}/locations/{locationId}/reviews/{reviewId}
     */
    public function upsertReply(Tenant $tenant, string $reviewName, string $replyText): ?array
    {
        try {
            $response = $this->client($tenant)
                ->put("{$this->reviewsBaseUrl}/{$reviewName}/reply", [
                    'comment' => $replyText,
                ]);

            if (!$response->successful()) {
                $this->handleError($response, 'upsertReply');
                return null;
            }

            return $response->json();
        } catch (Exception $e) {
            Log::error('upsertReply failed', [
                'tenant_id' => $tenant->id,
                'review' => $reviewName,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Delete a reply to a review.
     *
     * @param string $reviewName Format: accounts/{accountId}/locations/{locationId}/reviews/{reviewId}
     */
    public function deleteReply(Tenant $tenant, string $reviewName): bool
    {
        try {
            $response = $this->client($tenant)
                ->delete("{$this->reviewsBaseUrl}/{$reviewName}/reply");

            if (!$response->successful()) {
                $this->handleError($response, 'deleteReply');
                return false;
            }

            return true;
        } catch (Exception $e) {
            Log::error('deleteReply failed', [
                'tenant_id' => $tenant->id,
                'review' => $reviewName,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Handle API errors.
     */
    private function handleError(Response $response, string $method): void
    {
        $status = $response->status();
        $body = $response->json();

        Log::error("Google API error in {$method}", [
            'status' => $status,
            'body' => $body,
        ]);

        $message = $body['error']['message'] ?? 'Unknown error';

        if ($status === 401) {
            throw new Exception("Authentication failed: {$message}");
        }

        if ($status === 403) {
            throw new Exception("Access denied: {$message}");
        }

        if ($status === 404) {
            throw new Exception("Resource not found: {$message}");
        }

        if ($status === 429) {
            throw new Exception("Rate limit exceeded: {$message}");
        }

        throw new Exception("API error ({$status}): {$message}");
    }
}
