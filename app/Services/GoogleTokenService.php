<?php

namespace App\Services;

use App\Models\GoogleConnection;
use App\Models\Tenant;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleTokenService
{
    private string $clientId;
    private string $clientSecret;
    private string $tokenUrl = 'https://oauth2.googleapis.com/token';

    public function __construct()
    {
        $this->clientId = config('services.google.client_id');
        $this->clientSecret = config('services.google.client_secret');
    }

    /**
     * Get a valid access token for the tenant, refreshing if needed.
     */
    public function getAccessToken(Tenant $tenant): ?string
    {
        $connection = $tenant->googleConnection;

        if (!$connection) {
            Log::warning("No Google connection for tenant {$tenant->id}");
            return null;
        }

        // Check cache first
        $cacheKey = "google_access_token_{$tenant->id}";
        $cachedToken = Cache::get($cacheKey);

        if ($cachedToken) {
            return $cachedToken;
        }

        // Check if current token is still valid
        if (!$connection->isTokenExpired()) {
            $token = $connection->access_token;
            $ttl = $connection->token_expires_at->diffInSeconds(now()) - 60; // 1 min buffer
            if ($ttl > 0) {
                Cache::put($cacheKey, $token, $ttl);
            }
            return $token;
        }

        // Refresh the token
        return $this->refreshAccessToken($connection, $cacheKey);
    }

    /**
     * Refresh the access token using the refresh token.
     */
    public function refreshAccessToken(GoogleConnection $connection, ?string $cacheKey = null): ?string
    {
        try {
            $response = Http::asForm()->post($this->tokenUrl, [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'refresh_token' => $connection->refresh_token,
                'grant_type' => 'refresh_token',
            ]);

            if (!$response->successful()) {
                Log::error('Failed to refresh Google token', [
                    'tenant_id' => $connection->tenant_id,
                    'error' => $response->json(),
                ]);
                return null;
            }

            $data = $response->json();
            $accessToken = $data['access_token'];
            $expiresIn = $data['expires_in'] ?? 3600;

            // Update the connection
            $connection->update([
                'access_token' => $accessToken,
                'token_expires_at' => now()->addSeconds($expiresIn),
            ]);

            // Cache the token
            $ttl = $expiresIn - 60; // 1 min buffer
            if ($cacheKey && $ttl > 0) {
                Cache::put($cacheKey, $accessToken, $ttl);
            }

            return $accessToken;
        } catch (\Exception $e) {
            Log::error('Exception refreshing Google token', [
                'tenant_id' => $connection->tenant_id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Revoke all tokens for a connection.
     */
    public function revokeTokens(GoogleConnection $connection): bool
    {
        try {
            $response = Http::post('https://oauth2.googleapis.com/revoke', [
                'token' => $connection->refresh_token,
            ]);

            // Clear cache
            Cache::forget("google_access_token_{$connection->tenant_id}");

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Exception revoking Google tokens', [
                'tenant_id' => $connection->tenant_id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
