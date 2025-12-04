<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class QRCode extends Model
{
    protected $fillable = [
        'uuid', 'verification_id', 'type', 'url',
        'status', 'expires_at', 'used_at', 'user_id'
    ];

    protected $connection = 'mysql'; // Use the default MySQL connection (verif_tuk)

    protected $table = 'qr_codes'; // Explicitly set table name

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    protected $dates = ['expires_at', 'used_at'];

    /**
     * Relationship with Verification
     */
    public function verification(): BelongsTo
    {
        return $this->belongsTo(Verification::class);
    }

    /**
     * Relationship with User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if QR code is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if QR code is still valid
     */
    public function isValid(): bool
    {
        // QR codes can be scanned multiple times as long as they're not expired
        // Both 'active' and 'used' status are considered valid
        $validStatuses = ['active', 'used'];
        return in_array($this->status, $validStatuses) && !$this->isExpired();
    }

    /**
     * Check if QR code has been used
     */
    public function isUsed(): bool
    {
        return $this->status === 'used' || $this->used_at !== null;
    }

    /**
     * Get time remaining before expiry
     */
    public function getTimeRemaining(): ?string
    {
        if (!$this->expires_at) {
            return 'No expiry';
        }

        if ($this->isExpired()) {
            return 'Expired';
        }

        return $this->expires_at->diffForHumans(now(), true);
    }

    /**
     * Get formatted expiry date
     */
    public function getFormattedExpiry(): string
    {
        if (!$this->expires_at) {
            return 'Never';
        }

        return $this->expires_at->format('d M Y H:i:s');
    }

    /**
     * Scope for active QR codes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for valid QR codes
     */
    public function scopeValid($query)
    {
        return $query->whereIn('status', ['active', 'used'])
                    ->where(function($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }

    /**
     * Scope for specific QR type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Mark QR as used
     */
    public function markAsUsed(): bool
    {
        try {
            $this->update([
                'status' => 'used',
                'used_at' => now()
            ]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Extend expiry by specified days
     */
    public function extendExpiry(int $days): bool
    {
        try {
            $newExpiry = $this->expires_at
                ? $this->expires_at->addDays($days)
                : now()->addDays($days);

            $this->update(['expires_at' => $newExpiry]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}