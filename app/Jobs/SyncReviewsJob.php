<?php

namespace App\Jobs;

use App\Models\Tenant;
use App\Models\Review;
use App\Services\GoogleBusinessProfileClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SyncReviewsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public int $tenantId,
        public ?string $locationName = null
    ) {}

    public function handle(GoogleBusinessProfileClient $client): void
    {
        $tenant = Tenant::find($this->tenantId);

        if (!$tenant || !$tenant->googleConnection) {
            Log::warning('SyncReviewsJob: No tenant or connection', ['tenant_id' => $this->tenantId]);
            return;
        }

        $locationName = $this->locationName ?? $tenant->active_location_name;

        if (!$locationName) {
            Log::warning('SyncReviewsJob: No location specified', ['tenant_id' => $this->tenantId]);
            return;
        }

        try {
            $pageToken = null;
            $totalSynced = 0;

            do {
                $result = $client->listReviews($tenant, $locationName, $pageToken);
                $reviews = $result['reviews'] ?? [];
                $pageToken = $result['nextPageToken'] ?? null;

                foreach ($reviews as $reviewData) {
                    $this->upsertReview($reviewData, $locationName);
                    $totalSynced++;
                }

            } while ($pageToken);

            Log::info('SyncReviewsJob: Completed', [
                'tenant_id' => $this->tenantId,
                'location' => $locationName,
                'reviews_synced' => $totalSynced,
            ]);

        } catch (\Exception $e) {
            Log::error('SyncReviewsJob: Failed', [
                'tenant_id' => $this->tenantId,
                'location' => $locationName,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    private function upsertReview(array $data, string $locationName): void
    {
        $reviewName = $data['name']; // accounts/.../locations/.../reviews/{id}
        $reviewer = $data['reviewer'] ?? [];
        $reply = $data['reviewReply'] ?? null;

        $starRating = match ($data['starRating'] ?? 'STAR_RATING_UNSPECIFIED') {
            'ONE' => 1,
            'TWO' => 2,
            'THREE' => 3,
            'FOUR' => 4,
            'FIVE' => 5,
            default => 0,
        };

        $createdAt = isset($data['createTime'])
            ? Carbon::parse($data['createTime'])
            : null;

        $repliedAt = isset($reply['updateTime'])
            ? Carbon::parse($reply['updateTime'])
            : null;

        Review::updateOrCreate(
            [
                'review_name' => $reviewName,
            ],
            [
                'tenant_id' => $this->tenantId,
                'location_name' => $locationName,
                'reviewer_name' => $reviewer['displayName'] ?? 'Anonymous',
                'reviewer_photo_url' => $reviewer['profilePhotoUrl'] ?? null,
                'rating' => $starRating,
                'comment' => $data['comment'] ?? null,
                'created_at_google' => $createdAt,
                'reply_text' => $reply['comment'] ?? null,
                'replied_at_google' => $repliedAt,
                'status' => $reply ? 'replied' : 'unreplied',
                'raw' => $data,
            ]
        );
    }
}
