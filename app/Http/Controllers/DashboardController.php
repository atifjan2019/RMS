<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Services\OpenAIService;
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

        // Generate AI business summary (cache for 1 hour)
        $businessSummary = null;
        if ($stats['totalReviews'] > 0) {
            $businessSummary = Cache::remember("business_summary_{$tenant->id}", 3600, function () use ($tenant, $stats) {
                $openAI = app(OpenAIService::class);
                
                $reviewsQuery = Review::forTenant($tenant->id);
                if ($tenant->active_location_name) {
                    $reviewsQuery->where('location_name', $tenant->active_location_name);
                }
                
                $reviews = $reviewsQuery
                    ->orderBy('created_at_google', 'desc')
                    ->limit(50)
                    ->get()
                    ->map(fn($r) => [
                        'rating' => $r->rating,
                        'comment' => $r->comment,
                    ])
                    ->toArray();
                
                $businessName = $tenant->active_location_name ?? $tenant->name;
                
                return $openAI->generateBusinessSummary($reviews, $businessName, $stats);
            });
        }

        $activeLocation = $tenant->activeLocation();

        return view('dashboard', [
            'stats' => $stats,
            'tenant' => $tenant,
            'activeLocation' => $activeLocation,
            'hasGoogleConnection' => $tenant->hasGoogleConnection(),
            'isSubscribed' => $tenant->hasActiveSubscription(),
            'businessSummary' => $businessSummary,
        ]);
    }

    /**
     * Refresh the AI business summary.
     */
    public function refreshSummary()
    {
        $tenant = auth()->user()->tenant;
        
        // Clear the cached summary
        Cache::forget("business_summary_{$tenant->id}");
        
        return redirect()->route('dashboard')->with('success', 'Business insights refreshed successfully!');
    }
}
