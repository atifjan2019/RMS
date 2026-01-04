<?php

namespace App\Http\Controllers;

use App\Models\GbpLocation;
use App\Jobs\SyncLocationsJob;
use App\Jobs\SyncReviewsJob;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LocationsController extends Controller
{
    /**
     * List all locations for the tenant.
     */
    public function index(): View
    {
        $tenant = auth()->user()->tenant;

        $locations = $tenant->locations()
            ->orderBy('title')
            ->get();

        return view('locations.index', [
            'locations' => $locations,
            'activeLocationName' => $tenant->active_location_name,
        ]);
    }

    /**
     * Set a location as active.
     */
    public function setActive(Request $request, GbpLocation $location): RedirectResponse
    {
        $tenant = auth()->user()->tenant;

        // Ensure location belongs to tenant
        if ($location->tenant_id !== $tenant->id) {
            abort(403);
        }

        $tenant->update(['active_location_name' => $location->location_name]);

        // Queue review sync in the background (only works if QUEUE_CONNECTION != sync)
        // For sync driver, skip auto-sync to avoid blocking the request
        if (config('queue.default') !== 'sync') {
            SyncReviewsJob::dispatch($tenant->id, $location->location_name)->onQueue('reviews');
            return back()->with('success', "Now managing: {$location->title}. Reviews syncing in background.");
        }

        return back()->with('success', "Now managing: {$location->title}. Use 'Sync Reviews' to fetch reviews.");
    }

    /**
     * Trigger a manual sync of locations.
     */
    public function sync(): RedirectResponse
    {
        $tenant = auth()->user()->tenant;

        if (!$tenant->googleConnection) {
            return back()->with('error', 'Please connect Google first.');
        }

        SyncLocationsJob::dispatch($tenant->id)->onQueue('google');

        return back()->with('success', 'Location sync started. Check back in a moment.');
    }

    /**
     * Trigger a manual sync of reviews for active location.
     */
    public function syncReviews(): RedirectResponse
    {
        $tenant = auth()->user()->tenant;

        if (!$tenant->active_location_name) {
            return back()->with('error', 'Please select an active location first.');
        }

        SyncReviewsJob::dispatch($tenant->id, $tenant->active_location_name)->onQueue('reviews');

        return back()->with('success', 'Review sync started. Check back in a moment.');
    }
}
