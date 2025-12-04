<?php

namespace App\Http\Controllers;

use App\Models\QRCode;
use App\Services\QRService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\View;

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

            // Don't mark as used - QR codes can be scanned multiple times
            // for document verification purposes

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
        try {
            $qrCode = $this->qrService->validateQR($uuid);

            if (!$qrCode) {
                return view('qr.error', [
                    'error' => 'QR Code tidak valid atau sudah kadaluarsa'
                ]);
            }

            // Don't mark as used - QR codes can be scanned multiple times
            // for document verification purposes

            return view('qr.success', [
                'qrCode' => $qrCode,
                'verification' => $qrCode->verification,
                'scannedAt' => now()
            ]);

        } catch (\Exception $e) {
            return view('qr.error', [
                'error' => 'Terjadi kesalahan saat memproses QR Code'
            ]);
        }
    }

    /**
     * Check QR status without marking as used
     */
    public function checkStatus($uuid): JsonResponse
    {
        $qrCode = QRCode::where('uuid', $uuid)
                       ->with('verification', 'user')
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
                'time_remaining' => $qrCode->getTimeRemaining(),
                'user' => $qrCode->user ? [
                    'name' => $qrCode->user->name,
                    'email' => $qrCode->user->email
                ] : null
            ]
        ]);
    }

    /**
     * QR verification page for manual entry
     */
    public function index()
    {
        return view('qr.verify');
    }

    /**
     * Process manual QR verification
     */
    public function processManual(Request $request): JsonResponse
    {
        $request->validate([
            'uuid' => 'required|string'
        ]);

        return $this->verify($request->uuid);
    }
}