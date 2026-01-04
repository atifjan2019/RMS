<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoogleConnection extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'google_subject',
        'email',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'scopes',
    ];

    protected $casts = [
        'scopes' => 'array',
        'token_expires_at' => 'datetime',
    ];

    // Encrypt tokens at rest
    protected $encrypted = [
        'access_token',
        'refresh_token',
    ];

    /**
     * Set the access token (encrypted).
     */
    public function setAccessTokenAttribute($value): void
    {
        $this->attributes['access_token'] = encrypt($value);
    }

    /**
     * Get the access token (decrypted).
     */
    public function getAccessTokenAttribute($value): ?string
    {
        if (!$value) return null;
        try {
            return decrypt($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Set the refresh token (encrypted).
     */
    public function setRefreshTokenAttribute($value): void
    {
        $this->attributes['refresh_token'] = encrypt($value);
    }

    /**
     * Get the refresh token (decrypted).
     */
    public function getRefreshTokenAttribute($value): ?string
    {
        if (!$value) return null;
        try {
            return decrypt($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get the tenant that owns this connection.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Check if the access token is expired.
     */
    public function isTokenExpired(): bool
    {
        if (!$this->token_expires_at) {
            return true;
        }

        return $this->token_expires_at->isPast();
    }
}
