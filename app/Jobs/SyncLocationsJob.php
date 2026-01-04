<?php

namespace App\Jobs;

use App\Models\Tenant;
use App\Models\GbpLocation;
use App\Services\GoogleBusinessProfileClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncLocationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public int $tenantId
    ) {}

    public function handle(GoogleBusinessProfileClient $client): void
    {
        $tenant = Tenant::find($this->tenantId);

        if (!$tenant || !$tenant->googleConnection) {
            Log::warning('SyncLocationsJob: No tenant or connection', ['tenant_id' => $this->tenantId]);
            return;
        }

        try {
            // Fetch all accounts
            $accounts = $client->listAccounts($tenant);

            if (empty($accounts)) {
                Log::info('SyncLocationsJob: No accounts found', ['tenant_id' => $this->tenantId]);
                return;
            }

            $syncedLocations = [];

            foreach ($accounts as $account) {
                $accountName = $account['name']; // accounts/{accountId}

                // Fetch locations for each account
                $locations = $client->listLocations($tenant, $accountName);

                foreach ($locations as $location) {
                    $locationData = $this->parseLocation($location, $accountName);

                    $gbpLocation = GbpLocation::updateOrCreate(
                        [
                            'tenant_id' => $this->tenantId,
                            'location_name' => $locationData['location_name'],
                        ],
                        $locationData
                    );

                    $syncedLocations[] = $gbpLocation->id;
                }
            }

            // Auto-select first location if none selected
            if (!$tenant->active_location_name && count($syncedLocations) > 0) {
                $firstLocation = GbpLocation::find($syncedLocations[0]);
                if ($firstLocation) {
                    $tenant->update(['active_location_name' => $firstLocation->location_name]);
                }
            }

            Log::info('SyncLocationsJob: Completed', [
                'tenant_id' => $this->tenantId,
                'locations_synced' => count($syncedLocations),
            ]);

        } catch (\Exception $e) {
            Log::error('SyncLocationsJob: Failed', [
                'tenant_id' => $this->tenantId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    private function parseLocation(array $location, string $accountName): array
    {
        $address = $location['storefrontAddress'] ?? [];
        $phones = $location['phoneNumbers'] ?? [];
        $categories = $location['categories'] ?? [];

        return [
            'tenant_id' => $this->tenantId,
            'account_name' => $accountName,
            'location_name' => $location['name'], // locations/{locationId}
            'title' => $location['title'] ?? 'Unknown',
            'primary_category' => $categories['primaryCategory']['displayName'] ?? null,
            'phone' => $phones['primaryPhone'] ?? null,
            'address_line' => implode(', ', $address['addressLines'] ?? []),
            'city' => $address['locality'] ?? null,
            'state' => $address['administrativeArea'] ?? null,
            'postal_code' => $address['postalCode'] ?? null,
            'country' => $address['regionCode'] ?? null,
            'website' => $location['websiteUri'] ?? null,
            'metadata' => $location['metadata'] ?? null,
        ];
    }
}
