# QR Code Generator System Documentation

## ⚡ Quick Start Guide

### 🎯 **INSTANT SETUP: 3 Easy Ways to Configure QR Expiry**

#### **Option 1: Individual Customization (Most Flexible)**
```env
# .env - Customize each QR type separately
QR_EXPIRY_VERIFIKATOR1_DAYS=7       # 7 days
QR_EXPIRY_VERIFIKATOR2_HOURS=48     # 2 hours instead of days
QR_EXPIRY_KETUA_TUK_DAYS=5          # 5 days
QR_EXPIRY_DIREKTUR_WEEKS=2          # 2 weeks instead of days
QR_EXPIRY_ASESI_MINUTES=180         # 3 hours
QR_EXPIRY_DOCUMENT_HOURS=24         # 24 hours
```

#### **Option 2: Template-Based Setup (Fastest)**
```env
# .env - Just pick a template and you're done!
QR_EXPIRY_TEMPLATE=development      # Quick testing (minutes/hours)
# QR_EXPIRY_TEMPLATE=testing        # Faster testing
# QR_EXPIRY_TEMPLATE=standard       # Normal operation
# QR_EXPIRY_TEMPLATE=strict         # High security
# QR_EXPIRY_TEMPLATE=relaxed        # Extended validity
```

#### **Option 3: SIMPLE UNIVERSAL SETUP (Easiest)**
```env
# .env - One line setup for all QR types!
QR_EXPIRY_DAYS=365                  # 🎯 SET 1 YEAR (365 DAYS)
# QR_EXPIRY_DAYS=180               # 6 months
# QR_EXPIRY_DAYS=90                # 3 months
# QR_EXPIRY_DAYS=30                # 1 month
# QR_EXPIRY_DAYS=7                 # 1 week
```

#### **Option 4: Mixed Approach**
```env
# .env - Use template + override specific types
QR_EXPIRY_TEMPLATE=standard         # Start with standard template
QR_EXPIRY_VERIFIKATOR1_DAYS=3       # Override: only 3 days for verifikator1
QR_EXPIRY_KETUA_TUK_HOURS=12        # Override: only 12 hours for ketua_tuk
```

### 🚀 **Ready-to-Use Templates & Simple Setup**

#### **🎯 SUPER SIMPLE SETUP (RECOMMENDED)**
```env
# .env - Just ONE LINE!
QR_EXPIRY_DAYS=365                  # 1 YEAR for ALL QR types
```

| QR_EXPIRY_DAYS Value | Duration | Best For |
|----------------------|----------|-----------|
| **365** | 1 Year | 🎯 **Production, Long-term Validity** |
| 180 | 6 Months | Extended projects |
| 90 | 3 Months | Medium-term validation |
| 30 | 1 Month | Regular operations |
| 14 | 2 Weeks | Short-term projects |
| 7 | 1 Week | Testing purposes |

#### **Ready-to-Use Templates**

| Template | Verifikator1 | Ketua TUK | Direktur | Document | Use Case |
|----------|-------------|-----------|----------|----------|----------|
| **development** | 1 hour | 30 minutes | 2 hours | 10 minutes | Local testing |
| **testing** | 30 minutes | 15 minutes | 1 hour | 5 minutes | CI/CD testing |
| **standard** | 7 days | 5 days | 30 days | 24 hours | Normal production |
| **strict** | 3 days | 2 days | 7 days | 4 hours | High security |
| **relaxed** | 14 days | 10 days | 60 days | 72 hours | Extended period |

### 💡 **Quick Implementation**

**EASIEST WAY - Universal Expiry:**
1. **Step 1**: Add to .env: `QR_EXPIRY_DAYS=365`
2. **Step 2**: Run `php artisan config:clear`
3. **Step 3**: Test with `php artisan tinker`:
```bash
$qrService = new QRService();
dump($qrService->getExpirySettings());
```

**ADVANCED WAY - Individual Setup:**
1. **Step 1**: Choose setup method from options above
2. **Step 2**: Configure your .env settings
3. **Step 3**: Run `php artisan config:clear`
4. **Step 4**: Test as shown above

**That's it! Your QR system is ready to use!** 🎉

### 🔥 **Current Default: 1 Year (365 Days)**
All QR codes will expire after 365 days. Just change `QR_EXPIRY_DAYS=365` to any value you want!

---

## 📋 Overview

Dokumentasi ini menjelaskan implementasi sistem QR Code Generator otomatis untuk verifikasi TUK yang terintegrasi dengan sistem existing. Sistem ini memungkinkan pembuatan QR otomatis dari localhost atau production environment dengan tracking database yang lengkap.

## 🎯 Purpose

- Membuat QR code otomatis untuk verifikasi TUK
- Mendukung multi-environment (localhost/staging/production)
- Tracking QR yang sudah digenerate dan discan
- Auto-expiration untuk security
- Integration dengan sistem FileController yang existing
- Support dropdown untuk ketua TUK dengan role-based selection

---

## 📊 Database Schema

### 1. Table `qr_codes`

Tabel utama untuk menyimpan QR codes yang digenerate.

```sql
CREATE TABLE qr_codes (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(36) UNIQUE NOT NULL,
    verification_id BIGINT NOT NULL,
    type ENUM('verifikator1', 'verifikator2', 'ketua_tuk', 'direktur', 'asesi', 'document') NOT NULL,
    url VARCHAR(255) NOT NULL,
    status ENUM('active', 'expired', 'used') DEFAULT 'active',
    expires_at TIMESTAMP NULL,
    used_at TIMESTAMP NULL,
    user_id BIGINT NULL, -- User ID untuk tracking siapa yang generate
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (verification_id) REFERENCES verifications(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_uuid (uuid),
    INDEX idx_verification_id (verification_id),
    INDEX idx_status (status),
    INDEX idx_type_user (type, user_id)
);
```

