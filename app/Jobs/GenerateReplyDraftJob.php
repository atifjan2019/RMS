<?php

namespace App\Jobs;

use App\Models\Review;
use App\Models\GbpLocation;
use App\Services\OpenAIService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GenerateReplyDraftJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $backoff = 30;

    public function __construct(
        public int $reviewId,
        public ?string $tone = null // null means generate all tones
    ) {}

    public function handle(OpenAIService $openai): void
    {
        $review = Review::with('location')->find($this->reviewId);

        if (!$review) {
            Log::warning('GenerateReplyDraftJob: Review not found', ['review_id' => $this->reviewId]);
            return;
        }

        $location = $review->location;
        $businessName = $location ? $location->title : 'Our Business';

        try {
            if ($this->tone) {
                // Generate single tone
                $draft = $openai->generateReplyDrafts(
                    $review->reviewer_name,
                    $review->rating,
                    $review->comment,
                    $businessName,
                    $this->tone
                );

                $drafts = $review->ai_drafts ?? [];
                $drafts[$this->tone] = $draft;

                $review->update(['ai_drafts' => $drafts]);
            } else {
                // Generate all tones
                $drafts = $openai->generateAllDrafts(
                    $review->reviewer_name,
                    $review->rating,
                    $review->comment,
                    $businessName
                );

                $review->update(['ai_drafts' => $drafts]);
            }

            // Store in cache for quick access (job status polling)
            $cacheKey = "draft_job_{$this->reviewId}";
            Cache::put($cacheKey, [
                'status' => 'completed',
                'drafts' => $review->fresh()->ai_drafts,
            ], now()->addMinutes(10));

            Log::info('GenerateReplyDraftJob: Completed', [
                'review_id' => $this->reviewId,
                'tone' => $this->tone ?? 'all',
            ]);

        } catch (\Exception $e) {
            // Store error in cache
            Cache::put("draft_job_{$this->reviewId}", [
                'status' => 'failed',
                'error' => $e->getMessage(),
            ], now()->addMinutes(10));

            Log::error('GenerateReplyDraftJob: Failed', [
                'review_id' => $this->reviewId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
