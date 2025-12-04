<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConfirmController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;
use App\Http\Controllers\GenerateController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\QRVerificationController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['guest'])->group(function () {
    Route::get('login', [AuthController::class, 'index'])->name('login');
    Route::get('/login-direktur', [AuthController::class, 'viewDirektur']);
    Route::get('/login-verifikator', [AuthController::class, 'viewVerifikator']);
    Route::get('/login-validator', [AuthController::class, 'viewValidator']);
    Route::get('/login-tuk', [AuthController::class, 'viewTUK']);
    Route::post('login', [AuthController::class, 'login']);
    Route::get("/generate-sewaktu", [GenerateController::class, 'revisiTUK'])->name("generateSewaktu");
});

Route::middleware(['auth'])->group(function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/verification', [FileController::class, 'verification'])->name('verification');
    Route::get('/confirm-tuk', [ConfirmController::class, 'confirmTukView'])->name('confirm_tuk');
    Route::get('/verification/{id}', [FileController::class, 'checkList']);
    Route::post('/verify', [FileController::class, 'verify'])->name('verify');
    Route::get('/validation', [FileController::class, 'validation'])->name('validation');
    Route::get('/approve-validation/{id}', [FileController::class, 'approveValidation'])->name('approve');
    Route::get('/reject-validation/{id}', [FileController::class, 'rejectValidation'])->name('reject');
    Route::get('/sewaktu', [FileController::class, 'sewaktu'])->name('sewaktu');
    Route::get('/mandiri', [FileController::class, 'mandiri']);
    Route::get('/confirm', [ConfirmController::class, 'index'])->name('confirm');
    Route::get('/confirm/{id}', [ConfirmController::class, 'confirm']);
    Route::get('/confirm-tuk/{id}', [ConfirmController::class, 'confirmTuk']);
    Route::get('/sk/{id}', [ConfirmController::class, 'sk']);
    Route::post('/generate-sewaktu', [FileController::class, 'createFileSewaktu'])->name('createFileSewaktu');
    Route::post('/generate-mandiri', [FileController::class, 'createFileMandiri'])->name('createFileMandiri');

    // QR Management Routes for TUK
    Route::get('/tuk-qr-codes', [ConfirmController::class, 'getTukQRCodes'])->name('tuk_qr_codes');
    Route::post('/store-qr-code', [ConfirmController::class, 'storeQRCode'])->name('store_qr_code');
    Route::post('/embed-qr/{uuid}', [ConfirmController::class, 'embedQRCode'])->name('embed_qr_code');
    Route::delete('/delete-qr/{uuid}', [ConfirmController::class, 'deleteQRCode'])->name('delete_qr_code');
});

// QR Verification Routes (Public Access)
Route::group(['middleware' => ['web']], function () {
    // QR verification endpoints
    Route::get('/qr/{uuid}', [QRVerificationController::class, 'scanResult']);
    Route::get('/qr-scan/{uuid}', [QRVerificationController::class, 'scanResult']);
    Route::get('/qr-verify', [QRVerificationController::class, 'index']);
    Route::post('/qr-verify', [QRVerificationController::class, 'processManual']);
});

Route::get('/', [FileController::class, 'index']);
Route::get("/archive", [FileController::class, 'archive'])->name("archive");
Route::get("/files/{no}", [FileController::class, 'viewFiles'])->name("viewFiles");
Route::get("/pendaftaran", [PendaftaranController::class, 'index']);
Route::post("/pendaftaran", [PendaftaranController::class, 'store'])->name("registerTUK");


Route::get('/register-font', function () {
    $fontPath = resource_path('fonts/cambriab.ttf');
    
    if (!file_exists($fontPath)) {
        return "Font file not found: " . $fontPath;
    }

    $fontName = \TCPDF_FONTS::addTTFfont($fontPath, 'TrueTypeUnicode', '', 96);

    return "Font registered as: " . $fontName;
});

Route::get('/test', function () {
    $isLocal = app()->environment('local');

    $basePath = $isLocal 
            ? str_replace('\\', '/', base_path())
            : '/home/lspgatensi/new-balai/veriftuk';

    $template = "{$basePath}/app/Http/Controllers/template-mandiri.pdf";

    return $template;

});