**Fields Explanation:**
- `uuid`: Unique identifier untuk QR code (UUID4)
- `verification_id`: Foreign key ke tabel verifications
- `type`: Tipe QR code (verifikator1, verifikator2, ketua_tuk, direktur, asesi, document)
- `url`: Full URL yang akan dibaca oleh QR scanner
- `status`: Status QR code (active, expired, used)
- `expires_at`: Waktu kadaluarsa QR code
- `used_at`: Waktu QR code dipakai/discan
- `user_id`: User ID yang melakukan generate QR (untuk tracking)

### 2. Table `qr_config`

Tabel konfigurasi untuk base URL QR code berdasarkan environment.

```sql
CREATE TABLE qr_config (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    base_url VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Seed data
INSERT INTO qr_config (name, base_url, description) VALUES
('production', 'https://barcode.lspgatensi.id/', 'Production QR verification URL'),
('development', 'http://localhost:8000/qr/', 'Local development URL'),
('staging', 'https://staging-barcode.lspgatensi.id/', 'Staging environment URL');
```

### 3. Update Table `barcodes`

Menambahkan kolom baru untuk melacak QR UUID yang digenerate.

```sql
ALTER TABLE barcodes
ADD COLUMN qr_uuid VARCHAR(36) NULL AFTER url,
ADD COLUMN qr_type ENUM('verifikator1', 'verifikator2', 'ketua_tuk', 'direktur', 'document') NULL AFTER qr_uuid,
ADD COLUMN qr_status ENUM('active', 'expired', 'used') DEFAULT 'active' AFTER qr_type,
ADD COLUMN user_id BIGINT NULL AFTER qr_status,
ADD INDEX idx_qr_uuid (qr_uuid),
ADD FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL;
```

### 4. Update `verifications` Table

Menambahkan kolom untuk ketua TUK dan direktur tracking.

```sql
ALTER TABLE verifications
ADD COLUMN ketua_tuk_id BIGINT NULL AFTER ketua_tuk,
ADD COLUMN direktur_id BIGINT NULL AFTER approved,
ADD COLUMN direktur_name VARCHAR(255) NULL AFTER direktur_id,
ADD FOREIGN KEY (ketua_tuk_id) REFERENCES users(id) ON DELETE SET NULL,
ADD FOREIGN KEY (direktur_id) REFERENCES users(id) ON DELETE SET NULL,
ADD INDEX idx_ketua_tuk (ketua_tuk_id),
ADD INDEX idx_direktur (direktur_id);
```

---

## 🏗️ System Architecture

### Component Diagram

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Frontend      │    │  API Gateway    │    │   QR Service    │
│   (PDF/HTML)    │◄──►│  (Controllers)  │◄──►│   (Business)    │
└─────────────────┘    └─────────────────┘    └─────────────────┘
                                │                        │
                                ▼                        ▼
                       ┌─────────────────┐    ┌─────────────────┐
                       │   Routes        │    │   Database      │
                       │   (Endpoint)    │    │   (MySQL)       │
                       └─────────────────┘    └─────────────────┘
```

### Data Flow

1. **QR Generation**:
   ```
   FileController → QRService → Database → PDF with QR
   ```

2. **QR Verification**:
   ```
   Mobile Scanner → Route → QRVerificationController → Database → Response
   ```

---

## 👥 Role-Based QR Generation

### 1. Ketua TUK Dropdown Implementation

#### Frontend Dropdown (sewaktu.blade.php)

Update field ketua TUK menjadi dropdown dengan data dari users table:

```php
<!-- Replace baris 175-180 di sewaktu.blade.php -->
<label for="ketua" class="form-control w-full max-w-xs">
    <div class="label">
        <span class="label-text text-white">Ketua TUK</span>
    </div>
    <select id="ketua" name="ketua_tuk" class="select select-bordered w-full max-w-xs" required>
        <option disabled selected>Pilih Ketua TUK</option>
        @foreach ($ketuaTukList as $ketua)
            <option value="{{ $ketua->id }}" {{ old('ketua_tuk') == $ketua->id ? 'selected' : '' }}>
                {{ $ketua->name }} ({{ $ketua->email }})
            </option>
        @endforeach
    </select>
