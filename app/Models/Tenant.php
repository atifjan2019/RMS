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
        return $this->subscribed('default');
    }

    /**
     * Check if Google is connected.
     */
    public function hasGoogleConnection(): bool
    {
        return $this->googleConnection()->exists();
    }
}
