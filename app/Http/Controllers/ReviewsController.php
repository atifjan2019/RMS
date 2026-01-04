<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Jobs\GenerateReplyDraftJob;
use App\Jobs\PostReplyJob;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;

class ReviewsController extends Controller
{
    /**
     * List reviews with filters.
     */
    public function index(Request $request): View
    {
        $tenant = auth()->user()->tenant;

        $query = Review::forTenant($tenant->id)
            ->orderBy('created_at_google', 'desc');

        // Filter by location
        if ($tenant->active_location_name) {
            $query->where('location_name', $tenant->active_location_name);
        }

        // Filter by status
        if ($request->filled('status')) {
            $status = $request->get('status');
            if ($status === 'unreplied') {
                $query->unreplied();
            } elseif ($status === 'replied') {
                $query->replied();
            }
        }

        // Filter by rating
        if ($request->filled('rating')) {
            $query->withRating((int) $request->get('rating'));
        }

        // Search by keyword
        if ($request->filled('search')) {
            $query->search($request->get('search'));
        }

        $reviews = $query->paginate(20)->withQueryString();

        // Stats for the dashboard
        $stats = Cache::remember("review_stats_{$tenant->id}", 300, function () use ($tenant) {
            $baseQuery = Review::forTenant($tenant->id);
            if ($tenant->active_location_name) {
                $baseQuery->where('location_name', $tenant->active_location_name);
            }

            return [
                'total' => (clone $baseQuery)->count(),
                'unreplied' => (clone $baseQuery)->unreplied()->count(),
                'average_rating' => round((clone $baseQuery)->avg('rating') ?? 0, 1),
            ];
        });

        return view('reviews.index', [
            'reviews' => $reviews,
            'stats' => $stats,
            'filters' => [
                'status' => $request->get('status'),
                'rating' => $request->get('rating'),
                'search' => $request->get('search'),
            ],
        ]);
    }

    /**
     * Generate AI draft for a review (dispatches job, returns immediately).
     */
    public function draft(Request $request, Review $review): JsonResponse
    {
        $tenant = auth()->user()->tenant;

        // Ensure review belongs to tenant
        if ($review->tenant_id !== $tenant->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $tone = $request->input('tone'); // null = all tones

        // Set pending status in cache
        Cache::put("draft_job_{$review->id}", [
            'status' => 'pending',
        ], now()->addMinutes(10));

        // Dispatch the job
        GenerateReplyDraftJob::dispatch($review->id, $tone)->onQueue('ai');

        return response()->json([
            'success' => true,
            'message' => 'Generating drafts...',
            'review_id' => $review->id,
        ]);
    }

    /**
     * Check status of draft generation job.
     */
    public function draftStatus(Review $review): JsonResponse
    {
        $tenant = auth()->user()->tenant;

        if ($review->tenant_id !== $tenant->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $cached = Cache::get("draft_job_{$review->id}");

        if (!$cached) {
            // Check if review has drafts in DB
            $review->refresh();
            if ($review->hasDrafts()) {
                return response()->json([
                    'status' => 'completed',
                    'drafts' => $review->ai_drafts,
                ]);
            }
            return response()->json(['status' => 'unknown']);
        }

        return response()->json($cached);
    }

    /**
     * Post a reply to a review.
     */
    public function reply(Request $request, Review $review): JsonResponse
    {
        $tenant = auth()->user()->tenant;

        if ($review->tenant_id !== $tenant->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'reply' => 'required|string|max:4096',
        ]);

        $replyText = $request->input('reply');

        // Dispatch job to post reply
        PostReplyJob::dispatch($review->id, $replyText)->onQueue('google');

        // Optimistically update the review
        $review->update([
            'reply_text' => $replyText,
            'status' => 'replied',
            'replied_at_google' => now(),
        ]);

        // Clear stats cache
        Cache::forget("review_stats_{$tenant->id}");

        return response()->json([
            'success' => true,
            'message' => 'Reply submitted successfully!',
        ]);
    }

    /**
     * Show a single review (for modal/detail view).
     */
    public function show(Review $review): JsonResponse
    {
        $tenant = auth()->user()->tenant;

        if ($review->tenant_id !== $tenant->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'review' => $review->load('location'),
        ]);
    }
}
