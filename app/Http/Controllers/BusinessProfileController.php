<?php

namespace App\Http\Controllers;

use App\Services\GoogleBusinessProfileClient;
use App\Services\OpenAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class BusinessProfileController extends Controller
{
    public function __construct(
        private GoogleBusinessProfileClient $gbpClient,
        private OpenAIService $openAI
    ) {}

    /**
     * Show the business profile editor.
     */
    public function index(): View
    {
        $tenant = auth()->user()->tenant;
        $activeLocation = $tenant->activeLocation();

        if (!$activeLocation) {
            return view('business-profile.index', [
                'location' => null,
                'aiRecommendations' => null,
            ]);
        }

        // Get detailed location info from Google
        $locationData = Cache::remember("location_details_{$tenant->id}_{$activeLocation->location_name}", 300, function () use ($tenant, $activeLocation) {
            try {
                return $this->gbpClient->getLocation($tenant, $activeLocation->location_name);
            } catch (\Exception $e) {
                return null;
            }
        });

        // Generate AI recommendations for profile improvement
        $aiRecommendations = null;
        if ($locationData) {
            $aiRecommendations = Cache::remember("profile_recommendations_{$tenant->id}", 3600, function () use ($locationData, $tenant) {
                return $this->openAI->generateProfileRecommendations($locationData, $tenant);
            });
        }

        return view('business-profile.index', [
            'location' => $locationData,
            'aiRecommendations' => $aiRecommendations,
            'tenant' => $tenant,
        ]);
    }

    /**
     * Update the business profile.
     */
    public function update(Request $request): RedirectResponse
    {
        $tenant = auth()->user()->tenant;
        $activeLocation = $tenant->activeLocation();

        if (!$activeLocation) {
            return back()->with('error', 'No active location selected.');
        }

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:50',
            'website_uri' => 'nullable|url|max:500',
            'description' => 'nullable|string|max:750',
        ]);

        try {
            $updateData = [];
            $updateMask = [];

            if (isset($validated['title'])) {
                $updateData['title'] = $validated['title'];
                $updateMask[] = 'title';
            }

            if (isset($validated['phone_number'])) {
                $updateData['phoneNumbers'] = [
                    'primaryPhone' => $validated['phone_number']
                ];
                $updateMask[] = 'phoneNumbers.primaryPhone';
            }

            if (isset($validated['website_uri'])) {
                $updateData['websiteUri'] = $validated['website_uri'];
                $updateMask[] = 'websiteUri';
            }

            if (isset($validated['description'])) {
                $updateData['profile'] = [
                    'description' => $validated['description']
                ];
                $updateMask[] = 'profile.description';
            }

            if (empty($updateMask)) {
                return back()->with('info', 'No changes to save.');
            }

            $this->gbpClient->updateLocation(
                $tenant,
                $activeLocation->location_name,
                $updateData,
                implode(',', $updateMask)
            );

            // Clear cached location data
            Cache::forget("location_details_{$tenant->id}_{$activeLocation->location_name}");
            Cache::forget("profile_recommendations_{$tenant->id}");

            return back()->with('success', 'Business profile updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update profile: ' . $e->getMessage());
        }
    }

    /**
     * Refresh AI recommendations.
     */
    public function refreshRecommendations(): RedirectResponse
    {
        $tenant = auth()->user()->tenant;
        Cache::forget("profile_recommendations_{$tenant->id}");
        
        return back()->with('success', 'AI recommendations refreshed!');
    }
}
