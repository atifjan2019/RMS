<?php

namespace App\Console\Commands;

use App\Models\GbpLocation;
use App\Models\Tenant;
use App\Services\GoogleBusinessProfileClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncGoogleBusinessData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gbp:sync-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Google Business Profile data for all active locations (runs daily)';

    /**
     * Execute the console command.
     */
    public function handle(GoogleBusinessProfileClient $gbpClient): int
    {
        $this->info('Starting Google Business Profile data sync...');

        // Get all tenants with Google connections
        $tenants = Tenant::whereHas('googleConnection')->get();

        $successCount = 0;
        $failureCount = 0;

        foreach ($tenants as $tenant) {
            $this->info("Syncing data for tenant: {$tenant->name} (ID: {$tenant->id})");

            // Get all locations for this tenant
            $locations = $tenant->locations;

            foreach ($locations as $location) {
                try {
                    $this->line("  - Syncing location: {$location->title}");

                    // Fetch fresh data from Google API
                    $locationData = $gbpClient->getLocationComprehensive($tenant, $location->location_name);

                    if ($locationData) {
                        // Update database with fresh data
                        $location->update([
                            'title' => $locationData['title'] ?? $location->title,
                            'phone' => $locationData['phoneNumbers']['primaryPhone'] ?? $location->phone,
                            'website' => $locationData['websiteUri'] ?? $location->website,
                            'primary_category' => $locationData['categories']['primaryCategory']['displayName'] ?? $location->primary_category,
                            'metadata' => array_merge($location->metadata ?? [], $locationData),
                        ]);

                        $this->info("    ✓ Successfully synced: {$location->title}");
                        $successCount++;
                    } else {
                        $this->warn("    ✗ No data returned for: {$location->title}");
                        $failureCount++;
                    }
                } catch (\Exception $e) {
                    $this->error("    ✗ Failed to sync {$location->title}: {$e->getMessage()}");
                    
                    Log::error('GBP sync failed', [
                        'tenant_id' => $tenant->id,
                        'location_id' => $location->id,
                        'location_name' => $location->location_name,
                        'error' => $e->getMessage(),
                    ]);

                    $failureCount++;
                }

                // Small delay to avoid rate limiting
                usleep(500000); // 0.5 second delay
            }
        }

        $this->newLine();
        $this->info("Sync completed!");
        $this->info("✓ Success: {$successCount} locations");
        if ($failureCount > 0) {
            $this->warn("✗ Failed: {$failureCount} locations");
        }

        return Command::SUCCESS;
    }
}