</label>
```

#### Backend Controller Update

Update method `sewaktu()` di `FileController.php`:

```php
public function sewaktu()
{
    $allAsesor = DB::connection("mygatensi")->table("myasesorbnsp")->get();
    $allJabker = DB::connection("mygatensi")->table("myjabatankerja")->select(['id_jabatan_kerja', 'jabatan_kerja'])->get();

    // Ambil data ketua TUK dari users dengan role 'ketua_tuk'
    $ketuaTukList = \App\Models\User::where('role', 'ketua_tuk')
                                   ->where('is_active', true)
                                   ->orderBy('name')
                                   ->get();

    return view('file.sewaktu', compact('allJabker', 'allAsesor', 'ketuaTukList'));
}
```

#### Handle Ketua TUK Selection

Update method `createFileSewaktu()`:

```php
public function createFileSewaktu(Request $request)
{
    // ... existing validation code ...

    $verification = Verification::create([
        // ... existing fields ...
        'ketua_tuk_id' => $request->ketua_tuk, // ID dari dropdown
        'ketua_tuk' => User::find($request->ketua_tuk)->name, // Nama lengkap
        // ... other fields ...
    ]);

    // Generate QR untuk ketua TUK
    if ($request->ketua_tuk) {
        $qrKetuaTuk = $this->qrService->generateQR($verification, 'ketua_tuk');

        // Simpan ke table barcodes
        DB::connection('reguler')->table('barcodes')->insert([
            'nama' => User::find($request->ketua_tuk)->name,
            'id_izin' => User::find($request->ketua_tuk)->id,
            'jabatan' => 'Ketua TUK',
            'url' => $qrKetuaTuk['url'],
            'qr_uuid' => $qrKetuaTuk['uuid'],
            'qr_type' => 'ketua_tuk',
            'qr_status' => 'active',
            'user_id' => $request->ketua_tuk,
            'created_at' => now()
        ]);
    }

    // ... continue with existing code ...
}
```

### 2. Direktur QR Generation (Auto User Detection)

#### Update ConfirmController.php

Update method `confirm()` untuk menggunakan direktur yang sedang login:

```php
public function confirm($id)
{
    // Get logged-in direktur user
    $direkturUser = Auth::user();

    if (!$direkturUser || $direkturUser->role !== 'direktur') {
        return back()->with('error', 'Akses ditolak. Hanya direktur yang dapat mengkonfirmasi.');
    }

    $direktur_1 = (string) Uuid::uuid4(); // QR untuk signature pertama
    $direktur_2 = (string) Uuid::uuid4(); // QR untuk signature kedua

    $verification = Verification::where('id', $id)->first();

    // Generate QR codes untuk direktur yang login
    $qrDirektur1 = $this->qrService->generateQRForUser($verification, 'direktur', $direkturUser);
    $qrDirektur2 = $this->qrService->generateQRForUser($verification, 'direktur', $direkturUser);

    // Update verification dengan direktur info
    $verification->update([
        'approved' => true,
        'direktur_id' => $direkturUser->id,
        'direktur_name' => $direkturUser->name
    ]);

    // Simpan ke table barcodes
    DB::connection('reguler')->table('barcodes')->insert([
        [
            'nama' => $direkturUser->name,
            'id_izin' => $direkturUser->id,
            'jabatan' => 'Direktur LSP',
            'url' => $qrDirektur1['url'],
            'qr_uuid' => $qrDirektur1['uuid'],
            'qr_type' => 'direktur',
            'qr_status' => 'active',
            'user_id' => $direkturUser->id,
            'created_at' => $verification->created_at
        ],
        [
            'nama' => $direkturUser->name,
            'id_izin' => $direkturUser->id,
            'jabatan' => 'Direktur LSP',
            'url' => $qrDirektur2['url'],
            'qr_uuid' => $qrDirektur2['uuid'],
            'qr_type' => 'direktur',
            'qr_status' => 'active',
            'user_id' => $direkturUser->id,
            'created_at' => now()->modify('+1 day')
        ]
    ]);

    // ... continue with existing PDF generation code using dynamic values:

    // Di loop PDF generation:
    if($i === 1) {
        $fpdi->write2DBarcode($qrDirektur1['url'], 'QRCODE,H', 30, 224, 20, 20);
    }

    if ($i === $signaturePage) {
        $fpdi->write2DBarcode($qrDirektur2['url'], 'QRCODE,H', 30, 254, 20, 20);
    }

    // ... rest of the code ...
}
```

### 3. Enhanced QR Service untuk User-Based Generation

Update `QRService.php` untuk support user-based generation:

```php
/**
 * Generate QR code for specific user
 *
 * @param Verification $verification
 * @param string $type
 * @param User $user
 * @return array
 */
public function generateQRForUser(Verification $verification, string $type, \App\Models\User $user): array
{
    try {
        $uuid = (string) Uuid::uuid4();
        $url = $this->baseUrl . $uuid;

        // Simpan ke database dengan user_id
        $qrCode = QRCode::create([
            'uuid' => $uuid,
            'verification_id' => $verification->id,
            'type' => $type,
            'url' => $url,
            'user_id' => $user->id,
            'expires_at' => $this->calculateExpiry($type)
        ]);

        Log::info("QR Generated for User: {$type} - {$user->name} for verification {$verification->id}");

        return [
            'uuid' => $uuid,
            'url' => $url,
            'qr_data' => $qrCode,
            'expires_at' => $qrCode->expires_at,
            'user' => $user
        ];

    } catch (\Exception $e) {
        Log::error('Failed to generate QR for user: ' . $e->getMessage());
        throw $e;
    }
}
```

---

## 💻 Implementation Details

### 1. QR Service (`app/Services/QRService.php`)

Service class utama untuk generate QR codes dengan environment detection.

```php
<?php

namespace App\Services;

