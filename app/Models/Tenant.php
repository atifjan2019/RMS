<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Cashier\Billable;

class Tenant extends Model
{
    use HasFactory, Billable;

    protected $fillable = [
        'name',
        'active_location_name',
        'auto_reply_enabled',
        'auto_reply_tone',
        'auto_reply_stars',
        'auto_reply_delay_minutes',
    ];

    protected $casts = [
        'auto_reply_enabled' => 'boolean',
        'auto_reply_tone' => 'array',
        'auto_reply_stars' => 'array',
        'auto_reply_delay_minutes' => 'integer',
    ];

    /**
     * Get all users belonging to this tenant.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the Google connection for this tenant.
     */
    public function googleConnection(): HasOne
    {
        return $this->hasOne(GoogleConnection::class);
    }

    /**
     * Get all GBP locations for this tenant.
     */
    public function locations(): HasMany
    {
        return $this->hasMany(GbpLocation::class);
    }

    /**
     * Get all reviews for this tenant.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get all reply templates for this tenant.
     */
    public function replyTemplates(): HasMany
    {
        return $this->hasMany(ReplyTemplate::class);
    }

    /**
     * Get the active location.
     */
    public function activeLocation(): ?GbpLocation
    {
        if (!$this->active_location_name) {
            return null;
        }

        return $this->locations()
            ->where('location_name', $this->active_location_name)
            ->first();
    }

    /**
     * Check if tenant has an active subscription.
     */
    public function hasActiveSubscription(): bool
    {
        // Admin users bypass subscription check
        if ($this->users()->where('is_admin', true)->exists()) {
            return true;
        }
        
        return $this->subscribed('default');
    }

    /**
     * Check if Google is connected.
     */
    public function hasGoogleConnection(): bool
    {
        return $this->googleConnection()->exists();
    }

    /**
     * Check if auto-reply should be sent for a given star rating.
     */
    public function shouldAutoReply(int $rating): bool
    {
        if (!$this->auto_reply_enabled) {
            return false;
        }

        $allowedStars = $this->auto_reply_stars ?? [];
        
        return in_array($rating, $allowedStars);
    }

    /**
     * Cashier subscriptions relation for Tenant billing.
     *
     * Cashier's default relation uses $this->getForeignKey() (tenant_id), but
     * our subscriptions table uses billable_id / billable_type.
     */
    public function subscriptions()
    {
        return $this->morphMany(\Laravel\Cashier\Cashier::$subscriptionModel, 'billable')
            ->orderBy('created_at', 'desc');
    }
}
