<?php

namespace App\Http\Controllers;

use App\Jobs\SyncReviewsJob;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Handle Google Pub/Sub webhook for review notifications.
     */
    public function googlePubSub(Request $request): Response
    {
        Log::info('Pub/Sub webhook received', ['payload' => $request->all()]);

        try {
            // Decode Pub/Sub message
            $message = $request->input('message');

            if (!$message || !isset($message['data'])) {
                return response('No message data', 200);
            }

            $data = json_decode(base64_decode($message['data']), true);

            if (!$data) {
                return response('Invalid message data', 200);
            }

            // Extract location from notification
            // The exact format depends on your Pub/Sub setup
            $locationName = $data['locationName'] ?? null;

            if (!$locationName) {
                Log::warning('Pub/Sub: No location name in payload');
                return response('OK', 200);
            }

            // Find tenants that manage this location
            $tenants = Tenant::whereHas('locations', function ($query) use ($locationName) {
                $query->where('location_name', $locationName);
            })->get();

            foreach ($tenants as $tenant) {
                // Dispatch sync job
                SyncReviewsJob::dispatch($tenant->id, $locationName)
                    ->onQueue('reviews');

                Log::info('Pub/Sub: Dispatched sync job', [
                    'tenant_id' => $tenant->id,
                    'location' => $locationName,
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Pub/Sub webhook error', ['error' => $e->getMessage()]);
        }

        // Always return 200 quickly to acknowledge receipt
        return response('OK', 200);
    }

    /**
     * Handle Stripe webhooks (delegated to Cashier).
     */
    public function stripe(Request $request)
    {
        // Laravel Cashier handles this automatically via its route
        // This is just a fallback/placeholder
        return response('OK', 200);
    }
}