use App\Models\Verification;
use App\Models\QRCode;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QRService
{
    protected $baseUrl;
    protected $environment;

    public function __construct()
    {
        $this->environment = config('app.qr_environment', 'development');
        $this->baseUrl = $this->getBaseUrl();
    }

    /**
     * Generate QR code for verification
     *
     * @param Verification $verification
     * @param string $type (verifikator1, verifikator2, asesi, document)
     * @return array
     */
    public function generateQR(Verification $verification, string $type): array
    {
        try {
            $uuid = (string) Uuid::uuid4();
            $url = $this->baseUrl . $uuid;

            // Simpan ke database
            $qrCode = QRCode::create([
                'uuid' => $uuid,
                'verification_id' => $verification->id,
                'type' => $type,
                'url' => $url,
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
        $qrCode = QRCode::with('verification')
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
}
```

### 2. QR Model (`app/Models/QRCode.php`)

Eloquent model untuk QR codes.

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class QRCode extends Model
{
    protected $fillable = [
        'uuid', 'verification_id', 'type', 'url',
        'status', 'expires_at', 'used_at'
    ];

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
        return $this->status === 'active' && !$this->isExpired();
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
        return $query->where('status', 'active')
                    ->where(function($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }
}
```

### 3. QR Verification Controller

Controller untuk handle QR scanning dan verification.

```php
<?php

namespace App\Http\Controllers;

use App\Models\QRCode;
use App\Services\QRService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class QRVerificationController extends Controller
{
    protected $qrService;

    public function __construct(QRService $qrService)
    {
        $this->qrService = $qrService;
    }

    /**
     * Verify QR code via UUID
     *
     * @param string $uuid
     * @return JsonResponse
     */
    public function verify($uuid): JsonResponse
    {
        try {
            $qrCode = $this->qrService->validateQR($uuid);

            if (!$qrCode) {
                return response()->json([
                    'success' => false,
                    'error' => 'QR Code tidak valid atau sudah kadaluarsa',
                    'code' => 'INVALID_QR'
                ], 400);
            }

            // Mark sebagai used
            $this->qrService->markAsUsed($qrCode);

            // Prepare response data
            $verification = $qrCode->verification;

            return response()->json([
                'success' => true,
                'message' => 'QR Code valid',
                'data' => [
                    'qr' => [
                        'uuid' => $qrCode->uuid,
                        'type' => $qrCode->type,
                        'scanned_at' => now()->format('Y-m-d H:i:s')
                    ],
                    'verification' => [
                        'id' => $verification->id,
                        'tuk' => $verification->tuk,
                        'verificator' => $verification->verificator,
                        'jenis_tuk' => $verification->jenis_tuk,
                        'created_at' => $verification->created_at->format('Y-m-d H:i:s')
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Terjadi kesalahan saat verifikasi',
                'code' => 'SYSTEM_ERROR'
            ], 500);
        }
    }

    /**
     * Web view untuk QR scan result
     */
    public function scanResult($uuid)
    {
        $qrCode = $this->qrService->validateQR($uuid);

        if (!$qrCode) {
            return view('qr.error', [
                'error' => 'QR Code tidak valid atau sudah kadaluarsa'
            ]);
        }

        // Mark as used for web view
        $this->qrService->markAsUsed($qrCode);

        return view('qr.success', [
            'qrCode' => $qrCode,
            'verification' => $qrCode->verification,
            'scannedAt' => now()
        ]);
    }

    /**
     * Check QR status without marking as used
     */
    public function checkStatus($uuid): JsonResponse
    {
        $qrCode = QRCode::where('uuid', $uuid)
                       ->with('verification')
                       ->first();

        if (!$qrCode) {
            return response()->json([
                'success' => false,
                'error' => 'QR Code tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'uuid' => $qrCode->uuid,
                'status' => $qrCode->status,
                'type' => $qrCode->type,
                'is_valid' => $qrCode->isValid(),
                'is_expired' => $qrCode->isExpired(),
                'is_used' => $qrCode->isUsed(),
                'expires_at' => $qrCode->expires_at?->format('Y-m-d H:i:s'),
                'used_at' => $qrCode->used_at?->format('Y-m-d H:i:s'),
                'time_remaining' => $qrCode->getTimeRemaining()
            ]
        ]);
    }
}
```

---

## 🔄 Integration with Existing System

### Update FileController.php

Modifikasi method `verify()` untuk menggunakan QR service otomatis.

```php
<?php

namespace App\Http\Controllers;

use App\Services\QRService;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use DB;
use Fpdi;

class FileController extends Controller
{
    protected $qrService;

    public function __construct(QRService $qrService)
    {
        $this->qrService = $qrService;
    }

    public function verify(Request $request)
    {
        $verification = Verification::where('id', $request->id)->first();

        // ... kode existing untuk ambil data verifikasi

        // GENERATE QR OTOMATIS
        $qrVerifikator1 = $this->qrService->generateQR($verification, 'verifikator1');

        // Update barcode table dengan QR data
        DB::connection('reguler')->table('barcodes')->insert([
            'nama' => $verification->verificator,
            'id_izin' => $verifikator1->Noreg,
            'jabatan' => 'Verifikator 1',
            'url' => $qrVerifikator1['url'],
            'qr_uuid' => $qrVerifikator1['uuid'],
            'qr_type' => 'verifikator1',
            'qr_status' => 'active',
            'created_at' => $verification->created_at
        ]);

        // ... lanjut kode existing untuk PDF processing

        // Generate QR di PDF (update baris ini)
        $fpdi->write2DBarcode($qrVerifikator1['url'], 'QRCODE,H', 156, 51, 20, 20);

        // ... kode existing lainnya
    }
}
```

### Multiple QR Types

Untuk verifikasi dengan multiple verifikators atau asesis:

```php
// Generate untuk kedua verifikator jika ada
if ($verification->verificator2) {
    $qrVerifikator2 = $this->qrService->generateQR($verification, 'verifikator2');

    DB::connection('reguler')->table('barcodes')->insert([
        'nama' => $verification->verificator2,
        'id_izin' => $verifikator2->Noreg,
        'jabatan' => 'Verifikator 2',
        'url' => $qrVerifikator2['url'],
        'qr_uuid' => $qrVerifikator2['uuid'],
        'qr_type' => 'verifikator2',
        'created_at' => $verification->created_at
    ]);

    // Generate QR di PDF untuk verifikator 2
    $fpdi->write2DBarcode($qrVerifikator2['url'], 'QRCODE,H', 156, 75, 20, 20);
}
```

---

## 🌐 Environment Configuration

### Environment Variables (.env)

```env
# QR Configuration
QR_BASE_URL=http://localhost:8000/qr/
QR_ENVIRONMENT=development

# SIMPLE SETUP: Set expiry in DAYS for ALL QR types (EASIEST)
QR_EXPIRY_DAYS=365                  # 🎯 SET 1 YEAR (365 DAYS) - EDIT THIS ONLY!

# DETAILED SETUP: Individual expiry per QR type (ADVANCED)
# QR_EXPIRY_VERIFIKATOR1_DAYS=7
# QR_EXPIRY_VERIFIKATOR2_DAYS=7
# QR_EXPIRY_KETUA_TUK_DAYS=5
# QR_EXPIRY_DIREKTUR_DAYS=30
# QR_EXPIRY_ASESI_DAYS=3
# QR_EXPIRY_DOCUMENT_HOURS=24

# Advanced: Custom expiry formats
# QR_EXPIRY_CUSTOM_MINUTES=60        # 1 hour
# QR_EXPIRY_CUSTOM_HOURS=48         # 2 days
# QR_EXPIRY_CUSTOM_WEEKS=2          # 2 weeks
# QR_EXPIRY_CUSTOM_MONTHS=3         # 3 months
# QR_EXPIRY_CUSTOM_YEARS=1          # 1 year

# Common values (uncomment to use):
# QR_EXPIRY_DAYS=365               # 1 year
# QR_EXPIRY_DAYS=180               # 6 months
# QR_EXPIRY_DAYS=90                # 3 months
# QR_EXPIRY_DAYS=30                # 1 month
# QR_EXPIRY_DAYS=14                # 2 weeks
# QR_EXPIRY_DAYS=7                 # 1 week
# QR_EXPIRY_DAYS=1                 # 1 day

# Alternative untuk production
# QR_ENVIRONMENT=production
# QR_EXPIRY_DAYS=365               # Keep 1 year for production
```

### Configuration File (`config/qr.php`)

```php
<?php

return [
    'environments' => [
        'development' => [
            'base_url' => env('QR_BASE_URL', 'http://localhost:8000/qr/'),
            'expiry_days' => env('QR_EXPIRY_DAYS', 7),
            'expiry_hours' => env('QR_EXPIRY_HOURS', 24),
        ],
        'staging' => [
            'base_url' => 'https://staging-barcode.lspgatensi.id/',
            'expiry_days' => 14,
            'expiry_hours' => 48,
        ],
        'production' => [
            'base_url' => 'https://barcode.lspgatensi.id/',
            'expiry_days' => 30,
            'expiry_hours' => 72,
        ],
    ],

    'current_environment' => env('QR_ENVIRONMENT', 'development'),

    // SIMPLE SETUP: Universal expiry for ALL QR types (EASIEST)
    // If QR_EXPIRY_DAYS is set, ALL QR types will use this value
    'universal_expiry_days' => env('QR_EXPIRY_DAYS', null), // null = use individual settings

    // EASY CUSTOMIZATION: Define expiry settings per QR type
    // Format: 'type' => ['value' => number, 'unit' => 'unit_name']
    // Available units: 'minutes', 'hours', 'days', 'weeks', 'months', 'years'
    'expiry_settings' => [
        'verifikator1' => ['value' => env('QR_EXPIRY_VERIFIKATOR1_DAYS', 365), 'unit' => 'days'],
        'verifikator2' => ['value' => env('QR_EXPIRY_VERIFIKATOR2_DAYS', 365), 'unit' => 'days'],
        'ketua_tuk' => ['value' => env('QR_EXPIRY_KETUA_TUK_DAYS', 365), 'unit' => 'days'],
        'direktur' => ['value' => env('QR_EXPIRY_DIREKTUR_DAYS', 365), 'unit' => 'days'],
        'asesi' => ['value' => env('QR_EXPIRY_ASESI_DAYS', 365), 'unit' => 'days'],
        'document' => ['value' => env('QR_EXPIRY_DOCUMENT_HOURS', 24), 'unit' => 'hours'],

        // Advanced examples for quick customization:
        'custom_short' => ['value' => env('QR_EXPIRY_CUSTOM_MINUTES', 60), 'unit' => 'minutes'],
        'custom_medium' => ['value' => env('QR_EXPIRY_CUSTOM_HOURS', 48), 'unit' => 'hours'],
        'custom_long' => ['value' => env('QR_EXPIRY_CUSTOM_WEEKS', 2), 'unit' => 'weeks'],
        'custom_extended' => ['value' => env('QR_EXPIRY_CUSTOM_MONTHS', 3), 'unit' => 'months'],
        'custom_permanent' => ['value' => env('QR_EXPIRY_CUSTOM_YEARS', 1), 'unit' => 'years'],
    ],

    // QUICK SETUP: Predefined expiry templates for easy configuration
    // Just change the template name in your .env file: QR_EXPIRY_TEMPLATE=strict
    'templates' => [
        'development' => [
            'verifikator1' => ['value' => 1, 'unit' => 'hours'],
            'verifikator2' => ['value' => 1, 'unit' => 'hours'],
            'ketua_tuk' => ['value' => 30, 'unit' => 'minutes'],
            'direktur' => ['value' => 2, 'unit' => 'hours'],
            'asesi' => ['value' => 15, 'unit' => 'minutes'],
            'document' => ['value' => 10, 'unit' => 'minutes'],
        ],
        'testing' => [
            'verifikator1' => ['value' => 30, 'unit' => 'minutes'],
            'verifikator2' => ['value' => 30, 'unit' => 'minutes'],
            'ketua_tuk' => ['value' => 15, 'unit' => 'minutes'],
            'direktur' => ['value' => 1, 'unit' => 'hours'],
            'asesi' => ['value' => 10, 'unit' => 'minutes'],
            'document' => ['value' => 5, 'unit' => 'minutes'],
        ],
        'standard' => [
            'verifikator1' => ['value' => 7, 'unit' => 'days'],
            'verifikator2' => ['value' => 7, 'unit' => 'days'],
            'ketua_tuk' => ['value' => 5, 'unit' => 'days'],
            'direktur' => ['value' => 30, 'unit' => 'days'],
            'asesi' => ['value' => 3, 'unit' => 'days'],
            'document' => ['value' => 24, 'unit' => 'hours'],
        ],
        'strict' => [
            'verifikator1' => ['value' => 3, 'unit' => 'days'],
            'verifikator2' => ['value' => 3, 'unit' => 'days'],
            'ketua_tuk' => ['value' => 2, 'unit' => 'days'],
            'direktur' => ['value' => 7, 'unit' => 'days'],
            'asesi' => ['value' => 1, 'unit' => 'days'],
            'document' => ['value' => 4, 'unit' => 'hours'],
        ],
        'relaxed' => [
            'verifikator1' => ['value' => 14, 'unit' => 'days'],
            'verifikator2' => ['value' => 14, 'unit' => 'days'],
            'ketua_tuk' => ['value' => 10, 'unit' => 'days'],
            'direktur' => ['value' => 60, 'unit' => 'days'],
            'asesi' => ['value' => 7, 'unit' => 'days'],
            'document' => ['value' => 72, 'unit' => 'hours'],
        ],
    ],

    // Template selector from .env
    'current_template' => env('QR_EXPIRY_TEMPLATE', null), // null = use individual settings
];
```

---

## 🛣️ Routes Configuration

### Web Routes (`routes/web.php`)

```php
// QR Verification Routes
Route::group(['middleware' => ['web']], function () {
    // API endpoints untuk QR verification
    Route::get('/api/qr/{uuid}', [QRVerificationController::class, 'verify']);
    Route::get('/api/qr/{uuid}/status', [QRVerificationController::class, 'checkStatus']);

    // Web view untuk scan result
    Route::get('/qr/{uuid}', [QRVerificationController::class, 'scanResult']);
    Route::get('/qr-scan/{uuid}', [QRVerificationController::class, 'scanResult']);

    // QR management dashboard (admin only)
    Route::group(['middleware' => ['auth', 'admin']], function () {
        Route::get('/admin/qr', [QRAdminController::class, 'index']);
        Route::get('/admin/qr/create', [QRAdminController::class, 'create']);
        Route::post('/admin/qr', [QRAdminController::class, 'store']);
        Route::get('/admin/qr/{uuid}', [QRAdminController::class, 'show']);
    });
});
```

### API Routes (`routes/api.php`)

```php
// API v1 QR endpoints
Route::group(['prefix' => 'v1', 'middleware' => ['api']], function () {
    Route::get('/qr/verify/{uuid}', [QRVerificationController::class, 'verify']);
    Route::get('/qr/status/{uuid}', [QRVerificationController::class, 'checkStatus']);

    // QR Generation (protected)
    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::post('/qr/generate', [QRAdminController::class, 'generate']);
        Route::delete('/qr/{uuid}', [QRAdminController::class, 'destroy']);
    });
});
```

---

## 📱 Frontend Views

### QR Scan Success View (`resources/views/qr/success.blade.php`)

```html
@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">✅ Verifikasi QR Code Berhasil</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-success">
                        <h5>Detail Verifikasi</h5>
                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <strong>Verification ID:</strong><br>
                                <span class="badge badge-primary">{{ $verification->id }}</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Tipe QR:</strong><br>
                                <span class="badge badge-info">{{ $qrCode->type }}</span>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <strong>TUK:</strong><br>
                                {{ $verification->tuk }}
                            </div>
                            <div class="col-md-6">
                                <strong>Verifikator:</strong><br>
                                {{ $verification->verificator }}
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <strong>Jenis TUK:</strong><br>
                                {{ $verification->jenis_tuk }}
                            </div>
                            <div class="col-md-6">
                                <strong>Discan pada:</strong><br>
                                {{ $scannedAt->format('d M Y H:i:s') }}
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <button class="btn btn-secondary" onclick="history.back()">
                            ← Kembali
                        </button>
                        <a href="/" class="btn btn-primary">
                            Ke Halaman Utama
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

### QR Error View (`resources/views/qr/error.blade.php`)

```html
@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0">❌ QR Code Tidak Valid</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger">
                        <p>{{ $error }}</p>
                        <hr>
                        <p class="mb-0">
                            <strong>Mungkin QR Code:</strong>
                        </p>
                        <ul>
                            <li>Sudah kadaluarsa</li>
                            <li>Sudah pernah digunakan</li>
                            <li>Tidak ditemukan dalam sistem</li>
                            <li>URL tidak lengkap</li>
                        </ul>
                    </div>

                    <div class="text-center">
                        <button class="btn btn-secondary" onclick="history.back()">
                            ← Scan Ulang
                        </button>
                        <a href="/" class="btn btn-primary">
                            Ke Halaman Utama
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

---

## 🔧 Database Migration Files

### QR Codes Migration (`database/migrations/create_qr_codes_table.php`)

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('qr_codes', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 36)->unique();
            $table->unsignedBigInteger('verification_id');
            $table->enum('type', ['verifikator1', 'verifikator2', 'asesi', 'document']);
            $table->string('url');
            $table->enum('status', ['active', 'expired', 'used'])->default('active');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            // Foreign key
            $table->foreign('verification_id')
                  ->references('id')
                  ->on('verifications')
                  ->onDelete('cascade');

            // Indexes
            $table->index('uuid');
            $table->index('verification_id');
            $table->index('status');
            $table->index('expires_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('qr_codes');
    }
};
```

### QR Config Migration (`database/migrations/create_qr_config_table.php`)

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('qr_config', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('base_url');
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Seed default configs
        DB::table('qr_config')->insert([
            [
                'name' => 'production',
                'base_url' => 'https://barcode.lspgatensi.id/',
                'description' => 'Production QR verification URL',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'development',
                'base_url' => 'http://localhost:8000/qr/',
                'description' => 'Local development URL',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'staging',
                'base_url' => 'https://staging-barcode.lspgatensi.id/',
                'description' => 'Staging environment URL',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('qr_config');
    }
};
```

### Update Barcodes Migration (`database/migrations/update_barcodes_table.php`)

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('barcodes', function (Blueprint $table) {
            $table->string('qr_uuid', 36)->nullable()->after('url');
            $table->enum('qr_type', ['verifikator1', 'verifikator2', 'document'])->nullable()->after('qr_uuid');
            $table->enum('qr_status', ['active', 'expired', 'used'])->default('active')->after('qr_type');

            // Index
            $table->index('qr_uuid');
        });
    }

    public function down()
    {
        Schema::table('barcodes', function (Blueprint $table) {
            $table->dropIndex(['qr_uuid']);
            $table->dropColumn(['qr_uuid', 'qr_type', 'qr_status']);
        });
    }
};
```

---

## 💡 Usage Examples & Common Scenarios

### **Scenario 1: Development Environment**
```env
# .env for fast development testing
QR_ENVIRONMENT=development
QR_EXPIRY_TEMPLATE=development
QR_BASE_URL=http://localhost:8000/qr/
```

**Result**: All QR codes expire within minutes for quick testing.

### **Scenario 2: Staging with Custom Settings**
```env
# .env for staging with custom expiry
QR_ENVIRONMENT=staging
QR_EXPIRY_VERIFIKATOR1_HOURS=6    # 6 hours for testing
QR_EXPIRY_KETUA_TUK_DAYS=2        # 2 days for ketua TUK
QR_EXPIRY_DIREKTUR_DAYS=7         # 1 week for direktur
QR_EXPIRY_DOCUMENT_HOURS=12       # 12 hours for documents
```

**Result**: Specific expiry times for staging environment.

### **Scenario 3: High Security Production**
```env
# .env for high security production
QR_ENVIRONMENT=production
QR_EXPIRY_TEMPLATE=strict
QR_EXPIRY_VERIFIKATOR1_DAYS=2     # Override template: only 2 days
QR_EXPIRY_KETUA_TUK_HOURS=12      # Override template: only 12 hours
```

**Result**: Strict security with short expiry times.

### **Scenario 4: Remote Area Operations**
```env
# .env for areas with poor internet
QR_ENVIRONMENT=production
QR_EXPIRY_TEMPLATE=relaxed        # Extended validity
QR_EXPIRY_DOCUMENT_DAYS=7         # Documents valid for 1 week
QR_EXPIRY_ASESI_WEEKS=2           # Asesi valid for 2 weeks
```

**Result**: Longer expiry times for offline scenarios.

### **Real-time QR Management**

```php
// Generate QR with default expiry
$qr = $qrService->generateQR($verification, 'verifikator1');

// Check current expiry settings
dump($qrService->getExpirySettings());

// Override expiry for specific QR (admin function)
$qrService->setCustomExpiry($qr['qr_data'], 2, 'hours'); // Custom 2 hours

// Extend expiry for emergency case
$qrService->setCustomExpiry($qr['qr_data'], 3, 'days'); // Extend to 3 days
```

### **Role-Based Default Expiry**

| Role | Default Unit | Development | Production | High Security |
|------|--------------|-------------|-------------|---------------|
| **Verifikator** | days | 1 hour | 7 days | 3 days |
| **Ketua TUK** | days | 30 minutes | 5 days | 2 days |
| **Direktur** | days | 2 hours | 30 days | 7 days |
| **Document** | hours | 10 minutes | 24 hours | 4 hours |
| **Asesi** | days | 15 minutes | 3 days | 1 day |

---

## 🔍 Testing Guide

### 1. Manual Testing

#### Test QR Generation:
```bash
# Test generate QR dari tinker
php artisan tinker

$verification = Verification::first();
$qrService = new QRService();

# Test generate QR untuk verifikator
$qr = $qrService->generateQR($verification, 'verifikator1');
dump($qr);

# Test generate QR untuk ketua TUK
$ketuaTuk = \App\Models\User::where('role', 'ketua_tuk')->first();
$qrKetua = $qrService->generateQRForUser($verification, 'ketua_tuk', $ketuaTuk);
dump($qrKetua);

# Test generate QR untuk direktur
$direktur = \App\Models\User::where('role', 'direktur')->first();
$qrDirektur = $qrService->generateQRForUser($verification, 'direktur', $direktur);
dump($qrDirektur);
```

#### Test Role-Based Frontend:
```bash
# Test ketua TUK dropdown
# Login sebagai admin_lsp
# Visit: /sewaktu
# Check if ketua TUK dropdown populated

# Test direktur auto-detection
# Login sebagai direktur
# Visit: /confirm
# Click "Konfirmasi" pada verification
# Check if direktur name automatically used in QR
```

#### Test QR Verification:
```bash
# Test scan QR via API
curl -X GET "http://localhost:8000/api/qr/{UUID}"

# Test via web
# Buka: http://localhost:8000/qr/{UUID}
```

### 2. Automated Testing

#### QR Service Test (`tests/Unit/QRServiceTest.php`):

```php
<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\QRService;
use App\Models\Verification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QRServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $qrService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->qrService = new QRService();
    }

    public function test_can_generate_qr_code()
    {
        $verification = Verification::factory()->create();

        $result = $this->qrService->generateQR($verification, 'verifikator1');

        $this->assertArrayHasKey('uuid', $result);
        $this->assertArrayHasKey('url', $result);
        $this->assertArrayHasKey('qr_data', $result);

        // Check database
        $this->assertDatabaseHas('qr_codes', [
            'verification_id' => $verification->id,
            'type' => 'verifikator1',
            'status' => 'active'
        ]);
    }

    public function test_qr_expires_correctly()
    {
        $verification = Verification::factory()->create();

        $result = $this->qrService->generateQR($verification, 'verifikator1');
        $qrCode = $result['qr_data'];

        // Should have expiry date
        $this->assertNotNull($qrCode->expires_at);

        // Should not be expired yet
        $this->assertFalse($qrCode->isExpired());

        // Test expiry
        $qrCode->expires_at = now()->subDay();
        $this->assertTrue($qrCode->isExpired());
        $this->assertFalse($qrCode->isValid());
    }
}
```

#### Controller Test (`tests/Feature/QRVerificationTest.php`):

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\QRService;
use App\Models\Verification;
use App\Models\QRCode;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QRVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_qr_verification_success()
    {
        $verification = Verification::factory()->create();
        $qrService = new QRService();
        $qr = $qrService->generateQR($verification, 'verifikator1');

        $response = $this->getJson("/api/qr/{$qr['uuid']}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'qr' => [
                            'type' => 'verifikator1'
                        ]
                    ]
                ]);

        // Check QR is marked as used
        $qrCode = QRCode::where('uuid', $qr['uuid'])->first();
        $this->assertEquals('used', $qrCode->status);
        $this->assertNotNull($qrCode->used_at);
    }

    public function test_invalid_qr_returns_error()
    {
        $response = $this->getJson('/api/qr/invalid-uuid');

        $response->assertStatus(400)
                ->assertJson([
                    'success' => false,
                    'code' => 'INVALID_QR'
                ]);
    }
}
```

