<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'location_name',
        'review_name',
        'reviewer_name',
        'reviewer_photo_url',
        'rating',
        'comment',
        'created_at_google',
        'reply_text',
        'replied_at_google',
        'status',
        'raw',
        'ai_drafts',
    ];

    protected $casts = [
        'rating' => 'integer',
        'created_at_google' => 'datetime',
        'replied_at_google' => 'datetime',
        'raw' => 'array',
        'ai_drafts' => 'array',
    ];

    /**
     * Get the tenant that owns this review.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the location this review belongs to.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(GbpLocation::class, 'location_name', 'location_name');
    }

    /**
     * Scope to filter unreplied reviews.
     */
    public function scopeUnreplied(Builder $query): Builder
    {
        return $query->where('status', 'unreplied');
    }

    /**
     * Scope to filter replied reviews.
     */
    public function scopeReplied(Builder $query): Builder
    {
        return $query->where('status', 'replied');
    }

    /**
     * Scope to filter by rating.
     */
    public function scopeWithRating(Builder $query, int $rating): Builder
    {
        return $query->where('rating', $rating);
    }

    /**
     * Scope to search by keyword.
     */
    public function scopeSearch(Builder $query, string $keyword): Builder
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('comment', 'like', "%{$keyword}%")
              ->orWhere('reviewer_name', 'like', "%{$keyword}%");
        });
    }

    /**
     * Scope to filter by tenant.
     */
    public function scopeForTenant(Builder $query, int $tenantId): Builder
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Check if review has been replied to.
     */
    public function isReplied(): bool
    {
        return $this->status === 'replied';
    }

    /**
     * Check if AI drafts have been generated.
     */
    public function hasDrafts(): bool
    {
        return !empty($this->ai_drafts);
    }

    /**
     * Get a specific draft by tone.
     */
    public function getDraft(string $tone): ?string
    {
        return $this->ai_drafts[$tone] ?? null;
    }
}
