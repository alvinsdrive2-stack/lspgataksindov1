<?php

namespace App\Services;

use App\Models\Verification;
use App\Models\QRCode;
use App\Models\User;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class QRService
{
    protected $baseUrl;
    protected $environment;

    public function __construct()
    {
        $this->environment = config('qr.current_environment', 'development');
        $this->baseUrl = $this->getBaseUrl();
    }

    /**
     * Generate QR code for verification
     *
     * @param Verification $verification
     * @param string $type (verifikator1, verifikator2, ketua_tuk, direktur, asesi, document)
     * @return array
     */
    public function generateQR(Verification $verification, string $type): array
    {
        try {
            // Check if QR already exists for this verification and type
            $existingQR = QRCode::where('verification_id', $verification->id)
                               ->where('type', $type)
                               ->where('status', 'active')
                               ->first();

            if ($existingQR) {
                Log::info("QR Found in Cache: {$type} for verification {$verification->id}");
                return [
                    'uuid' => $existingQR->uuid,
                    'url' => $existingQR->url,
                    'qr_data' => $existingQR,
                    'expires_at' => $existingQR->expires_at,
                    'cached' => true
                ];
            }

            $uuid = (string) Uuid::uuid4();
            $url = $this->baseUrl . $uuid;

            // Simpan ke database
            $qrCode = QRCode::create([
                'uuid' => $uuid,
                'verification_id' => $verification->id,
                'type' => $type,
                'url' => $url,
                'status' => 'active',
                'expires_at' => $this->calculateExpiry($type)
            ]);

            Log::info("QR Generated: {$type} for verification {$verification->id}");

            return [
                'uuid' => $uuid,
                'url' => $url,
                'qr_data' => $qrCode,
                'expires_at' => $qrCode->expires_at
            ];

        } catch (\Exception $e) {
            Log::error('Failed to generate QR: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate QR code for specific user (with cache to prevent duplicates)
     *
     * @param Verification $verification
     * @param string $type
     * @param User $user
     * @return array
     */
    public function generateQRForUser(Verification $verification, string $type, User $user): array
    {
        try {
            // Check if QR already exists for this user and verification
            $existingQR = QRCode::where('verification_id', $verification->id)
                               ->where('type', $type)
                               ->where('user_id', $user->id)
                               ->where('status', 'active')
                               ->first();

            if ($existingQR) {
                Log::info("QR Found in Cache: {$type} - {$user->name} for verification {$verification->id}");
                return [
                    'uuid' => $existingQR->uuid,
                    'url' => $existingQR->url,
                    'qr_data' => $existingQR,
                    'expires_at' => $existingQR->expires_at,
                    'user' => $user,
                    'cached' => true
                ];
            }

            $uuid = (string) Uuid::uuid4();
            $url = $this->baseUrl . $uuid;

            // Simpan ke database dengan user_id
            $qrCode = QRCode::create([
                'uuid' => $uuid,
                'verification_id' => $verification->id,
                'type' => $type,
                'url' => $url,
                'user_id' => $user->id,
                'status' => 'active',
                'expires_at' => $this->calculateExpiry($type)
            ]);

            Log::info("QR Generated for User: {$type} - {$user->name} for verification {$verification->id}");

            return [
                'uuid' => $uuid,
                'url' => $url,
                'qr_data' => $qrCode,
                'expires_at' => $qrCode->expires_at,
                'user' => $user,
                'cached' => false
            ];

        } catch (\Exception $e) {
            Log::error('Failed to generate QR for user: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get base URL from database config
     */
    private function getBaseUrl(): string
    {
        $config = DB::table('qr_config')
                    ->where('is_active', true)
                    ->where('name', $this->environment)
                    ->first();

        if (!$config) {
            // Fallback ke config file atau default
            return config('app.qr_base_url', 'http://localhost:8000/qr/');
        }

        return rtrim($config->base_url, '/') . '/';
    }

    /**
     * Calculate expiry based on QR type with FLEXIBLE configuration
     */
    private function calculateExpiry(string $type): ?\DateTime
    {
        // 🔥 SIMPLE SETUP: Check universal expiry first (EASIEST)
        $universalDays = config('qr.universal_expiry_days');
        if ($universalDays && is_numeric($universalDays)) {
            return now()->addDays($universalDays);
        }

        // Check if using predefined template
        $templateName = config('qr.current_template');
        if ($templateName && isset(config('qr.templates')[$templateName])) {
            $expirySettings = config('qr.templates')[$templateName][$type] ?? null;
        } else {
            // Use individual settings from expiry_settings
            $expirySettings = config('qr.expiry_settings')[$type] ?? null;
        }

        if (!$expirySettings) {
            Log::warning("No expiry settings found for QR type: {$type}");
            return now()->addDays(365); // default to 1 year
        }

        try {
            $value = $expirySettings['value'] ?? 365; // default to 365 days
            $unit = $expirySettings['unit'] ?? 'days';

            // Build expiry time based on unit
            switch (strtolower($unit)) {
                case 'minutes':
                    return now()->addMinutes($value);
                case 'hours':
                    return now()->addHours($value);
                case 'days':
                    return now()->addDays($value);
                case 'weeks':
                    return now()->addWeeks($value);
                case 'months':
                    return now()->addMonths($value);
                case 'years':
                    return now()->addYears($value);
                default:
                    Log::error("Invalid expiry unit: {$unit} for QR type: {$type}");
                    return now()->addDays(365); // fallback to 1 year
            }

        } catch (\Exception $e) {
            Log::error("Error calculating expiry for QR type {$type}: " . $e->getMessage());
            return now()->addDays(365); // fallback to 1 year
        }
    }

    /**
     * Get existing QR codes for a verification (for debugging)
     */
    public function getExistingQRCodes(int $verificationId): array
    {
        return QRCode::where('verification_id', $verificationId)
                    ->with(['verification', 'user'])
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->toArray();
    }

    /**
     * Check if QR exists and return count (for debugging)
     */
    public function checkQRExists(int $verificationId, string $type): array
    {
        $count = QRCode::where('verification_id', $verificationId)
                      ->where('type', $type)
                      ->count();

        $activeCount = QRCode::where('verification_id', $verificationId)
                            ->where('type', $type)
                            ->where('status', 'active')
                            ->count();

        return [
            'total_count' => $count,
            'active_count' => $activeCount,
            'has_duplicate' => $count > 1
        ];
    }

    /**
     * Get all available expiry settings (for debugging/configuration)
     */
    public function getExpirySettings(): array
    {
        $templateName = config('qr.current_template');

        if ($templateName && isset(config('qr.templates')[$templateName])) {
            return [
                'template' => $templateName,
                'settings' => config('qr.templates')[$templateName]
            ];
        }

        return [
            'template' => 'custom',
            'settings' => config('qr.expiry_settings')
        ];
    }

    /**
     * Set expiry for QR code dynamically (for admin overrides)
     */
    public function setCustomExpiry(QRCode $qrCode, int $value, string $unit): bool
    {
        try {
            switch (strtolower($unit)) {
                case 'minutes':
                    $expiresAt = now()->addMinutes($value);
                    break;
                case 'hours':
                    $expiresAt = now()->addHours($value);
                    break;
                case 'days':
                    $expiresAt = now()->addDays($value);
                    break;
                case 'weeks':
                    $expiresAt = now()->addWeeks($value);
                    break;
                case 'months':
                    $expiresAt = now()->addMonths($value);
                    break;
                case 'years':
                    $expiresAt = now()->addYears($value);
                    break;
                default:
                    throw new \InvalidArgumentException("Invalid unit: {$unit}");
            }

            $qrCode->update(['expires_at' => $expiresAt]);
            return true;

        } catch (\Exception $e) {
            Log::error("Failed to set custom expiry: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Validate QR code
     */
    public function validateQR(string $uuid): ?QRCode
    {
        $qrCode = QRCode::with(['verification', 'user'])
                       ->where('uuid', $uuid)
                       ->first();

        if (!$qrCode || !$qrCode->isValid()) {
            return null;
        }

        return $qrCode;
    }

    /**
     * Mark QR as used
     */
    public function markAsUsed(QRCode $qrCode): bool
    {
        try {
            $qrCode->update([
                'status' => 'used',
                'used_at' => now()
            ]);

            Log::info("QR Used: {$qrCode->uuid}");
            return true;

        } catch (\Exception $e) {
            Log::error('Failed to mark QR as used: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get QR statistics
     */
    public function getStatistics(): array
    {
        $total = QRCode::count();
        $active = QRCode::valid()->count();
        $expired = QRCode::where('status', 'expired')->count();
        $used = QRCode::where('status', 'used')->count();

        return [
            'total' => $total,
            'active' => $active,
            'expired' => $expired,
            'used' => $used,
            'active_percentage' => $total > 0 ? round(($active / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Cleanup expired QR codes
     */
    public function cleanupExpired(): int
    {
        $count = QRCode::where('expires_at', '<', now())
                       ->where('status', '!=', 'used')
                       ->update(['status' => 'expired']);

        Log::info("Cleaned up {$count} expired QR codes");
        return $count;
    }

    /**
     * Generate QR URL from UUID
     */
    public function generateQRUrl(string $uuid): string
    {
        return $this->baseUrl . $uuid;
    }
}