---

## 📊 Monitoring & Logging

### Logging Strategy

Tambahkan logging di QR Service:

```php
// Generate QR
Log::info("QR Generated: {$type} for verification {$verification->id}", [
    'uuid' => $uuid,
    'url' => $url,
    'expires_at' => $qrCode->expires_at,
    'environment' => $this->environment
]);

// QR Scanned
Log::info("QR Scanned: {$qrCode->uuid}", [
    'type' => $qrCode->type,
    'verification_id' => $qrCode->verification_id,
    'scanned_at' => now(),
    'user_agent' => request()->userAgent(),
    'ip' => request()->ip()
]);

// QR Expired (via cron job)
Log::info("QR Expired: {$qrCode->uuid}", [
    'type' => $qrCode->type,
    'expired_at' => $qrCode->expires_at
]);
```

### Monitoring Dashboard

Create admin dashboard untuk monitoring QR:

```php
// QrAdminController.php
public function index()
{
    $totalQr = QRCode::count();
    $activeQr = QRCode::valid()->count();
    $expiredQr = QRCode::where('status', 'expired')->count();
    $usedQr = QRCode::where('status', 'used')->count();

    $recentQr = QRCode::with('verification')
                      ->orderBy('created_at', 'desc')
                      ->limit(10)
                      ->get();

    return view('admin.qr.index', compact(
        'totalQr', 'activeQr', 'expiredQr', 'usedQr', 'recentQr'
    ));
}
```

