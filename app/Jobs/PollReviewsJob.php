<?php

namespace App\Jobs;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PollReviewsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public function handle(): void
    {
        Log::info('PollReviewsJob: Starting hourly poll');

        // Get all tenants with active subscriptions and Google connections
        $tenants = Tenant::whereHas('googleConnection')
            ->whereNotNull('active_location_name')
            ->get();

        foreach ($tenants as $tenant) {
            // Check if tenant has active subscription
            if (!$tenant->hasActiveSubscription()) {
                continue;
            }

            // Dispatch sync job for each tenant
            SyncReviewsJob::dispatch($tenant->id, $tenant->active_location_name)
                ->onQueue('reviews');

            Log::info('PollReviewsJob: Dispatched sync for tenant', [
                'tenant_id' => $tenant->id,
            ]);
        }

        Log::info('PollReviewsJob: Completed', [
            'tenants_polled' => $tenants->count(),
        ]);
    }
}
