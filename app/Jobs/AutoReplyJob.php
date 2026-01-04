<?php

namespace App\Jobs;

use App\Models\Review;
use App\Models\Tenant;
use App\Services\OpenAIService;
use App\Services\GoogleBusinessProfileClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AutoReplyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $backoff = 60;

    public function __construct(
        public int $reviewId
    ) {}

    public function handle(OpenAIService $openai, GoogleBusinessProfileClient $client): void
    {
        $review = Review::with('location')->find($this->reviewId);

        if (!$review) {
            Log::warning('AutoReplyJob: Review not found', ['review_id' => $this->reviewId]);
            return;
        }

        // Check if already replied (user may have replied manually before this job runs)
        if ($review->status === 'replied' || $review->reply_text) {
            Log::info('AutoReplyJob: Review already has reply, skipping', ['review_id' => $this->reviewId]);
            return;
        }

        $tenant = Tenant::find($review->tenant_id);

        if (!$tenant) {
            Log::warning('AutoReplyJob: Tenant not found', ['review_id' => $this->reviewId]);
            return;
        }

        // Re-check if auto-reply is still enabled and this rating is still eligible
        if (!$tenant->shouldAutoReply($review->rating)) {
            Log::info('AutoReplyJob: Auto-reply no longer enabled for this rating', [
                'review_id' => $this->reviewId,
                'rating' => $review->rating,
            ]);
            return;
        }

        if (!$tenant->googleConnection) {
            Log::warning('AutoReplyJob: No Google connection', ['review_id' => $this->reviewId]);
            return;
        }

        $location = $review->location;
        $businessName = $location ? $location->title : 'Our Business';
        $tone = $tenant->auto_reply_tone ?? 'professional';

        try {
            // Step 1: Generate the reply using OpenAI
            $replyText = $openai->generateReplyDrafts(
                $review->reviewer_name,
                $review->rating,
                $review->comment,
                $businessName,
                $tone
            );

            if (empty($replyText)) {
                Log::error('AutoReplyJob: Empty reply generated', ['review_id' => $this->reviewId]);
                return;
            }

            Log::info('AutoReplyJob: Generated reply', [
                'review_id' => $this->reviewId,
                'tone' => $tone,
                'reply_length' => strlen($replyText),
            ]);

            // Step 2: Post the reply to Google
            $result = $client->upsertReply($tenant, $review->review_name, $replyText);

            if ($result) {
                // Update the review with the posted reply
                $review->update([
                    'reply_text' => $replyText,
                    'replied_at_google' => Carbon::now(),
                    'status' => 'replied',
                    'ai_drafts' => array_merge($review->ai_drafts ?? [], [$tone => $replyText]),
                ]);

                Log::info('AutoReplyJob: Successfully posted auto-reply', [
                    'review_id' => $this->reviewId,
                    'rating' => $review->rating,
                    'tone' => $tone,
                ]);
            } else {
                Log::error('AutoReplyJob: Failed to post reply to Google', ['review_id' => $this->reviewId]);
            }

        } catch (\Exception $e) {
            Log::error('AutoReplyJob: Failed', [
                'review_id' => $this->reviewId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