---

## 🚀 Deployment Plan

### Phase 1: Development Setup (Local)

1. **Database Setup:**
   ```bash
   php artisan migrate
   ```

2. **Environment Configuration:**
   ```bash
   # .env
   QR_ENVIRONMENT=development
   QR_BASE_URL=http://localhost:8000/qr/
   ```

3. **Testing:**
   ```bash
   php artisan serve
   # Test: http://localhost:8000/qr/test-uuid
   ```

### Phase 2: Staging Deployment

1. **Update Config:**
   ```bash
   # .env
   QR_ENVIRONMENT=staging
   QR_BASE_URL=https://staging-barcode.lspgatensi.id/
   ```

2. **Run Migration:**
   ```bash
   php artisan migrate --force
   ```

### Phase 3: Production Deployment

1. **Update Production Config:**
   ```bash
   # .env
   QR_ENVIRONMENT=production
   QR_BASE_URL=https://barcode.lspgatensi.id/
   QR_EXPIRY_DAYS=30
   ```

2. **Zero-Downtime Deployment:**
   ```bash
   # Pre-deployment backup
   php artisan backup:run

   # Deploy with zero downtime
   php artisan migrate --force --step
   ```

---

## 🔐 Security Considerations

### 1. QR Code Security

- **UUID4**: Menggunakan UUID v4 untuk unikness
- **Expiry**: Auto-expired untuk prevent abuse
- **Single Use**: QR bisa hanya sekali pakai
- **Rate Limiting**: Limit request untuk prevent brute force

