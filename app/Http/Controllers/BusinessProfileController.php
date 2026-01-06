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
                'activeLocation' => null,
            ]);
        }

        // Use database data by default - no API calls
        $locationData = [
            'name' => $activeLocation->location_name,
            'title' => $activeLocation->title,
            'phoneNumbers' => [
                'primaryPhone' => $activeLocation->phone,
            ],
            'websiteUri' => $activeLocation->website,
            'storefrontAddress' => [
                'addressLines' => [$activeLocation->address_line],
                'locality' => $activeLocation->city,
                'administrativeArea' => $activeLocation->state,
                'postalCode' => $activeLocation->postal_code,
                'regionCode' => $activeLocation->country,
            ],
            'categories' => [
                'primaryCategory' => [
                    'displayName' => $activeLocation->primary_category,
                ],
            ],
            'metadata' => $activeLocation->metadata,
        ];

        return view('business-profile.index', [
            'location' => $locationData,
            'tenant' => $tenant,
            'activeLocation' => $activeLocation,
        ]);
    }

    /**
     * Manually refresh location data from Google API.
     */
    public function refreshData(): RedirectResponse
    {
        $tenant = auth()->user()->tenant;
        $activeLocation = $tenant->activeLocation();

        if (!$activeLocation) {
            return back()->with('error', 'No active location selected.');
        }

        try {
            // Fetch fresh data from Google API
            $locationData = $this->gbpClient->getLocationComprehensive($tenant, $activeLocation->location_name);

            if ($locationData) {
                // Update database with fresh data
                $activeLocation->update([
                    'title' => $locationData['title'] ?? $activeLocation->title,
                    'phone' => $locationData['phoneNumbers']['primaryPhone'] ?? $activeLocation->phone,
                    'website' => $locationData['websiteUri'] ?? $activeLocation->website,
                    'primary_category' => $locationData['categories']['primaryCategory']['displayName'] ?? $activeLocation->primary_category,
                    'metadata' => array_merge($activeLocation->metadata ?? [], $locationData),
                ]);

                return back()->with('success', 'Business profile data refreshed successfully!');
            }

            return back()->with('error', 'Failed to fetch data from Google.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error refreshing data: ' . $e->getMessage());
        }
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
