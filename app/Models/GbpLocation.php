<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GbpLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'account_name',
        'location_name',
        'title',
        'primary_category',
        'phone',
        'address_line',
        'city',
        'state',
        'postal_code',
        'country',
        'website',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Get the tenant that owns this location.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get all reviews for this location.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'location_name', 'location_name');
    }

    /**
     * Get the full address as a string.
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address_line,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Check if this is the active location for the tenant.
     */
    public function isActive(): bool
    {
        return $this->tenant->active_location_name === $this->location_name;
    }
}