### 2. API Security

```php
// Rate limiting untuk QR endpoints
Route::middleware(['throttle:60,1'])->group(function () {
    Route::get('/api/qr/{uuid}', [QRVerificationController::class, 'verify']);
});

// CORS untuk mobile scanner
Route::middleware(['cors'])->group(function () {
    Route::get('/api/qr/{uuid}', [QRVerificationController::class, 'verify']);
});
```

### 3. Input Validation

```php
// Validate UUID format
public function verify($uuid)
{
    if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $uuid)) {
        return response()->json([
            'error' => 'Invalid UUID format'
        ], 400);
    }
    // ... continue
}
```

---

## 📈 Performance Optimization

### 1. Database Indexes

```sql
-- Add composite indexes for better query performance
ALTER TABLE qr_codes ADD INDEX idx_verification_type (verification_id, type);
ALTER TABLE qr_codes ADD INDEX idx_status_expires (status, expires_at);
ALTER TABLE qr_codes ADD INDEX idx_created_status (created_at, status);
```

### 2. Caching Strategy

```php
// Cache QR verification result for 5 minutes
public function verify($uuid)
{
    $cacheKey = "qr_verification_{$uuid}";

    $result = Cache::remember($cacheKey, 300, function() use ($uuid) {
        return $this->qrService->validateQR($uuid);
    });

    // ... process result
}
```

