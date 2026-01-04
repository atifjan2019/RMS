<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    /**
     * Show the dashboard.
     */
    public function index(): View
    {
        $tenant = auth()->user()->tenant;

        // Cache stats for 5 minutes
        $stats = Cache::remember("dashboard_stats_{$tenant->id}", 300, function () use ($tenant) {
            $reviewsQuery = Review::forTenant($tenant->id);

            if ($tenant->active_location_name) {
                $reviewsQuery->where('location_name', $tenant->active_location_name);
            }

            $totalReviews = (clone $reviewsQuery)->count();
            $unrepliedCount = (clone $reviewsQuery)->unreplied()->count();
            $avgRating = round((clone $reviewsQuery)->avg('rating') ?? 0, 1);

            // Rating distribution
            $ratingDistribution = [];
            for ($i = 5; $i >= 1; $i--) {
                $ratingDistribution[$i] = (clone $reviewsQuery)->withRating($i)->count();
            }

            // Recent activity
            $recentReviews = (clone $reviewsQuery)
                ->orderBy('created_at_google', 'desc')
                ->limit(5)
                ->get();

            return [
                'totalReviews' => $totalReviews,
                'unrepliedCount' => $unrepliedCount,
                'avgRating' => $avgRating,
                'ratingDistribution' => $ratingDistribution,
                'recentReviews' => $recentReviews,
            ];
        });

        $activeLocation = $tenant->activeLocation();

        return view('dashboard', [
            'stats' => $stats,
            'tenant' => $tenant,
            'activeLocation' => $activeLocation,
            'hasGoogleConnection' => $tenant->hasGoogleConnection(),
            'isSubscribed' => $tenant->hasActiveSubscription(),
        ]);
    }
}
