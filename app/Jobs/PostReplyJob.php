<?php

namespace App\Jobs;

use App\Models\Review;
use App\Models\Tenant;
use App\Services\GoogleBusinessProfileClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PostReplyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public int $reviewId,
        public string $replyText
    ) {}

    public function handle(GoogleBusinessProfileClient $client): void
    {
        $review = Review::find($this->reviewId);

        if (!$review) {
            Log::warning('PostReplyJob: Review not found', ['review_id' => $this->reviewId]);
            return;
        }

        $tenant = Tenant::find($review->tenant_id);

        if (!$tenant || !$tenant->googleConnection) {
            Log::warning('PostReplyJob: No tenant or connection', ['review_id' => $this->reviewId]);
            return;
        }

        try {
            $result = $client->upsertReply($tenant, $review->review_name, $this->replyText);

            if ($result) {
                $review->update([
                    'reply_text' => $this->replyText,
                    'replied_at_google' => Carbon::now(),
                    'status' => 'replied',
                ]);

                Log::info('PostReplyJob: Completed', [
                    'review_id' => $this->reviewId,
                ]);
            }

        } catch (\Exception $e) {
            Log::error('PostReplyJob: Failed', [
                'review_id' => $this->reviewId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