### 3. Background Jobs

```php
// Generate QR asynchronously
class GenerateQRJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(QRService $qrService)
    {
        // Generate QR in background
    }
}
```

---

## 🔧 Troubleshooting Guide

### Common Issues & Solutions

#### 1. QR Not Generated
**Symptoms:**
- QR code tidak muncul di PDF
- Error di log: "Failed to generate QR"

**Solutions:**
```php
// Check QR service initialization
$qrService = new QRService();

// Check database connection
if (!DB::connection()->getPdo()) {
    Log::error("Database connection failed");
}

// Check UUID generation
$uuid = (string) Uuid::uuid4();
if (strlen($uuid) !== 36) {
    Log::error("UUID generation failed");
}
```

#### 2. QR Verification Fails
**Symptoms:**
- QR scan returns "Invalid QR"
- QR tidak ditemukan di database

**Solutions:**
```php
// Debug QR validation
$qrCode = QRCode::where('uuid', $uuid)->first();
if (!$qrCode) {
    Log::error("QR not found: {$uuid}");
} elseif ($qrCode->isExpired()) {
    Log::error("QR expired: {$uuid}");
}
```

#### 3. Environment URL Issues
**Symptoms:**
- QR mengarah ke URL yang salah
- Localhost vs production confusion

**Solutions:**
```php
// Debug environment detection
$env = config('app.qr_environment', 'development');
$config = DB::table('qr_config')->where('name', $env)->first();

Log::debug("QR Environment: {$env}");
Log::debug("QR Base URL: " . ($config ? $config->base_url : 'Not found'));
```

---

## 📞 Support & Contact

### Technical Support

- **Developer**: [Developer Name]
- **Email**: dev@example.com
- **Slack**: #qr-support

### Documentation Updates

- Repository: [GitHub Repo URL]
- Documentation: [Docs URL]
- API Reference: [API Docs URL]

---

**Document Version:** 1.0
**Last Updated:** 2025-12-04
**Status:** Ready for Implementation