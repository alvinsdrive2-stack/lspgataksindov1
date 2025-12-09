<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use mikehaertl\pdftk\Pdf;
use setasign\Fpdi\Tcpdf\Fpdi;
use Illuminate\Support\Facades\Storage;
use App\Models\Verification;
use ZipArchive;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use App\Services\QRService;
use App\Services\VerificationCheckboxService;

ini_set('max_execution_time', 3800);

class FileController extends Controller
{
    protected $qrService;

    public function __construct(QRService $qrService)
    {
        $this->qrService = $qrService;
    }

    private function publicPath(string $path): string
{
    // Ambil full path dari Laravel
    $fullPath = Storage::disk('public')->path($path);

    // Normalisasi agar backslash Windows jadi slash
    return str_replace(['\\', '//'], '/', $fullPath);
}

/**
 * Safely delete a file with proper error handling for shared hosting
 * @param string $filePath
 * @return bool
 */
private function safeUnlink(string $filePath): bool
{
    // Normalize path untuk cross-platform
    $filePath = str_replace('\\', '/', $filePath);

    // Log untuk debugging
    \Log::info('Attempting to delete file: ' . $filePath);

    // Check if file exists
    if (!file_exists($filePath)) {
        \Log::info('File does not exist, skipping deletion');
        return true; // Return true jika file tidak ada (tidak ada yang dihapus)
    }

    // Try to delete with error suppression
    $result = @unlink($filePath);

    if (!$result) {
        \Log::error('Failed to delete file: ' . $filePath . ' - Error: ' . error_get_last()['message'] ?? 'Unknown error');
        return false;
    }

    \Log::info('Successfully deleted file: ' . $filePath);
    return true;
}

/**
 * Create temporary file with custom directory for shared hosting
 * @param string $prefix
 * @param string $suffix
 * @return string
 */
private function createTempFile(string $prefix = '', string $suffix = ''): string
{
    // Try system temp directory first
    $tempDir = sys_get_temp_dir();

    // Check if writable
    if (!is_writable($tempDir)) {
        // Fallback to storage directory
        $tempDir = storage_path('app/temp');

        // Create if doesn't exist
        if (!file_exists($tempDir)) {
            @mkdir($tempDir, 0755, true);
        }

        // Check if storage temp dir is writable
        if (!is_writable($tempDir)) {
            // Fallback to public temp directory
            $tempDir = public_path('temp');

            if (!file_exists($tempDir)) {
                @mkdir($tempDir, 0755, true);
            }
        }
    }

    // Generate unique filename
    $filename = uniqid($prefix, true) . $suffix;
    $filepath = $tempDir . '/' . $filename;

    // Normalize path
    return str_replace('\\', '/', $filepath);
}
    public function index()
    {
        if (auth()->check()) {
            $user = auth()->user();
    
            if ($user->role === 'direktur') {
                return redirect('/confirm');
            } elseif ($user->role === 'admin_lsp') {
                return redirect('/sewaktu');
            } elseif ($user->role === 'ketua_tuk') {
                return redirect('/confirm-tuk');
            } else {
                return redirect('/verification');
            }
        }

        return view('index');
    }

    public function verification()
    {
        $all_verifications = Verification::where('link', 'LIKE', '%DOKUMEN VERIFIKASI%')->where('verificator', 'LIKE', '%'. auth()->user()->name . '%')->where('approved', true)->where('verified', null)->get();
        return view('verificator.index', compact('all_verifications'));
    }

    
    public function verify(Request $request)
    {
        $verification = Verification::where('id', $request->id)->first();

        $verifikator1 = DB::connection("mygatensi")->table("myasesorbnsp")->select('Noreg')->where('Nama', $verification->verificator)->first();
        $url = $this->publicPath('tuk/' . \Carbon\Carbon::parse($verification->created_at)->format('Y-m-d') . '/' . strtoupper($verification->tuk) . '/' . $verification->link);
        $file = file_get_contents($url);
        $urlPaperless = $this->publicPath('tuk-paperless/' . \Carbon\Carbon::parse($verification->created_at)->format('Y-m-d') . '/' . strtoupper($verification->tuk) . '/' . $verification->link);
        $filePaperless = file_get_contents($urlPaperless);
        $currentDate = new \DateTime($verification->created_at);
        $currentDate = $currentDate->modify('+1 day');
        $skemaObservasi = json_decode($verification->skema_observasi, true);
        $skemaPortofolio = json_decode($verification->skema_portofolio, true);
        $observasiCount = is_array($skemaObservasi) ? count($skemaObservasi) : 0;
        $portofolioCount = is_array($skemaPortofolio) ? count($skemaPortofolio) : 0;
        $indexObservasi = 0;
        $indexPortofolio = 0;
        // Debug: Log checkbox data for troubleshooting
        \Log::info('Verification checkbox data:', VerificationCheckboxService::getCheckboxData($request));

        // Use service for validation
        $isSesuai = VerificationCheckboxService::validateAllRequiredCheckboxes($request);

        if (!$file || !$filePaperless) {
            throw new \Exception("Failed to fetch the PDF from the URL");
        }

        // Use service for tools mapping
        $requestTools = VerificationCheckboxService::getPraktikToolsMapping();

        // Store the file in a temporary location
        $tempFpdiPath = tempnam(sys_get_temp_dir(), 'pdf');
        file_put_contents($tempFpdiPath, $file);

        // Initialize FPDI with TCPDF
        $fpdi = new Fpdi();

        // Set document information (Optional)
        $fpdi->SetCreator('LSP LPK Gataksindo');
        $fpdi->SetAuthor('LSP LPK Gataksindo');
        
        // Load the existing PDF
        $pageCount = $fpdi->setSourceFile($tempFpdiPath);
        if ($verification->jenis_tuk === 'Mandiri') {
            $signaturePage = $pageCount - 5;
        }else {
            $signaturePage = $pageCount - 2;
        }

        $hasObservasi  = !empty(json_decode($verification->skema_observasi, true));
        $hasPortofolio = !empty(json_decode($verification->skema_portofolio, true));

        if ($hasObservasi && $hasPortofolio) {
            $baCount = 2;
            $isBothMethod = true;
        } else {
            $baCount = 0;
            $isBothMethod = false;
        }

        // Iterate through each page of the original PDF
        for ($i = 1; $i <= $pageCount; $i++) {
            $templateId = $fpdi->importPage($i);
            $fpdi->addPage();
            $fpdi->SetAutoPageBreak(false, 0);
            $fpdi->SetFillColor(255, 255, 255);
            $fpdi->Rect(0, 0, 210, 10, 'F');
            $fpdi->useTemplate($templateId);
                        if ($i === 3 && $verification->jenis_tuk === 'Mandiri') {
                // Generate QR otomatis dengan QRService
                $qrVerifikatorMandiri = $this->qrService->generateQR($verification, 'verifikator1');

                // QR data automatically saved to qr_codes table by QRService
                if ($isSesuai === true) {
                    $fpdi->SetFont('cambriab', 'B', 15.5);
                    $fpdi->SetXY(129, 148);
                    $fpdi->Write(0, '----------');
                } else {
                    $fpdi->SetFont('cambriab', 'B', 15.5);
                    $fpdi->SetXY(111, 148);
                    $fpdi->Write(0, '------');
                }
                // Gunakan URL otomatis dari QRService (localhost)
                $fpdi->write2DBarcode($qrVerifikatorMandiri['url'], 'QRCODE,H', 156, 170, 20, 20);
            }
            if ($i === 4 && $verification->filetype === '2' && $verification->jenis_tuk === 'Sewaktu') {
                // Database insert handled by QRService automatically
                if ($isSesuai === true) {
                    $fpdi->SetFont('cambriab', 'B', 15.5);
                    $fpdi->SetXY(174, 24);
                    $fpdi->Write(0, '----------');
                } else {
                    $fpdi->SetFont('cambriab', 'B', 15.5);
                    $fpdi->SetXY(157, 24);
                    $fpdi->Write(0, '------');
                }
                // Generate QR otomatis dengan QRService
                $qrVerifikator1 = $this->qrService->generateQR($verification, 'verifikator1');

                // QR data automatically saved to qr_codes table by QRService

                // Gunakan URL otomatis dari QRService (localhost)
                $fpdi->write2DBarcode($qrVerifikator1['url'], 'QRCODE,H', 156, 51, 20, 20);
            }
            if ($i === 4 + $baCount && $verification->filetype === '2' && $verification->jenis_tuk === 'Sewaktu' && $isBothMethod) {
                // Database insert handled by QRService automatically
                if ($isSesuai === true) {
                    $fpdi->SetFont('cambriab', 'B', 15.5);
                    $fpdi->SetXY(174, 24);
                    $fpdi->Write(0, '----------');
                } else {
                    $fpdi->SetFont('cambriab', 'B', 15.5);
                    $fpdi->SetXY(157, 24);
                    $fpdi->Write(0, '------');
                }
                // Generate QR otomatis dengan QRService
                $qrVerifikator1 = $this->qrService->generateQR($verification, 'verifikator1');

                // QR data automatically saved to qr_codes table by QRService

                // Gunakan URL otomatis dari QRService (localhost)
                $fpdi->write2DBarcode($qrVerifikator1['url'], 'QRCODE,H', 156, 51, 20, 20);
            }
            if ($i === 5 + $baCount && $verification->filetype === '1' && $verification->jenis_tuk === 'Sewaktu' && $isBothMethod) {
                // Generate QR otomatis untuk method kedua
                $qrVerifikator2 = $this->qrService->generateQR($verification, 'verifikator1');

                // QR data automatically saved to qr_codes table by QRService

                if ($isSesuai === true) {
                    $fpdi->SetFont('cambriab', 'B', 15.5);
                    $fpdi->SetXY(174, 24);
                    $fpdi->Write(0, '----------');
                } else {
                    $fpdi->SetFont('cambriab', 'B', 15.5);
                    $fpdi->SetXY(157, 24);
                    $fpdi->Write(0, '------');
                }

                // Gunakan URL otomatis dari QRService (localhost)
                $fpdi->write2DBarcode($qrVerifikator2['url'], 'QRCODE,H', 156, 51, 20, 20);
            }
            // For observasi mandiri
            if ($i >= 5 && $i < 5 + $observasiCount && $verification->jenis_tuk === 'Mandiri') {
                VerificationCheckboxService::drawPersyaratanCheckmarks($fpdi, $request);
                $jabkerBaru = resource_path('json/skema.json');
                $jabkerBaru = json_decode(file_get_contents($jabkerBaru), true)['myskemabnsp'];
                $jabkerBaru = collect($jabkerBaru)->firstWhere('nama', $skemaObservasi[$indexObservasi]);

                $jabker = DB::connection('mygatensi')
                    ->table('myjabatankerja')
                    ->where('jabatan_kerja', $skemaObservasi[$indexObservasi])
                    ->select(['peralatan'])
                    ->first();

                if ($jabkerBaru) {
                    $jabker = (object) [
                        'peralatan'  => $jabkerBaru['spesifikasi'] ?? null,
                    ];
                } elseif (!$jabker) {
                    $jabker = (object) [
                        'peralatan' => null,
                    ];
                }
                if ($jabker->peralatan !== null) {
                    $peralatanArray = array_filter(array_map('trim', explode(',', $jabker->peralatan)));
                    VerificationCheckboxService::drawPraktikToolsCheckmarks($fpdi, $request, $peralatanArray);
                }
                $indexObservasi++;
            }
            // For Portofolio mandiri
            if ($i >= (5 + $observasiCount + ($isBothMethod ? 2 : 0)) && $i < (5 + $observasiCount + $portofolioCount + ($isBothMethod ? 2 : 0)) && $verification->jenis_tuk === 'Mandiri') {
                VerificationCheckboxService::drawPersyaratanCheckmarks($fpdi, $request);
                $jabkerBaru = resource_path('json/skema.json');
                $jabkerBaru = json_decode(file_get_contents($jabkerBaru), true)['myskemabnsp'];
                $jabkerBaru = collect($jabkerBaru)->firstWhere('nama', $skemaPortofolio[$indexPortofolio]);

                $jabker = DB::connection('mygatensi')
                    ->table('myjabatankerja')
                    ->where('jabatan_kerja', $skemaPortofolio[$indexPortofolio])
                    ->select(['peralatan'])
                    ->first();

                if ($jabkerBaru) {
                    $jabker = (object) [
                        'peralatan'  => $jabkerBaru['spesifikasi'] ?? null,
                    ];
                } elseif (!$jabker) {
                    $jabker = (object) [
                        'peralatan' => null,
                    ];
                }
                if ($jabker->peralatan !== null) {
                    $peralatanArray = array_filter(array_map('trim', explode(',', $jabker->peralatan)));
                    VerificationCheckboxService::drawPraktikToolsCheckmarks($fpdi, $request, $peralatanArray);
                }
                $indexPortofolio++;
            }
            // For sewaktu observasi file 2
            if ($i >= 6 + $baCount && $i < 6 + $baCount + $observasiCount && $verification->filetype === '2' && $verification->jenis_tuk === 'Sewaktu') {
                VerificationCheckboxService::drawPersyaratanCheckmarks($fpdi, $request);
                $jabkerBaru = resource_path('json/skema.json');
                $jabkerBaru = json_decode(file_get_contents($jabkerBaru), true)['myskemabnsp'];
                $jabkerBaru = collect($jabkerBaru)->firstWhere('nama', $skemaObservasi[$indexObservasi]);

                $jabker = DB::connection('mygatensi')
                    ->table('myjabatankerja')
                    ->where('jabatan_kerja', $skemaObservasi[$indexObservasi])
                    ->select(['peralatan'])
                    ->first();

                if ($jabkerBaru) {
                    $jabker = (object) [
                        'peralatan'  => $jabkerBaru['spesifikasi'] ?? null,
                    ];
                } elseif (!$jabker) {
                    $jabker = (object) [
                        'peralatan' => null,
                    ];
                }
                if ($jabker->peralatan !== null) {
                    $peralatanArray = array_filter(array_map('trim', explode(',', $jabker->peralatan)));
                    VerificationCheckboxService::drawPraktikToolsCheckmarks($fpdi, $request, $peralatanArray);
                }
                $indexObservasi++;
            }
            // For sewaktu portofolio file 2
            if ($i >= (7 + $baCount + $observasiCount + ($isBothMethod ? 1 : 0)) && $i < (7 + $baCount + $observasiCount + $portofolioCount + ($isBothMethod ? 1 : 0)) && $verification->filetype === '2' && $verification->jenis_tuk === 'Sewaktu') {
                VerificationCheckboxService::drawPersyaratanCheckmarks($fpdi, $request);
                $jabkerBaru = resource_path('json/skema.json');
                $jabkerBaru = json_decode(file_get_contents($jabkerBaru), true)['myskemabnsp'];
                $jabkerBaru = collect($jabkerBaru)->firstWhere('nama', $skemaPortofolio[$indexPortofolio]);

                $jabker = DB::connection('mygatensi')
                    ->table('myjabatankerja')
                    ->where('jabatan_kerja', $skemaPortofolio[$indexPortofolio])
                    ->select(['peralatan'])
                    ->first();

                if ($jabkerBaru) {
                    $jabker = (object) [
                        'peralatan'  => $jabkerBaru['spesifikasi'] ?? null,
                    ];
                } elseif (!$jabker) {
                    $jabker = (object) [
                        'peralatan' => null,
                    ];
                }
                if ($jabker->peralatan !== null) {
                    $peralatanArray = array_filter(array_map('trim', explode(',', $jabker->peralatan)));
                    VerificationCheckboxService::drawPraktikToolsCheckmarks($fpdi, $request, $peralatanArray);
                }
                $indexPortofolio++;
            }
            // For sewaktu observasi file 1
            if ($i >= 7 + $baCount && $i < 7 + $baCount + $observasiCount && $verification->filetype === '1' && $verification->jenis_tuk === 'Sewaktu') {
                VerificationCheckboxService::drawPersyaratanCheckmarks($fpdi, $request);
                $jabkerBaru = resource_path('json/skema.json');
                $jabkerBaru = json_decode(file_get_contents($jabkerBaru), true)['myskemabnsp'];
                $jabkerBaru = collect($jabkerBaru)->firstWhere('nama', $skemaObservasi[$indexObservasi]);

                $jabker = DB::connection('mygatensi')
                    ->table('myjabatankerja')
                    ->where('jabatan_kerja', $skemaObservasi[$indexObservasi])
                    ->select(['peralatan'])
                    ->first();

                if ($jabkerBaru) {
                    $jabker = (object) [
                        'peralatan'  => $jabkerBaru['spesifikasi'] ?? null,
                    ];
                } elseif (!$jabker) {
                    $jabker = (object) [
                        'peralatan' => null,
                    ];
                }
                if ($jabker->peralatan !== null) {
                    $peralatanArray = array_filter(array_map('trim', explode(',', $jabker->peralatan)));
                    VerificationCheckboxService::drawPraktikToolsCheckmarks($fpdi, $request, $peralatanArray);
                }
                $indexObservasi++;
            }
            // For sewaktu portofolio file 1
            if ($i >= (8 + $baCount + $observasiCount + ($isBothMethod ? 1 : 0)) && $i < (8 + $baCount + $observasiCount + $portofolioCount + ($isBothMethod ? 1 : 0)) && $verification->filetype === '1' && $verification->jenis_tuk === 'Sewaktu') {
                VerificationCheckboxService::drawPersyaratanCheckmarks($fpdi, $request);
                $jabkerBaru = resource_path('json/skema.json');
                $jabkerBaru = json_decode(file_get_contents($jabkerBaru), true)['myskemabnsp'];
                $jabkerBaru = collect($jabkerBaru)->firstWhere('nama', $skemaPortofolio[$indexPortofolio]);

                $jabker = DB::connection('mygatensi')
                    ->table('myjabatankerja')
                    ->where('jabatan_kerja', $skemaPortofolio[$indexPortofolio])
                    ->select(['peralatan'])
                    ->first();

                if ($jabkerBaru) {
                    $jabker = (object) [
                        'peralatan'  => $jabkerBaru['spesifikasi'] ?? null,
                    ];
                } elseif (!$jabker) {
                    $jabker = (object) [
                        'peralatan' => null,
                    ];
                }
                if ($jabker->peralatan !== null) {
                    $peralatanArray = array_filter(array_map('trim', explode(',', $jabker->peralatan)));
                    VerificationCheckboxService::drawPraktikToolsCheckmarks($fpdi, $request, $peralatanArray);
                }
                $indexPortofolio++;
            }
            if ($i === ($signaturePage - $portofolioCount - 2) && $isBothMethod) {
                if ($verification->jenis_tuk === 'Mandiri') {
                    // Generate QR otomatis dengan QRService untuk signature page
                    $qrSignature = $this->qrService->generateQR($verification, 'verifikator_signature');
                    $fpdi->write2DBarcode($qrSignature['url'], 'QRCODE,H', 97, 180, 20, 20);
                    if ($isSesuai === true) {
                        $fpdi->SetFont('cambriab', 'B', 15.5);
                        $fpdi->SetXY(78, 76);
                        $fpdi->Write(0, '----------');
                        $fpdi->SetFont('dejavusans', '', 12);
                        $fpdi->SetXY(166, 53);
                        $fpdi->Write(0, "✓");
                        $fpdi->SetXY(166, 58);
                        $fpdi->Write(0, "✓");
                    } else {
                        $fpdi->SetFont('cambriab', 'B', 15.5);
                        $fpdi->SetXY(63, 76);
                        $fpdi->Write(0, '------');
                        $fpdi->SetFont('dejavusans', '', 12);
                        $fpdi->SetXY(177, 53);
                        $fpdi->Write(0, "✓");
                        $fpdi->SetXY(177, 58);
                        $fpdi->Write(0, "✓");
                    }
                } else {
                    // Generate QR otomatis dengan QRService untuk signature page
                    $qrSignature = $this->qrService->generateQR($verification, 'verifikator_signature');
                    $fpdi->write2DBarcode($qrSignature['url'], 'QRCODE,H', 132, 180, 20, 20);
                    if ($isSesuai === true) {
                        $fpdi->SetFont('cambriab', 'B', 15.5);
                        $fpdi->SetXY(78, 76);
                        $fpdi->Write(0, '----------');
                        $fpdi->SetFont('dejavusans', '', 12);
                        $fpdi->SetXY(166, 53);
                        $fpdi->Write(0, "✓");
                        $fpdi->SetXY(166, 58);
                        $fpdi->Write(0, "✓");
                    } else {
                        $fpdi->SetFont('cambriab', 'B', 15.5);
                        $fpdi->SetXY(63, 76);
                        $fpdi->Write(0, '------');
                        $fpdi->SetFont('dejavusans', '', 12);
                        $fpdi->SetXY(177, 53);
                        $fpdi->Write(0, "✓");
                        $fpdi->SetXY(177, 58);
                        $fpdi->Write(0, "✓");
                    }
                }
            }
            if ($i === $signaturePage) {
                if ($verification->jenis_tuk === 'Mandiri') {
                    // Generate QR otomatis dengan QRService untuk signature page
                    $qrSignature = $this->qrService->generateQR($verification, 'verifikator_signature');
                    $fpdi->write2DBarcode($qrSignature['url'], 'QRCODE,H', 97, 180, 20, 20);
                     if ($isSesuai === true) {
                        $fpdi->SetFont('cambriab', 'B', 15.5);
                        $fpdi->SetXY(78, 76);
                        $fpdi->Write(0, '----------');
                        $fpdi->SetFont('dejavusans', '', 12);
                        $fpdi->SetXY(166, 53);
                        $fpdi->Write(0, "✓");
                        $fpdi->SetXY(166, 58);
                        $fpdi->Write(0, "✓");
                    } else {
                        $fpdi->SetFont('cambriab', 'B', 15.5);
                        $fpdi->SetXY(63, 76);
                        $fpdi->Write(0, '------');
                        $fpdi->SetFont('dejavusans', '', 12);
                        $fpdi->SetXY(177, 53);
                        $fpdi->Write(0, "✓");
                        $fpdi->SetXY(177, 58);
                        $fpdi->Write(0, "✓");
                    }
                } else {
                    // Generate QR otomatis dengan QRService untuk signature page
                    $qrSignature = $this->qrService->generateQR($verification, 'verifikator_signature');
                    $fpdi->write2DBarcode($qrSignature['url'], 'QRCODE,H', 132, 180, 20, 20);
                    if ($isSesuai === true) {
                        $fpdi->SetFont('cambriab', 'B', 15.5);
                        $fpdi->SetXY(78, 76);
                        $fpdi->Write(0, '----------');
                        $fpdi->SetFont('dejavusans', '', 12);
                        $fpdi->SetXY(166, 53);
                        $fpdi->Write(0, "✓");
                        $fpdi->SetXY(166, 58);
                        $fpdi->Write(0, "✓");
                    } else {
                        $fpdi->SetFont('cambriab', 'B', 15.5);
                        $fpdi->SetXY(63, 76);
                        $fpdi->Write(0, '------');
                        $fpdi->SetFont('dejavusans', '', 12);
                        $fpdi->SetXY(177, 53);
                        $fpdi->Write(0, "✓");
                        $fpdi->SetXY(177, 58);
                        $fpdi->Write(0, "✓");
                    }
                }
            }
        }

        $finalPdf = $fpdi->Output('', 'S');
        $tempFinalPdfPath = tempnam(sys_get_temp_dir(), 'final_') . '.pdf';
        file_put_contents($tempFinalPdfPath, $finalPdf);

        Storage::disk("public")->delete("tuk/" . \Carbon\Carbon::parse($verification->created_at)->format('Y-m-d') . '/' . strtoupper($verification->tuk) . '/' . $verification->link);
        $result_save = Storage::disk("public")->put("tuk/" . \Carbon\Carbon::parse($verification->created_at)->format('Y-m-d') . '/' . strtoupper($verification->tuk) . '/' . $verification->link, $finalPdf);

        if (!$result_save) {
            throw new \Exception("Failed to save the verification file to storage.");
        }


        $tempPaperlessPath = tempnam(sys_get_temp_dir(), 'pdf');
        file_put_contents($tempPaperlessPath, $filePaperless);

        // Initialize FPDI with TCPDF
        $fpdiPaperless = new Fpdi();

        // Set document information (Optional)
        $fpdiPaperless->SetCreator('LSP LPK Gataksindo');
        $fpdiPaperless->SetAuthor('LSP LPK Gataksindo');
        
        // Load the existing PDF
        $paperlessCount = $fpdiPaperless->setSourceFile($tempPaperlessPath);
        $indexObservasi = 0;
        $indexPortofolio = 0;

        // Iterate through each page of the original PDF
        for ($i = 1; $i <= $paperlessCount; $i++) {
            $templateId = $fpdiPaperless->importPage($i);
            $fpdiPaperless->addPage();
            $fpdiPaperless->SetAutoPageBreak(false, 0);
            $fpdiPaperless->SetFillColor(255, 255, 255);
            $fpdiPaperless->Rect(0, 0, 210, 10, 'F');
            $fpdiPaperless->useTemplate($templateId);
                        if ($i === 3 && $verification->jenis_tuk === 'Mandiri') {
                // Database insert handled by QRService automatically
                if ($isSesuai === true) {
                    $fpdiPaperless->SetFont('cambriab', 'B', 15.5);
                    $fpdiPaperless->SetXY(129, 148);
                    $fpdiPaperless->Write(0, '----------');
                } else {
                    $fpdiPaperless->SetFont('cambriab', 'B', 15.5);
                    $fpdiPaperless->SetXY(111, 148);
                    $fpdiPaperless->Write(0, '------');
                } 
                // Generate QR otomatis dengan QRService untuk paperless signature
                    $qrPaperlessSignature = $this->qrService->generateQR($verification, 'verifikator_paperless');
                    $fpdiPaperless->write2DBarcode($qrPaperlessSignature['url'], 'QRCODE,H', 156, 170, 20, 20);
            }
            if ($i === 4 && $verification->filetype === '2' && $verification->jenis_tuk === 'Sewaktu') {
                // Database insert handled by QRService automatically
                if ($isSesuai === true) {
                    $fpdiPaperless->SetFont('cambriab', 'B', 15.5);
                    $fpdiPaperless->SetXY(174, 24);
                    $fpdiPaperless->Write(0, '----------');
                } else {
                    $fpdiPaperless->SetFont('cambriab', 'B', 15.5);
                    $fpdiPaperless->SetXY(157, 24);
                    $fpdiPaperless->Write(0, '------');
                }
                // Generate QR otomatis dengan QRService untuk paperless
                    $qrPaperless = $this->qrService->generateQR($verification, 'verifikator_paperless');
                    $fpdiPaperless->write2DBarcode($qrPaperless['url'], 'QRCODE,H', 156, 51, 20, 20);
            }
            if ($i === 4 + $baCount && $verification->filetype === '2' && $verification->jenis_tuk === 'Sewaktu' && $isBothMethod) {
                // Database insert handled by QRService automatically
                if ($isSesuai === true) {
                    $fpdiPaperless->SetFont('cambriab', 'B', 15.5);
                    $fpdiPaperless->SetXY(174, 24);
                    $fpdiPaperless->Write(0, '----------');
                } else {
                    $fpdiPaperless->SetFont('cambriab', 'B', 15.5);
                    $fpdiPaperless->SetXY(157, 24);
                    $fpdiPaperless->Write(0, '------');
                }
                // Generate QR otomatis dengan QRService untuk paperless
                    $qrPaperless = $this->qrService->generateQR($verification, 'verifikator_paperless');
                    $fpdiPaperless->write2DBarcode($qrPaperless['url'], 'QRCODE,H', 156, 51, 20, 20);
            }
            if ($i === 5 && $verification->filetype === '1' && $verification->jenis_tuk === 'Sewaktu') {
                // Database insert handled by QRService automatically
                if ($isSesuai === true) {
                    $fpdiPaperless->SetFont('cambriab', 'B', 15.5);
                    $fpdiPaperless->SetXY(174, 24);
                    $fpdiPaperless->Write(0, '----------');
                } else {
                    $fpdiPaperless->SetFont('cambriab', 'B', 15.5);
                    $fpdiPaperless->SetXY(157, 24);
                    $fpdiPaperless->Write(0, '------');
                }
                // Generate QR otomatis dengan QRService untuk paperless
                    $qrPaperless = $this->qrService->generateQR($verification, 'verifikator_paperless');
                    $fpdiPaperless->write2DBarcode($qrPaperless['url'], 'QRCODE,H', 156, 51, 20, 20);
            }
            if ($i === 5 + $baCount && $verification->filetype === '1' && $verification->jenis_tuk === 'Sewaktu' && $isBothMethod) {
                // Database insert handled by QRService automatically
                if ($isSesuai === true) {
                    $fpdiPaperless->SetFont('cambriab', 'B', 15.5);
                    $fpdiPaperless->SetXY(174, 24);
                    $fpdiPaperless->Write(0, '----------');
                } else {
                    $fpdiPaperless->SetFont('cambriab', 'B', 15.5);
                    $fpdiPaperless->SetXY(157, 24);
                    $fpdiPaperless->Write(0, '------');
                }
                // Generate QR otomatis dengan QRService untuk paperless
                    $qrPaperless = $this->qrService->generateQR($verification, 'verifikator_paperless');
                    $fpdiPaperless->write2DBarcode($qrPaperless['url'], 'QRCODE,H', 156, 51, 20, 20);
            }
            // For observasi mandiri
            if ($i >= 5 && $i < 5 + $observasiCount && $verification->jenis_tuk === 'Mandiri') {
                VerificationCheckboxService::drawPersyaratanCheckmarks($fpdiPaperless, $request);
                $jabkerBaru = resource_path('json/skema.json');
                $jabkerBaru = json_decode(file_get_contents($jabkerBaru), true)['myskemabnsp'];
                $jabkerBaru = collect($jabkerBaru)->firstWhere('nama', $skemaObservasi[$indexObservasi]);

                $jabker = DB::connection('mygatensi')
                    ->table('myjabatankerja')
                    ->where('jabatan_kerja', $skemaObservasi[$indexObservasi])
                    ->select(['peralatan'])
                    ->first();

                if ($jabkerBaru) {
                    $jabker = (object) [
                        'peralatan'  => $jabkerBaru['spesifikasi'] ?? null,
                    ];
                } elseif (!$jabker) {
                    $jabker = (object) [
                        'peralatan' => null,
                    ];
                }
                if ($jabker->peralatan !== null) {
                    $initialY = 233;
                    $peralatanArray = array_filter(array_map('trim', explode(',', $jabker->peralatan)));

                    foreach ($peralatanArray as $peralatan) {
                        foreach ($requestTools as $requestName => $peralatanName) {
                            $labels = (array) $peralatanName; // ensure it's always an array

                            foreach ($labels as $label) {
                                if (($request->$requestName ?? null) === "Yes" && strcasecmp($peralatan, $label) === 0) {
                                    $fpdiPaperless->SetFont('dejavusans', '', 12);
                                    $fpdiPaperless->SetXY(148, $initialY);
                                    $fpdiPaperless->Write(0, "✓");
                                }
                            }
                        }
                        $initialY += 7;
                    }
                }
                $indexObservasi++;
            }
            // For Portofolio mandiri
            if ($i >= (5 + $observasiCount + ($isBothMethod ? 2 : 0)) && $i < (5 + $observasiCount + $portofolioCount + ($isBothMethod ? 2 : 0)) && $verification->jenis_tuk === 'Mandiri') {
                VerificationCheckboxService::drawPersyaratanCheckmarks($fpdiPaperless, $request);
                $jabkerBaru = resource_path('json/skema.json');
                $jabkerBaru = json_decode(file_get_contents($jabkerBaru), true)['myskemabnsp'];
                $jabkerBaru = collect($jabkerBaru)->firstWhere('nama', $skemaPortofolio[$indexPortofolio]);

                $jabker = DB::connection('mygatensi')
                    ->table('myjabatankerja')
                    ->where('jabatan_kerja', $skemaPortofolio[$indexPortofolio])
                    ->select(['peralatan'])
                    ->first();

                if ($jabkerBaru) {
                    $jabker = (object) [
                        'peralatan'  => $jabkerBaru['spesifikasi'] ?? null,
                    ];
                } elseif (!$jabker) {
                    $jabker = (object) [
                        'peralatan' => null,
                    ];
                }
                if ($jabker->peralatan !== null) {
                    $initialY = 233;
                    $peralatanArray = array_filter(array_map('trim', explode(',', $jabker->peralatan)));

                    foreach ($peralatanArray as $peralatan) {
                        foreach ($requestTools as $requestName => $peralatanName) {
                            $labels = (array) $peralatanName; // ensure it's always an array

                            foreach ($labels as $label) {
                                if (($request->$requestName ?? null) === "Yes" && strcasecmp($peralatan, $label) === 0) {
                                    $fpdiPaperless->SetFont('dejavusans', '', 12);
                                    $fpdiPaperless->SetXY(148, $initialY);
                                    $fpdiPaperless->Write(0, "✓");
                                }
                            }
                        }
                        $initialY += 7;
                    }
                }
                $indexPortofolio++;
            }
            // For sewaktu observasi file 2
            if ($i >= 6 + $baCount && $i < 6 + $baCount + $observasiCount && $verification->filetype === '2' && $verification->jenis_tuk === 'Sewaktu') {
                VerificationCheckboxService::drawPersyaratanCheckmarks($fpdiPaperless, $request);
                $jabkerBaru = resource_path('json/skema.json');
                $jabkerBaru = json_decode(file_get_contents($jabkerBaru), true)['myskemabnsp'];
                $jabkerBaru = collect($jabkerBaru)->firstWhere('nama', $skemaObservasi[$indexObservasi]);

                $jabker = DB::connection('mygatensi')
                    ->table('myjabatankerja')
                    ->where('jabatan_kerja', $skemaObservasi[$indexObservasi])
                    ->select(['peralatan'])
                    ->first();

                if ($jabkerBaru) {
                    $jabker = (object) [
                        'peralatan'  => $jabkerBaru['spesifikasi'] ?? null,
                    ];
                } elseif (!$jabker) {
                    $jabker = (object) [
                        'peralatan' => null,
                    ];
                }
                if ($jabker->peralatan !== null) {
                    $initialY = 233;
                    $peralatanArray = array_filter(array_map('trim', explode(',', $jabker->peralatan)));

                    foreach ($peralatanArray as $peralatan) {
                        foreach ($requestTools as $requestName => $peralatanName) {
                            $labels = (array) $peralatanName; // ensure it's always an array

                            foreach ($labels as $label) {
                                if (($request->$requestName ?? null) === "Yes" && strcasecmp($peralatan, $label) === 0) {
                                    $fpdiPaperless->SetFont('dejavusans', '', 12);
                                    $fpdiPaperless->SetXY(148, $initialY);
                                    $fpdiPaperless->Write(0, "✓");
                                }
                            }
                        }
                        $initialY += 7;
                    }
                }
                $indexObservasi++;
            }
            // For sewaktu portofolio file 2
            if ($i >= (7 + $baCount + $observasiCount + ($isBothMethod ? 1 : 0)) && $i < (7 + $baCount + $observasiCount + $portofolioCount + ($isBothMethod ? 1 : 0)) && $verification->filetype === '2' && $verification->jenis_tuk === 'Sewaktu') {
                VerificationCheckboxService::drawPersyaratanCheckmarks($fpdiPaperless, $request);
                $jabkerBaru = resource_path('json/skema.json');
                $jabkerBaru = json_decode(file_get_contents($jabkerBaru), true)['myskemabnsp'];
                $jabkerBaru = collect($jabkerBaru)->firstWhere('nama', $skemaPortofolio[$indexPortofolio]);

                $jabker = DB::connection('mygatensi')
                    ->table('myjabatankerja')
                    ->where('jabatan_kerja', $skemaPortofolio[$indexPortofolio])
                    ->select(['peralatan'])
                    ->first();

                if ($jabkerBaru) {
                    $jabker = (object) [
                        'peralatan'  => $jabkerBaru['spesifikasi'] ?? null,
                    ];
                } elseif (!$jabker) {
                    $jabker = (object) [
                        'peralatan' => null,
                    ];
                }
                if ($jabker->peralatan !== null) {
                    $initialY = 233;
                    $peralatanArray = array_filter(array_map('trim', explode(',', $jabker->peralatan)));

                    foreach ($peralatanArray as $peralatan) {
                        foreach ($requestTools as $requestName => $peralatanName) {
                            $labels = (array) $peralatanName; // ensure it's always an array

                            foreach ($labels as $label) {
                                if (($request->$requestName ?? null) === "Yes" && strcasecmp($peralatan, $label) === 0) {
                                    $fpdiPaperless->SetFont('dejavusans', '', 12);
                                    $fpdiPaperless->SetXY(148, $initialY);
                                    $fpdiPaperless->Write(0, "✓");
                                }
                            }
                        }
                        $initialY += 7;
                    }
                }
                $indexPortofolio++;
            }
            // For sewaktu observasi file 1
            if ($i >= 7 + $baCount && $i < 7 + $baCount + $observasiCount && $verification->filetype === '1' && $verification->jenis_tuk === 'Sewaktu') {
                VerificationCheckboxService::drawPersyaratanCheckmarks($fpdiPaperless, $request);
                $jabkerBaru = resource_path('json/skema.json');
                $jabkerBaru = json_decode(file_get_contents($jabkerBaru), true)['myskemabnsp'];
                $jabkerBaru = collect($jabkerBaru)->firstWhere('nama', $skemaObservasi[$indexObservasi]);

                $jabker = DB::connection('mygatensi')
                    ->table('myjabatankerja')
                    ->where('jabatan_kerja', $skemaObservasi[$indexObservasi])
                    ->select(['peralatan'])
                    ->first();

                if ($jabkerBaru) {
                    $jabker = (object) [
                        'peralatan'  => $jabkerBaru['spesifikasi'] ?? null,
                    ];
                } elseif (!$jabker) {
                    $jabker = (object) [
                        'peralatan' => null,
                    ];
                }
                if ($jabker->peralatan !== null) {
                    $initialY = 233;
                    $peralatanArray = array_filter(array_map('trim', explode(',', $jabker->peralatan)));

                    foreach ($peralatanArray as $peralatan) {
                        foreach ($requestTools as $requestName => $peralatanName) {
                            $labels = (array) $peralatanName; // ensure it's always an array

                            foreach ($labels as $label) {
                                if (($request->$requestName ?? null) === "Yes" && strcasecmp($peralatan, $label) === 0) {
                                    $fpdiPaperless->SetFont('dejavusans', '', 12);
                                    $fpdiPaperless->SetXY(148, $initialY);
                                    $fpdiPaperless->Write(0, "✓");
                                }
                            }
                        }
                        $initialY += 7;
                    }
                }
                $indexObservasi++;
            }
            // For sewaktu portofolio file 1
            if ($i >= (8 + $baCount + $observasiCount + ($isBothMethod ? 1 : 0)) && $i < (8 + $baCount + $observasiCount + $portofolioCount + ($isBothMethod ? 1 : 0)) && $verification->filetype === '1' && $verification->jenis_tuk === 'Sewaktu') {
                VerificationCheckboxService::drawPersyaratanCheckmarks($fpdiPaperless, $request);
                $jabkerBaru = resource_path('json/skema.json');
                $jabkerBaru = json_decode(file_get_contents($jabkerBaru), true)['myskemabnsp'];
                $jabkerBaru = collect($jabkerBaru)->firstWhere('nama', $skemaPortofolio[$indexPortofolio]);

                $jabker = DB::connection('mygatensi')
                    ->table('myjabatankerja')
                    ->where('jabatan_kerja', $skemaPortofolio[$indexPortofolio])
                    ->select(['peralatan'])
                    ->first();

                if ($jabkerBaru) {
                    $jabker = (object) [
                        'peralatan'  => $jabkerBaru['spesifikasi'] ?? null,
                    ];
                } elseif (!$jabker) {
                    $jabker = (object) [
                        'peralatan' => null,
                    ];
                }
                if ($jabker->peralatan !== null) {
                    $initialY = 233;
                    $peralatanArray = array_filter(array_map('trim', explode(',', $jabker->peralatan)));

                    foreach ($peralatanArray as $peralatan) {
                        foreach ($requestTools as $requestName => $peralatanName) {
                            $labels = (array) $peralatanName; // ensure it's always an array

                            foreach ($labels as $label) {
                                if (($request->$requestName ?? null) === "Yes" && strcasecmp($peralatan, $label) === 0) {
                                    $fpdiPaperless->SetFont('dejavusans', '', 12);
                                    $fpdiPaperless->SetXY(148, $initialY);
                                    $fpdiPaperless->Write(0, "✓");
                                }
                            }
                        }
                        $initialY += 7;
                    }
                }
                $indexPortofolio++;
            }
            if ($i === ($paperlessCount - $portofolioCount - 2) && $isBothMethod) {
                if ($verification->jenis_tuk === 'Mandiri') {
                    // Generate QR otomatis dengan QRService untuk paperless signature
                    $qrPaperlessSignature = $this->qrService->generateQR($verification, 'verifikator_signature');
                    $fpdiPaperless->write2DBarcode($qrPaperlessSignature['url'], 'QRCODE,H', 97, 180, 20, 20);
                    if ($isSesuai === true) {
                        $fpdiPaperless->SetFont('cambriab', 'B', 15.5);
                        $fpdiPaperless->SetXY(78, 76);
                        $fpdiPaperless->Write(0, '----------');
                        $fpdiPaperless->SetFont('dejavusans', '', 12);
                        $fpdiPaperless->SetXY(166, 53);
                        $fpdiPaperless->Write(0, "✓");
                        $fpdiPaperless->SetXY(166, 58);
                        $fpdiPaperless->Write(0, "✓");
                    } else {
                        $fpdiPaperless->SetFont('cambriab', 'B', 15.5);
                        $fpdiPaperless->SetXY(63, 76);
                        $fpdiPaperless->Write(0, '------');
                        $fpdiPaperless->SetFont('dejavusans', '', 12);
                        $fpdiPaperless->SetXY(177, 53);
                        $fpdiPaperless->Write(0, "✓");
                        $fpdiPaperless->SetXY(177, 58);
                        $fpdiPaperless->Write(0, "✓");
                    }
                } else {
                    // Generate QR otomatis dengan QRService untuk paperless signature
                    $qrPaperlessSignature = $this->qrService->generateQR($verification, 'verifikator_signature');
                    $fpdiPaperless->write2DBarcode($qrPaperlessSignature['url'], 'QRCODE,H', 132, 180, 20, 20);
                    if ($isSesuai === true) {
                        $fpdiPaperless->SetFont('cambriab', 'B', 15.5);
                        $fpdiPaperless->SetXY(78, 76);
                        $fpdiPaperless->Write(0, '----------');
                        $fpdiPaperless->SetFont('dejavusans', '', 12);
                        $fpdiPaperless->SetXY(166, 53);
                        $fpdiPaperless->Write(0, "✓");
                        $fpdiPaperless->SetXY(166, 58);
                        $fpdiPaperless->Write(0, "✓");
                    } else {
                        $fpdiPaperless->SetFont('cambriab', 'B', 15.5);
                        $fpdiPaperless->SetXY(63, 76);
                        $fpdiPaperless->Write(0, '------');
                        $fpdiPaperless->SetFont('dejavusans', '', 12);
                        $fpdiPaperless->SetXY(177, 53);
                        $fpdiPaperless->Write(0, "✓");
                        $fpdiPaperless->SetXY(177, 58);
                        $fpdiPaperless->Write(0, "✓");
                    }
                }
            }
            if ($i === $paperlessCount) {
                if ($verification->jenis_tuk === 'Mandiri') {
                    // Generate QR otomatis dengan QRService untuk paperless signature
                    $qrPaperlessSignature = $this->qrService->generateQR($verification, 'verifikator_signature');
                    $fpdiPaperless->write2DBarcode($qrPaperlessSignature['url'], 'QRCODE,H', 97, 180, 20, 20);
                    if ($isSesuai === true) {
                        $fpdiPaperless->SetFont('cambriab', 'B', 15.5);
                        $fpdiPaperless->SetXY(78, 76);
                        $fpdiPaperless->Write(0, '----------');
                        $fpdiPaperless->SetFont('dejavusans', '', 12);
                        $fpdiPaperless->SetXY(166, 53);
                        $fpdiPaperless->Write(0, "✓");
                        $fpdiPaperless->SetXY(166, 58);
                        $fpdiPaperless->Write(0, "✓");
                    } else {
                        $fpdiPaperless->SetFont('cambriab', 'B', 15.5);
                        $fpdiPaperless->SetXY(63, 76);
                        $fpdiPaperless->Write(0, '------');
                        $fpdiPaperless->SetFont('dejavusans', '', 12);
                        $fpdiPaperless->SetXY(177, 53);
                        $fpdiPaperless->Write(0, "✓");
                        $fpdiPaperless->SetXY(177, 58);
                        $fpdiPaperless->Write(0, "✓");
                    }
                } else {
                    // Generate QR otomatis dengan QRService untuk paperless signature
                    $qrPaperlessSignature = $this->qrService->generateQR($verification, 'verifikator_signature');
                    $fpdiPaperless->write2DBarcode($qrPaperlessSignature['url'], 'QRCODE,H', 132, 180, 20, 20);
                    if ($isSesuai === true) {
                        $fpdiPaperless->SetFont('cambriab', 'B', 15.5);
                        $fpdiPaperless->SetXY(78, 76);
                        $fpdiPaperless->Write(0, '----------');
                        $fpdiPaperless->SetFont('dejavusans', '', 12);
                        $fpdiPaperless->SetXY(166, 53);
                        $fpdiPaperless->Write(0, "✓");
                        $fpdiPaperless->SetXY(166, 58);
                        $fpdiPaperless->Write(0, "✓");
                    } else {
                        $fpdiPaperless->SetFont('cambriab', 'B', 15.5);
                        $fpdiPaperless->SetXY(63, 76);
                        $fpdiPaperless->Write(0, '------');
                        $fpdiPaperless->SetFont('dejavusans', '', 12);
                        $fpdiPaperless->SetXY(177, 53);
                        $fpdiPaperless->Write(0, "✓");
                        $fpdiPaperless->SetXY(177, 58);
                        $fpdiPaperless->Write(0, "✓");
                    }
                }
            }
        }

        $finalPaperless = $fpdiPaperless->Output('', 'S');
        $tempFinalPaperless = tempnam(sys_get_temp_dir(), 'final_') . '.pdf';
        file_put_contents($tempFinalPaperless, $finalPaperless);

        Storage::disk("public")->delete("tuk-paperless/" . \Carbon\Carbon::parse($verification->created_at)->format('Y-m-d') . '/' . strtoupper($verification->tuk) . '/' . $verification->link);
        $result_save = Storage::disk("public")->put("tuk-paperless/" . \Carbon\Carbon::parse($verification->created_at)->format('Y-m-d') . '/' . strtoupper($verification->tuk) . '/' . $verification->link, $finalPaperless);

        if (!$result_save) {
            throw new \Exception("Failed to save the verification file to storage.");
        }

        $verification->update([
            'verified' => true
        ]);

        $link_file = Storage::disk('public')->url('tuk-paperless/' . \Carbon\Carbon::parse($verification->created_at)->format('Y-m-d') . '/' . strtoupper($verification->tuk) . '/' . rawurlencode($verification->link));

        $this->safeUnlink($tempFpdiPath);
        $this->safeUnlink($tempFinalPdfPath);
        return back()->with('success', "Berhasil konfirmasi TUK! (<a target='blank' href='$link_file'>$link_file</a>)");
    }

    public function validation()
    {
        $all_verifications = Verification::where('link', 'LIKE', '%DOKUMEN VERIFIKASI%')->where('validator', 'LIKE', '%'. auth()->user()->name . '%')->where('approved', true)->where('verified', true)->where('jenis_tuk', 'Mandiri')->get();
        return view('validation', compact('all_verifications'));
    }

    public function approveValidation(Request $request)
    {
        $barcodeValidator = (string) Uuid::uuid4();

        $verification = Verification::where('id', $request->id)->first();

        $validator = DB::connection("mygatensi")->table("myasesorbnsp")->select('Noreg')->where('Nama', $verification->validator)->first();
        $url = $this->publicPath('tuk/' . \Carbon\Carbon::parse($verification->created_at)->format('Y-m-d') . '/' . strtoupper($verification->tuk) . '/' . $verification->link);
        $file = file_get_contents($url);
        $urlPaperless = $this->publicPath('tuk-paperless/' . \Carbon\Carbon::parse($verification->created_at)->format('Y-m-d') . '/' . strtoupper($verification->tuk) . '/' . $verification->link);
        $filePaperless = file_get_contents($urlPaperless);
        $currentDate = new \DateTime($verification->created_at);
        $currentDate = $currentDate->modify('+1 day');
        $skemaObservasi = json_decode($verification->skema_observasi, true);
        $skemaPortofolio = json_decode($verification->skema_portofolio, true);
        $observasiCount = is_array($skemaObservasi) ? count($skemaObservasi) : 0;
        $portofolioCount = is_array($skemaPortofolio) ? count($skemaPortofolio) : 0;

        if (!$file || !$filePaperless) {
            throw new \Exception("Failed to fetch the PDF from the URL");
        }

        // Store the file in a temporary location
        $tempFpdiPath = tempnam(sys_get_temp_dir(), 'pdf');
        file_put_contents($tempFpdiPath, $file);

        // Initialize FPDI with TCPDF
        $fpdi = new Fpdi();

        // Set document information (Optional)
        $fpdi->SetCreator('LSP LPK Gataksindo');
        $fpdi->SetAuthor('LSP LPK Gataksindo');
        
        // Load the existing PDF
        $pageCount = $fpdi->setSourceFile($tempFpdiPath);
        if ($verification->jenis_tuk === 'Mandiri') {
            $signaturePage = $pageCount - 5;
        }else {
            $signaturePage = $pageCount - 2;
        }

        $hasObservasi  = !empty(json_decode($verification->skema_observasi, true));
        $hasPortofolio = !empty(json_decode($verification->skema_portofolio, true));

        if ($hasObservasi && $hasPortofolio) {
            $baCount = 2;
            $isBothMethod = true;
        } else {
            $baCount = 0;
            $isBothMethod = false;
        }

        // Iterate through each page of the original PDF
        for ($i = 1; $i <= $pageCount; $i++) {
            $templateId = $fpdi->importPage($i);
            $fpdi->addPage();
            $fpdi->SetAutoPageBreak(false, 0);
            $fpdi->SetFillColor(255, 255, 255);
            $fpdi->Rect(0, 0, 210, 10, 'F');
            $fpdi->useTemplate($templateId);
                        if ($i === 3 && $verification->jenis_tuk === 'Mandiri') {
                // Generate QR otomatis dengan QRService untuk validator
                $qrValidator = $this->qrService->generateQR($verification, 'validator');

                // QR data automatically saved to qr_codes table by QRService
                $fpdi->write2DBarcode($qrValidator['url'], 'QRCODE,H', 156, 207, 20, 20);
                $fpdi->SetFont('cambriab', 'B', 15.5);
                $fpdi->SetXY(129, 148);
                $fpdi->Write(0, '----------');
            }
            if ($i === ($signaturePage - $portofolioCount - 2) && $isBothMethod) {
                // Generate QR otomatis dengan QRService untuk validator signature
                    $qrValidatorSignature = $this->qrService->generateQR($verification, 'validator_signature');
                    $fpdi->write2DBarcode($qrValidatorSignature['url'], 'QRCODE,H', 161, 180, 20, 20);
                $fpdi->SetFont('cambriab', 'B', 15.5);
                $fpdi->SetXY(78, 76);
                $fpdi->Write(0, '----------');
                $fpdi->SetFont('dejavusans', '', 12);
                $fpdi->SetXY(166, 53);
                $fpdi->Write(0, "✓");
                $fpdi->SetXY(166, 58);
                $fpdi->Write(0, "✓");
            }
            if ($i === $signaturePage) {
                // Generate QR otomatis dengan QRService untuk validator signature
                    $qrValidatorSignature = $this->qrService->generateQR($verification, 'validator_signature');
                    $fpdi->write2DBarcode($qrValidatorSignature['url'], 'QRCODE,H', 161, 180, 20, 20);
                $fpdi->SetFont('cambriab', 'B', 15.5);
                $fpdi->SetXY(78, 76);
                $fpdi->Write(0, '----------');
                $fpdi->SetFont('dejavusans', '', 12);
                $fpdi->SetXY(166, 53);
                $fpdi->Write(0, "✓");
                $fpdi->SetXY(166, 58);
                $fpdi->Write(0, "✓");
            }
        }

        $finalPdf = $fpdi->Output('', 'S');
        $tempFinalPdfPath = tempnam(sys_get_temp_dir(), 'final_') . '.pdf';
        file_put_contents($tempFinalPdfPath, $finalPdf);

        Storage::disk("public")->delete("tuk/" . \Carbon\Carbon::parse($verification->created_at)->format('Y-m-d') . '/' . strtoupper($verification->tuk) . '/' . $verification->link);
        $result_save = Storage::disk("public")->put("tuk/" . \Carbon\Carbon::parse($verification->created_at)->format('Y-m-d') . '/' . strtoupper($verification->tuk) . '/' . $verification->link, $finalPdf);

        if (!$result_save) {
            throw new \Exception("Failed to save the verification file to storage.");
        }


        $tempPaperlessPath = tempnam(sys_get_temp_dir(), 'pdf');
        file_put_contents($tempPaperlessPath, $filePaperless);

        // Initialize FPDI with TCPDF
        $fpdiPaperless = new Fpdi();

        // Set document information (Optional)
        $fpdiPaperless->SetCreator('LSP LPK Gataksindo');
        $fpdiPaperless->SetAuthor('LSP LPK Gataksindo');
        
        // Load the existing PDF
        $paperlessCount = $fpdiPaperless->setSourceFile($tempPaperlessPath);

        // Iterate through each page of the original PDF
        for ($i = 1; $i <= $paperlessCount; $i++) {
            $templateId = $fpdiPaperless->importPage($i);
            $fpdiPaperless->addPage();
            $fpdiPaperless->SetAutoPageBreak(false, 0);
            $fpdiPaperless->SetFillColor(255, 255, 255);
            $fpdiPaperless->Rect(0, 0, 210, 10, 'F');
            $fpdiPaperless->useTemplate($templateId);
                        if ($i === 3 && $verification->jenis_tuk === 'Mandiri') {
                // Generate QR otomatis dengan QRService untuk validator
                    $qrValidator = $this->qrService->generateQR($verification, 'validator');
                    $fpdiPaperless->write2DBarcode($qrValidator['url'], 'QRCODE,H', 156, 207, 20, 20);
                $fpdiPaperless->SetFont('cambriab', 'B', 15.5);
                $fpdiPaperless->SetXY(129, 148);
                $fpdiPaperless->Write(0, '----------');
            }
            if ($index === ($paperlessCount - $portofolioCount - 2) && $isBothMethod) {
                // Generate QR otomatis dengan QRService untuk validator signature
                    $qrValidatorSignature = $this->qrService->generateQR($verification, 'validator_signature');
                    $fpdiPaperless->write2DBarcode($qrValidatorSignature['url'], 'QRCODE,H', 161, 180, 20, 20);
                $fpdiPaperless->SetFont('cambriab', 'B', 15.5);
                $fpdiPaperless->SetXY(78, 76);
                $fpdiPaperless->Write(0, '----------');
                $fpdiPaperless->SetFont('dejavusans', '', 12);
                $fpdiPaperless->SetXY(166, 53);
                $fpdiPaperless->Write(0, "✓");
                $fpdiPaperless->SetXY(166, 58);
                $fpdiPaperless->Write(0, "✓");
            }
            if ($i === $paperlessCount) {
                // Generate QR otomatis dengan QRService untuk validator signature
                    $qrValidatorSignature = $this->qrService->generateQR($verification, 'validator_signature');
                    $fpdiPaperless->write2DBarcode($qrValidatorSignature['url'], 'QRCODE,H', 161, 180, 20, 20);
                $fpdiPaperless->SetFont('cambriab', 'B', 15.5);
                $fpdiPaperless->SetXY(78, 76);
                $fpdiPaperless->Write(0, '----------');
                $fpdiPaperless->SetFont('dejavusans', '', 12);
                $fpdiPaperless->SetXY(166, 53);
                $fpdiPaperless->Write(0, "✓");
                $fpdiPaperless->SetXY(166, 58);
                $fpdiPaperless->Write(0, "✓");
            }
        }

        $finalPaperless = $fpdiPaperless->Output('', 'S');
        $tempFinalPaperless = tempnam(sys_get_temp_dir(), 'final_') . '.pdf';
        file_put_contents($tempFinalPaperless, $finalPaperless);

        Storage::disk("public")->delete("tuk-paperless/" . \Carbon\Carbon::parse($verification->created_at)->format('Y-m-d') . '/' . strtoupper($verification->tuk) . '/' . $verification->link);
        $result_save = Storage::disk("public")->put("tuk-paperless/" . \Carbon\Carbon::parse($verification->created_at)->format('Y-m-d') . '/' . strtoupper($verification->tuk) . '/' . $verification->link, $finalPaperless);

        if (!$result_save) {
            throw new \Exception("Failed to save the verification file to storage.");
        }

        $verification->update([
            'validated' => true
        ]);

        $link_file = Storage::disk('public')->url('tuk-paperless/' . \Carbon\Carbon::parse($verification->created_at)->format('Y-m-d') . '/' . strtoupper($verification->tuk) . '/' . rawurlencode($verification->link));

        $this->safeUnlink($tempFpdiPath);
        $this->safeUnlink($tempFinalPdfPath);
        return back()->with('success', "Berhasil validasi TUK! (<a target='blank' href='$link_file'>$link_file</a>)");
    }

    public function rejectValidation(Request $request)
    {
        $barcodeValidator = (string) Uuid::uuid4();

        $verification = Verification::where('id', $request->id)->first();

        $validator = DB::connection("mygatensi")->table("myasesorbnsp")->select('Noreg')->where('Nama', $verification->validator)->first();
        $url = $this->publicPath('tuk/' . \Carbon\Carbon::parse($verification->created_at)->format('Y-m-d') . '/' . strtoupper($verification->tuk) . '/' . $verification->link);
        $file = file_get_contents($url);
        $urlPaperless = $this->publicPath('tuk-paperless/' . \Carbon\Carbon::parse($verification->created_at)->format('Y-m-d') . '/' . strtoupper($verification->tuk) . '/' . $verification->link);
        $filePaperless = file_get_contents($urlPaperless);
        $currentDate = new \DateTime($verification->created_at);
        $currentDate = $currentDate->modify('+1 day');
        $skemaObservasi = json_decode($verification->skema_observasi, true);
        $skemaPortofolio = json_decode($verification->skema_portofolio, true);
        $observasiCount = is_array($skemaObservasi) ? count($skemaObservasi) : 0;
        $portofolioCount = is_array($skemaPortofolio) ? count($skemaPortofolio) : 0;

        if (!$file || !$filePaperless) {
            throw new \Exception("Failed to fetch the PDF from the URL");
        }

        // Store the file in a temporary location
        $tempFpdiPath = tempnam(sys_get_temp_dir(), 'pdf');
        file_put_contents($tempFpdiPath, $file);

        // Initialize FPDI with TCPDF
        $fpdi = new Fpdi();

        // Set document information (Optional)
        $fpdi->SetCreator('LSP LPK Gataksindo');
        $fpdi->SetAuthor('LSP LPK Gataksindo');
        
        // Load the existing PDF
        $pageCount = $fpdi->setSourceFile($tempFpdiPath);
        if ($verification->jenis_tuk === 'Mandiri') {
            $signaturePage = $pageCount - 5;
        }else {
            $signaturePage = $pageCount - 2;
        }

        $hasObservasi  = !empty(json_decode($verification->skema_observasi, true));
        $hasPortofolio = !empty(json_decode($verification->skema_portofolio, true));

        if ($hasObservasi && $hasPortofolio) {
            $baCount = 2;
            $isBothMethod = true;
        } else {
            $baCount = 0;
            $isBothMethod = false;
        }

        // Iterate through each page of the original PDF
        for ($i = 1; $i <= $pageCount; $i++) {
            $templateId = $fpdi->importPage($i);
            $fpdi->addPage();
            $fpdi->SetAutoPageBreak(false, 0);
            $fpdi->SetFillColor(255, 255, 255);
            $fpdi->Rect(0, 0, 210, 10, 'F');
            $fpdi->useTemplate($templateId);
                        if ($i === 3 && $verification->jenis_tuk === 'Mandiri') {
                // Generate QR otomatis dengan QRService untuk validator
                $qrValidator = $this->qrService->generateQR($verification, 'validator');

                // QR data automatically saved to qr_codes table by QRService
                $fpdi->write2DBarcode($qrValidator['url'], 'QRCODE,H', 156, 207, 20, 20);
                $fpdi->SetFont('cambriab', 'B', 15.5);
                $fpdi->SetXY(112, 148);
                $fpdi->Write(0, '------');
            }
            if ($i === ($signaturePage - $portofolioCount - 2) && $isBothMethod) {
                // Generate QR otomatis dengan QRService untuk validator signature
                    $qrValidatorSignature = $this->qrService->generateQR($verification, 'validator_signature');
                    $fpdi->write2DBarcode($qrValidatorSignature['url'], 'QRCODE,H', 161, 180, 20, 20);
                $fpdi->SetFont('cambriab', 'B', 15.5);
                $fpdi->SetXY(63, 76);
                $fpdi->Write(0, '------');
                $fpdi->SetFont('dejavusans', '', 12);
                $fpdi->SetXY(177, 53);
                $fpdi->Write(0, "✓");
                $fpdi->SetXY(177, 58);
                $fpdi->Write(0, "✓");
            }
            if ($i === $signaturePage) {
                // Generate QR otomatis dengan QRService untuk validator signature
                    $qrValidatorSignature = $this->qrService->generateQR($verification, 'validator_signature');
                    $fpdi->write2DBarcode($qrValidatorSignature['url'], 'QRCODE,H', 161, 180, 20, 20);
                $fpdi->SetFont('cambriab', 'B', 15.5);
                $fpdi->SetXY(63, 76);
                $fpdi->Write(0, '------');
                $fpdi->SetFont('dejavusans', '', 12);
                $fpdi->SetXY(177, 53);
                $fpdi->Write(0, "✓");
                $fpdi->SetXY(177, 58);
                $fpdi->Write(0, "✓");
            }
        }

        $finalPdf = $fpdi->Output('', 'S');
        $tempFinalPdfPath = tempnam(sys_get_temp_dir(), 'final_') . '.pdf';
        file_put_contents($tempFinalPdfPath, $finalPdf);

        Storage::disk("public")->delete("tuk/" . \Carbon\Carbon::parse($verification->created_at)->format('Y-m-d') . '/' . strtoupper($verification->tuk) . '/' . $verification->link);
        $result_save = Storage::disk("public")->put("tuk/" . \Carbon\Carbon::parse($verification->created_at)->format('Y-m-d') . '/' . strtoupper($verification->tuk) . '/' . $verification->link, $finalPdf);

        if (!$result_save) {
            throw new \Exception("Failed to save the verification file to storage.");
        }


        $tempPaperlessPath = tempnam(sys_get_temp_dir(), 'pdf');
        file_put_contents($tempPaperlessPath, $filePaperless);

        // Initialize FPDI with TCPDF
        $fpdiPaperless = new Fpdi();

        // Set document information (Optional)
        $fpdiPaperless->SetCreator('LSP LPK Gataksindo');
        $fpdiPaperless->SetAuthor('LSP LPK Gataksindo');
        
        // Load the existing PDF
        $paperlessCount = $fpdiPaperless->setSourceFile($tempPaperlessPath);

        // Iterate through each page of the original PDF
        for ($i = 1; $i <= $paperlessCount; $i++) {
            $templateId = $fpdiPaperless->importPage($i);
            $fpdiPaperless->addPage();
            $fpdiPaperless->SetAutoPageBreak(false, 0);
            $fpdiPaperless->SetFillColor(255, 255, 255);
            $fpdiPaperless->Rect(0, 0, 210, 10, 'F');
            $fpdiPaperless->useTemplate($templateId);
                        if ($i === 3 && $verification->jenis_tuk === 'Mandiri') {
                // Generate QR otomatis dengan QRService untuk validator
                    $qrValidator = $this->qrService->generateQR($verification, 'validator');
                    $fpdiPaperless->write2DBarcode($qrValidator['url'], 'QRCODE,H', 156, 207, 20, 20);
                $fpdiPaperless->SetFont('cambriab', 'B', 15.5);
                $fpdiPaperless->SetXY(112, 148);
                $fpdiPaperless->Write(0, '------');
            }
            if ($index === ($paperlessCount - $portofolioCount - 2) && $isBothMethod) {
                // Generate QR otomatis dengan QRService untuk validator signature
                    $qrValidatorSignature = $this->qrService->generateQR($verification, 'validator_signature');
                    $fpdiPaperless->write2DBarcode($qrValidatorSignature['url'], 'QRCODE,H', 161, 180, 20, 20);
                $fpdiPaperless->SetFont('cambriab', 'B', 15.5);
                $fpdiPaperless->SetXY(63, 76);
                $fpdiPaperless->Write(0, '------');
                $fpdiPaperless->SetFont('dejavusans', '', 12);
                $fpdiPaperless->SetXY(177, 53);
                $fpdiPaperless->Write(0, "✓");
                $fpdiPaperless->SetXY(177, 58);
                $fpdiPaperless->Write(0, "✓");
            }
            if ($i === $paperlessCount) {
                // Generate QR otomatis dengan QRService untuk validator signature
                    $qrValidatorSignature = $this->qrService->generateQR($verification, 'validator_signature');
                    $fpdiPaperless->write2DBarcode($qrValidatorSignature['url'], 'QRCODE,H', 161, 180, 20, 20);
                $fpdiPaperless->SetFont('cambriab', 'B', 15.5);
                $fpdiPaperless->SetXY(63, 76);
                $fpdiPaperless->Write(0, '------');
                $fpdiPaperless->SetFont('dejavusans', '', 12);
                $fpdiPaperless->SetXY(177, 53);
                $fpdiPaperless->Write(0, "✓");
                $fpdiPaperless->SetXY(177, 58);
                $fpdiPaperless->Write(0, "✓");
            }
        }

        $finalPaperless = $fpdiPaperless->Output('', 'S');
        $tempFinalPaperless = tempnam(sys_get_temp_dir(), 'final_') . '.pdf';
        file_put_contents($tempFinalPaperless, $finalPaperless);

        Storage::disk("public")->delete("tuk-paperless/" . \Carbon\Carbon::parse($verification->created_at)->format('Y-m-d') . '/' . strtoupper($verification->tuk) . '/' . $verification->link);
        $result_save = Storage::disk("public")->put("tuk-paperless/" . \Carbon\Carbon::parse($verification->created_at)->format('Y-m-d') . '/' . strtoupper($verification->tuk) . '/' . $verification->link, $finalPaperless);

        if (!$result_save) {
            throw new \Exception("Failed to save the verification file to storage.");
        }

        $verification->update([
            'validated' => true
        ]);

        $link_file = Storage::disk('public')->url('tuk-paperless/' . \Carbon\Carbon::parse($verification->created_at)->format('Y-m-d') . '/' . strtoupper($verification->tuk) . '/' . rawurlencode($verification->link));

        $this->safeUnlink($tempFpdiPath);
        $this->safeUnlink($tempFinalPdfPath);
        return back()->with('success', "Berhasil validasi TUK! (<a target='blank' href='$link_file'>$link_file</a>)");
    }

    public function sewaktu()
    {
        $allAsesor = DB::connection("mygatensi")->table("myasesorbnsp")->get();
        $allJabker = DB::connection("mygatensi")->table("myjabatankerja")->select(['id_jabatan_kerja', 'jabatan_kerja'])->get();

        // Ambil data ketua TUK dari users dengan role 'ketua_tuk'
        $ketuaTukList = DB::table('users')
                            ->where('role', 'ketua_tuk')
                            ->whereNotNull('name')
                            ->orderBy('name')
                            ->get();

        return view('file.sewaktu', compact('allJabker', 'allAsesor', 'ketuaTukList'));
    }

    public function mandiri()
    {
        $allSubklas = DB::connection("mygatensi")->table("mysubklasifikasi")->where('id_klasifikasi', 'SI')->select(['kode_subklasifikasi', 'deskripsi_subklasifikasi'])->get();

        return view('file.mandiri', compact('allSubklas'));
    }

    public function checkList($id)
    {
        $allJabker = Verification::select(['skema_portofolio', 'skema_observasi'])
            ->where('id', $id)
            ->first();

        $skemaPortofolio = json_decode($allJabker->skema_portofolio, true) ?? [];
        $skemaObservasi  = json_decode($allJabker->skema_observasi, true) ?? [];
        $skemaList = array_merge($skemaPortofolio, $skemaObservasi);

        // --- Ambil dari JSON
        $jabkerBaru = resource_path('json/skema.json');
        $jabkerBaru = json_decode(file_get_contents($jabkerBaru), true)['myskemabnsp'];

        // Cari semua peralatan di JSON yang nama skemanya ada di $skemaList
        $jsonPeralatan = collect($jabkerBaru)
            ->whereIn('nama', $skemaList)
            ->pluck('peralatan')
            ->filter()
            ->values()
            ->toArray();

        // --- Ambil dari DB
        $results = DB::connection('mygatensi')
            ->table('myjabatankerja')
            ->select('jabatan_kerja', 'peralatan')
            ->whereIn('jabatan_kerja', $skemaList)
            ->orderBy('InputDate', 'desc')
            ->get()
            ->unique('jabatan_kerja');

        $dbPeralatan = $results->pluck('peralatan')->filter()->values()->toArray();

        // Gabungkan hasil dari DB + JSON
        $allPeralatan = array_merge($dbPeralatan, $jsonPeralatan);

        return view('verificator.verification', compact('allPeralatan', 'id'));
    }

    public function archive()
    {
        // Ambil data file verifikasi - get latest record for each TUK
        $all_files = Verification::select(['no_surat', 'tuk', 'created_at'])
            ->whereIn('id', function($query) {
                $query->selectRaw('MAX(id)')
                      ->from('verifications')
                      ->groupBy('tuk');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $all_files_view = [];
        foreach ($all_files as $files) {
            array_push($all_files_view, [
                "no_surat" => $files->no_surat,
                "tuk" => $files->tuk,
                "created_at" => \Carbon\Carbon::parse($files->created_at)->setTimezone('Asia/Jakarta')->isoFormat('dddd, D MMMM YYYY'),
            ]);
        }

        // For filtered invoice
        $all_files_filtered_tuk = Verification::select(['tuk'])->get()->unique("tuk");
        $tuk_filtered = [];
        foreach ($all_files_filtered_tuk as $tuk) {
            array_push($tuk_filtered, [
                "nama_tuk" => $tuk->tuk,
            ]);
        }

        return view("archive", compact('all_files_view', 'tuk_filtered'));
    }

    public function viewFiles($no)
    {
        // Ambil data file verifikasi
        $all_files = Verification::where('no_surat', $no)->orderBy('id', 'desc')->get();

        $all_files_view = [];
        foreach ($all_files as $file) {
            array_push($all_files_view, [
                "no_surat" => $file->no_surat,
                "tuk" => $file->tuk,
                "link" => $file->link,
                "created_at" => \Carbon\Carbon::parse($file->created_at)->setTimezone('Asia/Jakarta')->isoFormat('dddd, D MMMM YYYY'),
            ]);
        }

        // For filtered file
        $all_files_filtered_tuk = Verification::select(['tuk'])->get();
        $tuk_filtered = [];
        foreach ($all_files_filtered_tuk as $tuk) {
            array_push($tuk_filtered, [
                "nama_tuk" => $tuk->tuk,
            ]);
        }

        return view("file.files", compact('all_files_view', 'tuk_filtered'));
    }
    
    public function createFileSewaktu(Request $request)
    {
        $romanMonths = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        $daysIndonesian = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];
        $monthsIndonesian = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $skemaArray = $request->input('skema');
        $jenjangArray = $request->input('jenjang');

        $validator = DB::connection("mygatensi")->table("myasesorbnsp")->select('Nama')->where('Noreg', $request->met1)->first();
        $verifikator1 = DB::connection("mygatensi")->table("myasesorbnsp")->select('Nama')->where('Noreg', $request->asesor)->first();
        $verifikator2 = null;
        if (isset($request->met2)) {
            $verifikator2 = DB::connection("mygatensi")->table("myasesorbnsp")->select('Nama')->where('Noreg', $request->met2)->first();
        }

        // Tanggal Asesmen
        $tanggal1 = new \DateTime($request->tanggal_asesmen);
        $dayOfWeekEnglish1 = $tanggal1->format('l');
        $dayOfWeekIndonesian1 = $daysIndonesian[$dayOfWeekEnglish1];
        $day1 = $tanggal1->format('d');
        $month1 = $monthsIndonesian[$tanggal1->format('n')];
        $year1 = $tanggal1->format('Y');
        $formattedTanggal1 = "$dayOfWeekIndonesian1 / $day1 $month1 $year1";

        // Tanggal Verifikasi
        $currentDate = new \DateTime($request->tanggal_verifikasi);
        $dayOfWeekEnglish3 = $currentDate->format('l');
        $dayOfWeekIndonesian3 = $daysIndonesian[$dayOfWeekEnglish3];
        $day3 = $currentDate->format('d');
        $month3 = $monthsIndonesian[$currentDate->format('n')];
        $year3 = $currentDate->format('Y');
        $formattedTanggal3 = "$dayOfWeekIndonesian3 / $day3 $month3 $year3";
        $formattedTanggal5 = "$dayOfWeekIndonesian3 , $day3 $month3 $year3";

        // Tanggal TTD
        $yesterday = (clone $currentDate)->modify('-1 day');
        $day2 = $yesterday->format('d');
        $month2 = $monthsIndonesian[$yesterday->format('n')];
        $year2 = $yesterday->format('Y');
        $formattedTanggal2 = "Jakarta, $day2 $month2 $year2";

        // Tanggal Verifikasi
        $day4 = $currentDate->format('d');
        $month4 = $monthsIndonesian[$currentDate->format('n')];
        $year4 = $currentDate->format('Y');
        $formattedTanggal4 = "Jakarta, $day4 $month4 $year4";

        $monthRoman = $romanMonths[date('n')];

        $maxAlamat1Length = 62;

        $alamatWords = explode(' ', $request->alamat);
        $alamat1 = '';
        $alamat2 = '';
        $alamat1Length = 0;
        foreach ($alamatWords as $word) {
            if (($alamat1Length + strlen($word) + 1) <= $maxAlamat1Length) {
                $alamat1 .= ($alamat1 === '' ? '' : ' ') . $word;
                $alamat1Length += strlen($word) + 1; // +1 for the space
            } else {
                $alamat2 .= ($alamat2 === '' ? '' : ' ') . $word;
            }
        }

        $jumlahSkema = count($request->skema ?? []);

        $isLocal = app()->environment('local');

        $basePath = $isLocal 

            ? str_replace('\\', '/', base_path())
            : '/home/lspgatensi/new-balai/veriftuk';
        
        // ubah pdftk pathnya sesuai dengan lokasi download jika pakai windows dan local
        $pdftkPath = 'C:/Program Files (x86)/PDFtk Server/bin/pdftk.exe';

        if($request->jenisTUK === 'Mandiri') {
            $template = "{$basePath}/app/Http/Controllers/template-mandiri.pdf";
            
            // $template = '/home/lspgatensi/new-balai/veriftuk/app/Http/Controllers/template-mandiri.pdf';
        } else {
            $template = $jumlahSkema < 8 ? "{$basePath}/app/Http/Controllers/template2-sewaktu.pdf" : "{$basePath}/app/Http/Controllers/template1-sewaktu.pdf";

            // $template = $request->skema7 === null ? '/home/lspgatensi/new-balai/veriftuk/app/Http/Controllers/template2-sewaktu.pdf' : '/home/lspgatensi/new-balai/veriftuk/app/Http/Controllers/template1-sewaktu.pdf';
        }
        $outputPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'DOKUMEN VERIFIKASI' . '.pdf';

        $pdf = $isLocal
        ? new Pdf($template, [
            'command' => $pdftkPath,
            'useExec' => true,
        ])
        : new Pdf($template);

        $formFields = [
            'no1' => $request->nomor . '/LSP LPK GTK B.006-B/' . $monthRoman . '/' . date('Y'),
            'no4' => $request->nomor . '/LSP LPK GTK C.003-F/' . $monthRoman . '/' . date('Y'),
            'tanggal1' => $formattedTanggal1,
            'tanggal2' => $formattedTanggal2,
            'tanggal3' => $formattedTanggal3,
            'tanggal4' => $formattedTanggal4,
            'tanggal5' => $formattedTanggal5,
            'tuk' => $request->tuk,
            'alamat1' => $alamat1,
            'alamat2' => $alamat2,
            'metode' => $request->metode,
            'peserta' => $request->peserta . ' peserta',
            'metode_verif' => $request->metodeVerif,
            'verifikator1' => "$verifikator1->Nama ($request->asesor)",
            'verifikator2' => $verifikator2 !== null ? "$verifikator2->Nama ($request->met2)" : null,
            'verifikatorlist1' => $verifikator1 !== null ? "•  $verifikator1->Nama ($request->asesor)" : null,
            'verifikatorlist2' => $verifikator2 !== null ? "•  $verifikator2->Nama ($request->met2)"  : null,
            'verifikatorlist' => $verifikator1 !== null ? "•  $verifikator1->Nama ($request->asesor) - Verifikator" : null,
            'validatorlist' => $verifikator2 !== null ? "•  $validator->Nama ($request->met1) - Validator"  : null,
            'validator' => $validator !== null ? "$validator->Nama ($request->met1)" : null,
            'memutuskan' => "$request->tuk sebagai TUK Sewaktu Terverifikasi.",
            'penanggungjawab' => "$request->ketua_tuk sebagai Penanggungjawab $request->tuk.",
            'admin' => "$request->admin sebagai admin $request->tuk.",
            'tanggal_uji' => "$day1 $month1 $year1",
            'ketua' => $request->ketua_tuk,
        ];

        foreach ($skemaArray as $index => $skema) {
            $i = $index + 1;
            $formFields["skema$i"] = $skema;
            $formFields["noskema$i"] = $skema !== null ? "$i." : null;
            $formFields["skemalist$i"] = $skema !== null ? '•  ' . $skema : null;
        }

        $result = $pdf->fillForm($formFields)->flatten()->saveAs($outputPath);

        if (!$result) {
            dd('PDFtk Error: ' . $pdf->getError());
        }

        $fpdiContents = file_get_contents($outputPath);
        
        if (!$fpdiContents) {
            throw new \Exception("Failed to fetch the PDF from the URL");
        }

        // Store the file in a temporary location
        $tempFpdiPath = tempnam(sys_get_temp_dir(), 'pdf');
        file_put_contents($tempFpdiPath, $fpdiContents);

        // Initialize FPDI with TCPDF
        $fpdi = new Fpdi();

        // Set document information (Optional)
        $fpdi->SetCreator('LSP LPK Gataksindo');
        $fpdi->SetAuthor('LSP LPK Gataksindo');
        
        // Load the existing PDF
        $pageCount = $fpdi->setSourceFile($tempFpdiPath);

        // Iterate through each page of the original PDF
        for ($i = 1; $i <= $pageCount; $i++) {
            $templateId = $fpdi->importPage($i);
            $fpdi->addPage();
            $fpdi->SetAutoPageBreak(false, 0);
            $fpdi->SetFillColor(255, 255, 255);
            $fpdi->Rect(0, 0, 210, 10, 'F');
            $fpdi->useTemplate($templateId);
            
            if ($i === 2) {
                $fpdi->SetFont('cambriab', 'B', 15.5);
                $fpdi->SetXY(76, 81);
                $fpdi->Write(0, $request->nomor . '/LSP LPK GTK B.007-B/' . $monthRoman . '/' . date('Y'));
            }
            if (($i === 3 && $jumlahSkema < 8) || ($i === 3 && $request->jenisTUK === 'Mandiri')) {
                $fpdi->SetFont('cambriab', 'B', 15.5);
                $fpdi->SetXY(76, 84.4);
                $fpdi->Write(0, $request->nomor . '/LSP LPK GTK B.008-B/' . $monthRoman . '/' . date('Y'));
            }
            if ($i === 4 && $jumlahSkema >= 8) {
                $fpdi->SetFont('cambriab', 'B', 15.5);
                $fpdi->SetXY(76, 84.4);
                $fpdi->Write(0, $request->nomor . '/LSP LPK GTK B.008-B/' . $monthRoman . '/' . date('Y'));
            }
        }

        $finalPdf = $fpdi->Output('', 'S');

        $tempFinalPdfPath = tempnam(sys_get_temp_dir(), 'final_') . '.pdf';
        file_put_contents($tempFinalPdfPath, $finalPdf);

        $countMetode = array_count_values($request->metode ?? []);
        $skemaObservasi = [];
        $skemaPortofolio = [];

        $outputBAPortofolioPath = null;
        $outputPortofolioPath = null;
        $outputTtdPortofolioPath = null;
        $mergedPortofolioPath = null;

        $outputBAObservasiPath = null;
        $outputObservasiPath = null;
        $outputTtdObservasiPath = null;
        $mergedObservasiPath = null;
        
        // Halaman Form Checklist Observasi
        if (($countMetode['Observasi'] ?? 0) > 0 || ($countMetode['Observasi & Portofolio'] ?? 0) > 0) {
            // $templateBAObservasi = '/home/lspgatensi/new-balai/veriftuk/app/Http/Controllers/templatePdf/BAObservasi.pdf';
            // $templateObservasi = '/home/lspgatensi/new-balai/veriftuk/app/Http/Controllers/templatePdf/checklistObservasi.pdf';
            // $templateTtdObservasi = $request->jenisTUK === 'Mandiri' ? '/home/lspgatensi/new-balai/veriftuk/app/Http/Controllers/templatePdf/ttdObservasiMandiri.pdf' : '/home/lspgatensi/new-balai/veriftuk/app/Http/Controllers/templatePdf/ttdObservasiSewaktu.pdf';
            $templateBAObservasi = "{$basePath}/app/Http/Controllers/templatePdf/BAObservasi.pdf";
            $templateObservasi = "{$basePath}/app/Http/Controllers/templatePdf/checklistObservasi.pdf";
            $templateTtdObservasi = $request->jenisTUK === 'Mandiri' ? "{$basePath}/app/Http/Controllers/templatePdf/ttdObservasiMandiri.pdf" : "{$basePath}/app/Http/Controllers/templatePdf/ttdObservasiSewaktu.pdf";
            //

            $outputBAObservasiPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'BA Observasi.pdf';
            $outputObservasiPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'Checklist Observasi.pdf';
            $outputTtdObservasiPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'TTD Observasi.pdf';
            $pdfBAObservasi = $isLocal
                ? new Pdf($templateBAObservasi, [
                    'command' => $pdftkPath,
                    'useExec' => true,
                ])
                : new Pdf($templateBAObservasi);
            $pdfObservasi = $isLocal
                ? new Pdf($templateObservasi, [
                    'command' => $pdftkPath,
                    'useExec' => true,
                ])
                : new Pdf($templateBAObservasi);
            $pdfTtdObservasi = $isLocal
                ? new Pdf($templateTtdObservasi, [
                    'command' => $pdftkPath,
                    'useExec' => true,
                ])
                : new Pdf($templateTtdObservasi);
            $formFieldObservasi = [
                'tuk' => $request->tuk,
                'tanggal3' => $formattedTanggal3,
                'tanggal4' => $formattedTanggal4,
                'alamat1' => $alamat1,
                'alamat2' => $alamat2,
                'metode_verif' => $request->metodeVerif,
                'tanggal1' => $formattedTanggal1,
                'verifikator1' => "$verifikator1->Nama ($request->asesor)",
                'ketua' => $request->ketua_tuk,
                'asesor' => $verifikator1->Nama,
                'validator' => $validator !== null ? $validator->Nama : null,
            ];
            if($request->jenisTUK === 'Mandiri') {
                $formFieldObservasi["skemaobservasi1"] = 'Daftar Skema Terlampir';
            }
            $indexObservasi = 1;
            $tempFinalObservasiPaths = [];
            foreach ($request->metode as $index => $metode) {
                if ($metode === 'Observasi' || $metode === 'Observasi & Portofolio') {
                    $skemaValue = $request->skema[$index] ?? null;
                    $skemaObservasi[] = $skemaValue;
                    if($request->jenisTUK === 'Sewaktu') {
                        $formFieldObservasi["noskema$indexObservasi"] = $indexObservasi . '.';
                        $formFieldObservasi["skemaobservasi_BA$indexObservasi"] = 'TUK  ' . $skemaValue;
                        $formFieldObservasi["skemaobservasi$indexObservasi"] = '•  ' . $skemaValue;
                        $indexObservasi++;
                    }

                    $jabkerBaru = resource_path('json/skema.json');
                    $jabkerBaru = json_decode(file_get_contents($jabkerBaru), true)['myskemabnsp'];
                    $jabkerBaru = collect($jabkerBaru)->firstWhere('nama', $skemaValue);

                    $jabker = DB::connection('mygatensi')
                        ->table('myjabatankerja')
                        ->where('jabatan_kerja', $skemaValue)
                        ->select(['jenjang_id', 'peralatan'])
                        ->first();

                    if ($jabkerBaru) {
                        $jabker = (object) [
                            'jenjang_id' => $jenjangArray[$index] ?? null,
                            'peralatan'  => $jabkerBaru['peralatan'] ?? null,
                            'spesifikasi'  => $jabkerBaru['spesifikasi'] ?? null,
                        ];
                    } elseif (!$jabker) {
                        $jabker = (object) [
                            'jenjang_id' => $jenjangArray[$index] ?? null,
                            'peralatan' => null,
                        ];
                    }
                    if (!empty($skemaValue)) {
                        if ($jabker->jenjang_id > 3) {
                            // $templateskema = '/home/lspgatensi/new-balai/veriftuk/app/Http/Controllers/templatePdf/checklist-j4-9.pdf';
                            $templateskema = "{$basePath}/app/Http/Controllers/templatePdf/checklist-j4-9.pdf";
                        } elseif ($jabker->peralatan === null) {
                            // $templateskema = '/home/lspgatensi/new-balai/veriftuk/app/Http/Controllers/templatePdf/checklist-j4-9.pdf';
                            $templateskema = "{$basePath}/app/Http/Controllers/templatePdf/checklist-j4-9.pdf";
                        } else {
                            // $templateskema = '/home/lspgatensi/new-balai/veriftuk/app/Http/Controllers/templatePdf/checklist-j1-3.pdf';
                            $templateskema = "{$basePath}/app/Http/Controllers/templatePdf/checklist-j1-3.pdf";
                            $peralatanArray = explode(',', $jabker->peralatan);

                            $requestTools = [
                                'theodolite' => 'Theodolite',
                                'meteran' => 'Meteran',
                                'penggaris' => 'Penggaris',
                                'waterpass' => 'Waterpass',
                                'autocad' => 'Autocad',
                                'perancah' => 'Perancah',
                                'bouwplank' => 'Bouwplank',
                                'patok' => 'Patok / Bench Mark',
                                'jidar' => 'Jidar',
                                'bandul' => 'Lot / Bandul',
                                'palu_karet' => 'Palu Karet',
                                'palu_besi' => 'Palu',
                                'tang_jepit' => 'Tang Jepit',
                                'tang_potong' => 'Tang Potong',
                                'gergaji_kayu' => 'Gergaji Kayu',
                                'gergaji_besi' => 'Gergaji Besi',
                                'gerinda' => 'Mesin Gerinda',
                                'pembengkok' => 'Alat Pembengkok Besi',
                                'pahat' => 'Pahat Kayu',
                                'obeng' => 'Obeng',
                                'cangkul' => 'Cangkul',
                                'sendok_semen' => 'Sendok Semen',
                                'ember' => 'Ember',
                                'pengerik' => 'Alat Pengerik / Kape',
                                'roll_cat' => 'Kuas Roll Cat',
                                'kuas_cat' => 'Kuas',
                                'roll_cat' => 'Kuas & roller',
                                'kuas_cat' => 'Kuas & roller',
                                'nampan' => 'Nampan Cat',
                                'benang' => 'Benang',
                                'paku' => 'Paku',
                                'ampelas' => 'Ampelas',
                                'triplek' => 'Triplek',
                                'lakban' => 'Masking Tape / Lakban',
                                'dempul' => 'Dempul',
                                'papan_applicator' => 'Papan Applicator',
                                'mesin_bor' => 'Mesin Bor',
                                'mesin_serut' => 'Mesin Serut',
                                'mesin_gergaji' => 'Mesin Gergaji',
                                'penggaris_siku' => 'Penggaris Siku',
                                'cat' => 'Cat',
                                'triplek' => 'Triplek'
                            ];
                        }

                        $maxSkema1Length = 46;
                        $skemaWords = explode(' ', $skemaValue);
                        $skema1 = '';
                        $skema2 = '';
                        $skema1Length = 0;
                        foreach ($skemaWords as $word) {
                            if (($skema1Length + strlen($word) + 1) <= $maxSkema1Length) {
                                $skema1 .= ($skema1 === '' ? '' : ' ') . $word;
                                $skema1Length += strlen($word) + 1;
                            } else {
                                $skema2 .= ($skema2 === '' ? '' : ' ') . $word;
                            }
                        }

                        $skemaOutputPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'SKEMA_' . $index . '.pdf';
                        $pdfSkema = $isLocal
                            ? new Pdf($templateskema, [
                                'command' => $pdftkPath,
                                'useExec' => true,
                            ])
                            : new Pdf($templateskema);
                        $formSkema = [
                            'jabker1' => ucwords($skema1),
                            'jabker2' => ucwords($skema2),
                            'jenjang' => $jabker->jenjang_id,
                            'laptop' => $request->peserta,
                            'metode' => 'Observasi',
                            'kursipemateri' => 2,
                            'kursikerja' => $request->peserta,
                            'komunikasi' => $request->peserta,
                            'pulpen' => $request->peserta,
                            'pensil' => $request->peserta,
                            'tipex' => $request->peserta,
                            'penghapus' => $request->peserta,
                            'spidol' => $request->peserta,
                            'penggaris' => $request->peserta,
                            'hvs' => $request->peserta,
                            'apd' => $request->peserta,
                            'apk' => $request->peserta,
                            'p3k' => $request->peserta,
                        ];

                        $alatSesuai = "Yes";
                        
                        if($jabker->peralatan !== null) {
                            if ($jabkerBaru) {
                                $spesifikasiArray = explode(',', $jabker->spesifikasi);
                                foreach ($peralatanArray as $index => $peralatan) {
                                    $i = $index + 1;
                                    $no = 23 + $index;
                                    $formSkema["no$i"] = $no;
                                    $formSkema["alat$i"] = $peralatan;
                                }
                                foreach ($spesifikasiArray as $index => $spesifikasi) {
                                    $i = $index + 1;
                                    $formSkema["spesifikasi$i"] = $spesifikasi;
                                    $formSkema["jumlah$i"] = 1;
                                }
                            } else {
                                $formSkema["no1"] = 23;
                                $formSkema["alat1"] = "Peralatan praktik";
                                foreach ($peralatanArray as $index => $peralatan) {
                                    $i = $index + 1;
                                    $formSkema["spesifikasi$i"] = $peralatan;
                                }
                            }
                            
                            foreach ($requestTools as $requestName => $peralatanName) {
                                $key = array_search($peralatanName, $peralatanArray) + 1;
            
                                if ($request->$requestName === null) {
                                    $alatSesuai = "Off";
                                }
            
                                $formSkema["praktik{$key}_ada"] = $request->$requestName;
                                $formSkema["praktik{$key}_tidakada"] = $request->$requestName === null ? 'Yes' : 'Off';
                                $formSkema["praktik{$key}_sesuai"] = $request->$requestName;
                                $formSkema["praktik{$key}_tidaksesuai"] = $request->$requestName === null ? 'Yes' : 'Off';
                            }
                        }

                        $formSkema["{$metode}_standaralat_yes"] = $alatSesuai;
                        $formSkema["{$metode}_standaralat_no"] = $alatSesuai === "Off" ? "Yes" : "Off";
                        $result = $pdfSkema->fillForm($formSkema)->flatten()->saveAs($skemaOutputPath);
                        //   if (!$result) {
                        //     dd($pdfSkema->getError());
                        // }
                        $fpdiSkema = file_get_contents($skemaOutputPath);
                        $tempFpdiSkema = tempnam(sys_get_temp_dir(), 'pdf_skema');
                        file_put_contents($tempFpdiSkema, $fpdiSkema);
                        $tempFinalObservasiPaths[] = $tempFpdiSkema;
                    }
                }
            }
            $pdfBAObservasi->fillForm($formFieldObservasi)->flatten()->saveAs($outputBAObservasiPath);
            $pdfObservasi->fillForm($formFieldObservasi)->flatten()->saveAs($outputObservasiPath);
            $pdfTtdObservasi->fillForm($formFieldObservasi)->flatten()->saveAs($outputTtdObservasiPath);
            $allObservasiFiles = array_merge([$outputObservasiPath], $tempFinalObservasiPaths, [$outputTtdObservasiPath]);
            $mergedObservasiPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'Merged_Observasi.pdf';
            $pdfMerge = $isLocal
                ? new Pdf($allObservasiFiles, [
                    'command' => $pdftkPath,
                    'useExec' => true,
                ])
                : new Pdf($allObservasiFiles);
            if (!$pdfMerge->cat()->saveAs($mergedObservasiPath)) {
                throw new \Exception("Failed to merge Observasi PDFs: " . $pdfMerge->getError());
            }
        }
        // Halaman Form Checklist Portofolio
        if (($countMetode['Portofolio'] ?? 0) > 0 || ($countMetode['Observasi & Portofolio'] ?? 0) > 0) {
            // $templateBAPortofolio = '/home/lspgatensi/new-balai/veriftuk/app/Http/Controllers/templatePdf/BAPortofolio.pdf';
            // $templatePortofolio = '/home/lspgatensi/new-balai/veriftuk/app/Http/Controllers/templatePdf/checklistPortofolio.pdf';
            // $templateTtdPortofolio = $request->jenisTUK === 'Mandiri' ? '/home/lspgatensi/new-balai/veriftuk/app/Http/Controllers/templatePdf/ttdPortofolioMandiri.pdf' : '/home/lspgatensi/new-balai/veriftuk/app/Http/Controllers/templatePdf/ttdPortofolioSewaktu.pdf';
            $templateBAPortofolio = "{$basePath}/app/Http/Controllers/templatePdf/BAPortofolio.pdf";
            $templatePortofolio = "{$basePath}/app/Http/Controllers/templatePdf/checklistPortofolio.pdf";
            $templateTtdPortofolio = $request->jenisTUK === 'Mandiri' ? "{$basePath}/app/Http/Controllers/templatePdf/ttdPortofolioMandiri.pdf" : "{$basePath}/app/Http/Controllers/templatePdf/ttdPortofolioSewaktu.pdf";
            //

            $outputBAPortofolioPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'BA Portofolio' . '.pdf';
            $outputPortofolioPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'Checklist Portofolio' . '.pdf';
            $outputTtdPortofolioPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'TTD Portofolio.pdf';
            $pdfBAPortofolio = $isLocal
                ? new Pdf($templateBAPortofolio, [
                    'command' => $pdftkPath,
                    'useExec' => true,
                ])
                : new Pdf($templateBAPortofolio);
            $pdfPortofolio = $isLocal
                ? new Pdf($templatePortofolio, [
                    'command' => $pdftkPath,
                    'useExec' => true,
                ])
                : new Pdf($templatePortofolio);
            $pdfTtdPortofolio = $isLocal
                ? new Pdf($templateTtdPortofolio, [
                    'command' => $pdftkPath,
                    'useExec' => true,
                ])
                : new Pdf($templateTtdPortofolio);
            $formFieldPortofolio = [
                'tuk' => $request->tuk,
                'tanggal3' => $formattedTanggal3,
                'tanggal4' => $formattedTanggal4,
                'alamat1' => $alamat1,
                'alamat2' => $alamat2,
                'metode_verif' => $request->metodeVerif,
                'tanggal1' => $formattedTanggal1,
                'verifikator1' => "$verifikator1->Nama ($request->asesor)",
                'ketua' => $request->ketua_tuk,
                'asesor' => $verifikator1->Nama,
                'validator' => $validator !== null ? $validator->Nama : null,
            ];
            if($request->jenisTUK === 'Mandiri') {
                $formFieldPortofolio["skemaportofolio1"] = 'Daftar Skema Terlampir';
            }
            $indexPortofolio = 1;
            $tempFinalPortofolioPaths = [];
            foreach ($request->metode as $index => $metode) {
                if ($metode === 'Portofolio' || $metode === 'Observasi & Portofolio') {
                    $skemaValue = $request->skema[$index] ?? null;
                    $skemaPortofolio[] = $skemaValue;
                    if($request->jenisTUK === 'Sewaktu') {
                        $formFieldPortofolio["noskema$indexPortofolio"] = $indexPortofolio . '.';
                        $formFieldPortofolio["skemaportofolio_BA$indexPortofolio"] = 'TUK  ' . $skemaValue;
                        $formFieldPortofolio["skemaportofolio$indexPortofolio"] = '•  ' . $skemaValue;
                        $indexPortofolio++;
                    }
                    $jabkerBaru = resource_path('json/skema.json');
                    $jabkerBaru = json_decode(file_get_contents($jabkerBaru), true)['myskemabnsp'];
                    $jabkerBaru = collect($jabkerBaru)->firstWhere('nama', $skemaValue);

                    $jabker = DB::connection('mygatensi')
                        ->table('myjabatankerja')
                        ->where('jabatan_kerja', $skemaValue)
                        ->select(['jenjang_id', 'peralatan'])
                        ->first();

                    if ($jabkerBaru) {
                        $jabker = (object) [
                            'jenjang_id' => $jenjangArray[$index] ?? null,
                            'peralatan'  => $jabkerBaru['peralatan'] ?? null,
                            'spesifikasi'  => $jabkerBaru['spesifikasi'] ?? null,
                        ];
                    } elseif (!$jabker) {
                        $jabker = (object) [
                            'jenjang_id' => $jenjangArray[$index] ?? null,
                            'peralatan' => null,
                        ];
                    }

                    if (!empty($skemaValue)) {
                        if ($jabker->jenjang_id > 3) {
                            // $templateskema = '/home/lspgatensi/new-balai/veriftuk/app/Http/Controllers/templatePdf/checklist-j4-9.pdf';

                            $templateskema = "{$basePath}/app/Http/Controllers/templatePdf/checklist-j4-9.pdf";
                        } elseif ($jabker->peralatan === null) {
                            // $templateskema = '/home/lspgatensi/new-balai/veriftuk/app/Http/Controllers/templatePdf/checklist-j4-9.pdf';

                            $templateskema = "{$basePath}/app/Http/Controllers/templatePdf/checklist-j4-9.pdf";
                        } else {
                            // $templateskema = '/home/lspgatensi/new-balai/veriftuk/app/Http/Controllers/templatePdf/checklist-j1-3.pdf'; 

                            $templateskema = "{$basePath}/app/Http/Controllers/templatePdf/checklist-j1-3.pdf";
                            $peralatanArray = explode(',', $jabker->peralatan);

                            $requestTools = [
                                'theodolite' => 'Theodolite',
                                'meteran' => 'Meteran',
                                'penggaris' => 'Penggaris',
                                'waterpass' => 'Waterpass',
                                'autocad' => 'Autocad',
                                'perancah' => 'Perancah',
                                'bouwplank' => 'Bouwplank',
                                'patok' => 'Patok / Bench Mark',
                                'jidar' => 'Jidar',
                                'bandul' => 'Lot / Bandul',
                                'palu_karet' => 'Palu Karet',
                                'palu_besi' => 'Palu',
                                'tang_jepit' => 'Tang Jepit',
                                'tang_potong' => 'Tang Potong',
                                'gergaji_kayu' => 'Gergaji Kayu',
                                'gergaji_besi' => 'Gergaji Besi',
                                'gerinda' => 'Mesin Gerinda',
                                'pembengkok' => 'Alat Pembengkok Besi',
                                'pahat' => 'Pahat Kayu',
                                'obeng' => 'Obeng',
                                'cangkul' => 'Cangkul',
                                'sendok_semen' => 'Sendok Semen',
                                'ember' => 'Ember',
                                'pengerik' => 'Alat Pengerik / Kape',
                                'roll_cat' => 'Kuas Roll Cat',
                                'roll_cat' => 'Tongkat roller',
                                'kuas_cat' => 'Kuas',
                                'nampan' => 'Nampan Cat',
                                'benang' => 'Benang',
                                'paku' => 'Paku',
                                'ampelas' => 'Ampelas',
                                'triplek' => 'Triplek',
                                'lakban' => 'Masking Tape / Lakban',
                                'dempul' => 'Dempul',
                                'papan_applicator' => 'Papan Applicator',
                                'mesin_bor' => 'Mesin Bor',
                                'mesin_serut' => 'Mesin Serut',
                                'mesin_gergaji' => 'Mesin Gergaji',
                                'penggaris_siku' => 'Penggaris Siku',
                                'cat' => 'Cat',
                                'triplek' => 'Triplek'
                            ];
                        }

                        $maxSkema1Length = 46;
                        $skemaWords = explode(' ', $skemaValue);
                        $skema1 = '';
                        $skema2 = '';
                        $skema1Length = 0;
                        foreach ($skemaWords as $word) {
                            if (($skema1Length + strlen($word) + 1) <= $maxSkema1Length) {
                                $skema1 .= ($skema1 === '' ? '' : ' ') . $word;
                                $skema1Length += strlen($word) + 1;
                            } else {
                                $skema2 .= ($skema2 === '' ? '' : ' ') . $word;
                            }
                        }

                        $skemaOutputPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'SKEMA_' . $index . '.pdf';
                        $pdfSkema = $isLocal
                            ? new Pdf($templateskema, [
                                'command' => $pdftkPath,
                                'useExec' => true,
                            ])
                            : new Pdf($templateskema);
                        $formSkema = [
                            'jabker1' => ucwords($skema1),
                            'jabker2' => ucwords($skema2),
                            'jenjang' => $jabker->jenjang_id,
                            'laptop' => $request->peserta,
                            'metode' => 'Portofolio',
                            'kursipemateri' => 2,
                            'kursikerja' => $request->peserta,
                            'komunikasi' => $request->peserta,
                            'pulpen' => $request->peserta,
                            'pensil' => $request->peserta,
                            'tipex' => $request->peserta,
                            'penghapus' => $request->peserta,
                            'spidol' => $request->peserta,
                            'penggaris' => $request->peserta,
                            'hvs' => $request->peserta,
                            'apd' => $request->peserta,
                            'apk' => $request->peserta,
                            'p3k' => $request->peserta,
                        ];

                        $alatSesuai = "Yes";
                        
                        if($jabker->peralatan !== null) {
                            if ($jabkerBaru) {
                                $spesifikasiArray = explode(',', $jabker->spesifikasi);
                                foreach ($peralatanArray as $index => $peralatan) {
                                    $i = $index + 1;
                                    $no = 23 + $index;
                                    $formSkema["no$i"] = $no;
                                    $formSkema["alat$i"] = $peralatan;
                                }
                                foreach ($spesifikasiArray as $index => $spesifikasi) {
                                    $i = $index + 1;
                                    $formSkema["spesifikasi$i"] = $spesifikasi;
                                    $formSkema["jumlah$i"] = 1;
                                }
                            } else {
                                $formSkema["no1"] = 23;
                                $formSkema["alat1"] = "Peralatan praktik";
                                foreach ($peralatanArray as $index => $peralatan) {
                                    $i = $index + 1;
                                    $formSkema["spesifikasi$i"] = $peralatan;
                                }
                            }
                            
                            foreach ($requestTools as $requestName => $peralatanName) {
                                $key = array_search($peralatanName, $peralatanArray) + 1;
            
                                if ($request->$requestName === null) {
                                    $alatSesuai = "Off";
                                }
            
                                $formSkema["praktik{$key}_ada"] = $request->$requestName;
                                $formSkema["praktik{$key}_tidakada"] = $request->$requestName === null ? 'Yes' : 'Off';
                                $formSkema["praktik{$key}_sesuai"] = $request->$requestName;
                                $formSkema["praktik{$key}_tidaksesuai"] = $request->$requestName === null ? 'Yes' : 'Off';
                            }
                        }

                        $formSkema["{$metode}_standaralat_yes"] = $alatSesuai;
                        $formSkema["{$metode}_standaralat_no"] = $alatSesuai === "Off" ? "Yes" : "Off";
                        $pdfSkema->fillForm($formSkema)->flatten()->saveAs($skemaOutputPath);
                        $fpdiSkema = file_get_contents($skemaOutputPath);
                        $tempFpdiSkema = tempnam(sys_get_temp_dir(), 'pdf_skema');
                        file_put_contents($tempFpdiSkema, $fpdiSkema);
                        $tempFinalPortofolioPaths[] = $tempFpdiSkema;
                    }
                }
            }
            $pdfBAPortofolio->fillForm($formFieldPortofolio)->flatten()->saveAs($outputBAPortofolioPath);
            $pdfPortofolio->fillForm($formFieldPortofolio)->flatten()->saveAs($outputPortofolioPath);
            $pdfTtdPortofolio->fillForm($formFieldPortofolio)->flatten()->saveAs($outputTtdPortofolioPath);
            $allPortofolioFiles = array_merge([$outputPortofolioPath], $tempFinalPortofolioPaths, [$outputTtdPortofolioPath]);
            $mergedPortofolioPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'Merged_Portofolio.pdf';
            $pdfMerge = $isLocal
                ? new Pdf($allPortofolioFiles, [
                    'command' => $pdftkPath,
                    'useExec' => true,
                ])
                : new Pdf($allPortofolioFiles);
            if (!$pdfMerge->cat()->saveAs($mergedPortofolioPath)) {
                throw new \Exception("Failed to merge Portofolio PDFs: " . $pdfMerge->getError());
            }
        }

        if ($request->jenisTUK === 'Sewaktu') {
            // $templateSK = '/home/lspgatensi/new-balai/veriftuk/app/Http/Controllers/templatePdf/skSewaktu.pdf';
            $templateSK = "{$basePath}/app/Http/Controllers/templatePdf/skSewaktu.pdf";
        } else if ($request->jenisTUK === 'Mandiri' && $request->skema137 === null) {
            // $templateSK = '/home/lspgatensi/new-balai/veriftuk/app/Http/Controllers/templatePdf/skMandiri2.pdf';
            $templateSK = "{$basePath}/app/Http/Controllers/templatePdf/skMandiri2.pdf";
        } else {
            // $templateSK = '/home/lspgatensi/new-balai/veriftuk/app/Http/Controllers/templatePdf/skMandiri1.pdf';
            $templateSK = "{$basePath}/app/Http/Controllers/templatePdf/skMandiri1.pdf";
        }
        $outputSkPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'Surat Keputusan' . '.pdf';
        $pdfSk = $isLocal
            ? new Pdf($templateSK, [
                'command' => $pdftkPath,
                'useExec' => true,
            ])
            : new Pdf($templateSK);
        $formFieldSK = [
            'no4' => $request->nomor . '/LSP LPK GTK C.003-F/' . $monthRoman . '/' . date('Y'),
            'tanggal4' => $formattedTanggal4,
            'memutuskan' => "$request->tuk sebagai TUK $request->jenisTUK Terverifikasi.",
            'penanggungjawab' => "$request->ketua_tuk sebagai Penanggungjawab $request->tuk.",
            'admin' => "$request->admin sebagai admin $request->tuk.",
            'tanggal_uji' => "$day1 $month1 $year1",
            'tuk' => $request->tuk,
            'validator' => $validator !== null ? "$validator->Nama ($request->met1)" : null,
            'verifikator1' => "$verifikator1->Nama ($request->asesor)",
        ];

        foreach ($skemaArray as $index => $skema) {
            $i = $index + 1;
            $formFieldSK["skemalist$i"] = $skema !== null ? '•  ' . $skema : null;
        }

        $pdfSk->fillForm($formFieldSK)->flatten()->saveAs($outputSkPath);

        $skContents = file_get_contents($outputSkPath);
        
        if (!$skContents) {
            throw new \Exception("Failed to fetch the PDF from the URL");
        }

        // Store the file in a temporary location
        $tempSkPath = tempnam(sys_get_temp_dir(), 'pdf');
        file_put_contents($tempSkPath, $skContents);

        $allFinalFiles = [];
        if ($tempFinalPdfPath && file_exists($tempFinalPdfPath)) {
            $allFinalFiles[] = $tempFinalPdfPath;
        }
        if ($outputBAObservasiPath && file_exists($outputBAObservasiPath) && $request->jenisTUK === 'Sewaktu') {
            $allFinalFiles[] = $outputBAObservasiPath;
        }
        if ($outputBAPortofolioPath && file_exists($outputBAPortofolioPath) && $request->jenisTUK === 'Sewaktu') {
            $allFinalFiles[] = $outputBAPortofolioPath;
        }
        if ($mergedObservasiPath && file_exists($mergedObservasiPath)) {
            $allFinalFiles[] = $mergedObservasiPath;
        }
        if ($mergedPortofolioPath && file_exists($mergedPortofolioPath)) {
            $allFinalFiles[] = $mergedPortofolioPath;
        }
        if ($outputSkPath && file_exists($outputSkPath)) {
            $allFinalFiles[] = $outputSkPath;
        }

        // Merge all into one final PDF
        $finalMergedPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'Final_Merged.pdf';
        $pdfFinal = $isLocal
            ? new Pdf($allFinalFiles, [
                'command' => $pdftkPath,
                'useExec' => true,
            ])
            : new Pdf($allFinalFiles);

        if (!$pdfFinal->cat()->saveAs($finalMergedPath)) {
            throw new \Exception("Failed to merge final PDFs: " . $pdfFinal->getError());
        }

        $finalContents = file_get_contents($finalMergedPath);
        $filename_verifikasi = 'DOKUMEN VERIFIKASI ' . strtoupper($request->tuk) . '.pdf';

        $metodeGabungan = ($countMetode['Observasi & Portofolio'] ?? 0) > 0;
        $metodeTerpisah = ($countMetode['Observasi'] ?? 0) > 0 && ($countMetode['Portofolio'] ?? 0) > 0;

        $fpdiMerge = new Fpdi();
        $fpdiMerge->SetCreator('LSP LPK Gataksindo');
        $fpdiMerge->SetAuthor('LSP LPK Gataksindo');
        $pageCountMerge = $fpdiMerge->setSourceFile($finalMergedPath);
   
         for ($i = 1; $i <= $pageCountMerge; $i++) {
            $templateId = $fpdiMerge->importPage($i);
            $fpdiMerge->addPage();
            $fpdiMerge->SetAutoPageBreak(false, 0);
            $fpdiMerge->SetFillColor(255, 255, 255);
            $fpdiMerge->Rect(0, 0, 210, 10, 'F');
            $fpdiMerge->useTemplate($templateId);
            
            if ($i === 3 && $jumlahSkema < 8 || ($i === 3 && $request->jenisTUK === 'Mandiri')) {
                $fpdiMerge->SetFont('cambriab', 'B', 15.5);
                $fpdiMerge->SetXY(76, 84.4);
                $fpdiMerge->Write(0, $request->nomor . '/LSP LPK GTK B.008-B/' . $monthRoman . '/' . date('Y'));
            }
            if ($i === 4 && $jumlahSkema >= 8 && $request->jenisTUK === 'Sewaktu') {
                $fpdiMerge->SetFont('cambriab', 'B', 15.5);
                $fpdiMerge->SetXY(76, 84.4);
                $fpdiMerge->Write(0, $request->nomor . '/LSP LPK GTK B.008-B/' . $monthRoman . '/' . date('Y'));
            }
            if ($i === 3 && $jumlahSkema < 8) {
                $fpdiMerge->SetFont('cambriab', 'B', 15.5);
                $fpdiMerge->SetXY(76, 84.4);
                $fpdiMerge->Write(0, $request->nomor . '/LSP LPK GTK B.008-B/' . $monthRoman . '/' . date('Y'));
            }
            
            if ($i === 5 && ($metodeGabungan || $metodeTerpisah) && $jumlahSkema < 8) {
                $fpdiMerge->SetFont('cambriab', 'B', 15.5);
                $fpdiMerge->SetXY(76, 84.4);
                $fpdiMerge->Write(0, $request->nomor . '/LSP LPK GTK B.008-B/' . $monthRoman . '/' . date('Y'));
            }

            if ($i === 6 && ($metodeGabungan || $metodeTerpisah) && $jumlahSkema >= 8 && $request->jenisTUK === 'Sewaktu') {
                $fpdiMerge->SetFont('cambriab', 'B', 15.5);
                $fpdiMerge->SetXY(76, 84.4);
                $fpdiMerge->Write(0, $request->nomor . '/LSP LPK GTK B.008-B/' . $monthRoman . '/' . date('Y'));
            }
        }
        $finalPdfMerge = $fpdiMerge->Output('', 'S');

        $result_save = Storage::disk("public")->put("tuk/" . $yesterday->format('Y-m-d') . "/" . strtoupper($request->tuk) . "/$filename_verifikasi", $finalPdfMerge);
        
        // ==== Generate PDF without last page ====
        $fpdiNoLastPage = new Fpdi();
        $fpdiNoLastPage->SetCreator('LSP LPK Gataksindo');
        $fpdiNoLastPage->SetAuthor('LSP LPK Gataksindo');
        $pageCount = $fpdiNoLastPage->setSourceFile($finalMergedPath);
        if ($request->jenisTUK === 'Sewaktu') {
            $pageCount = $pageCount - 1;
        } else if ($request->jenisTUK === 'Mandiri' && $request->skema137 === null) {
            $pageCount = $pageCount - 4;
        } else {
            $pageCount = $pageCount - 7;
        }
        for ($i = 1; $i < $pageCount; $i++) {
            $templateId = $fpdiNoLastPage->importPage($i);
            $fpdiNoLastPage->addPage();
            $fpdiNoLastPage->SetAutoPageBreak(false, 0);
            $fpdiNoLastPage->SetFillColor(255, 255, 255);
            $fpdiNoLastPage->Rect(0, 0, 210, 10, 'F');
            $fpdiNoLastPage->useTemplate($templateId);
            
            if ($i === 2) {
                $fpdiNoLastPage->SetFont('cambriab', 'B', 15.5);
                $fpdiNoLastPage->SetXY(76, 81);
                $fpdiNoLastPage->Write(0, $request->nomor . '/LSP LPK GTK B.007-B/' . $monthRoman . '/' . date('Y'));
            }
            if ($i === 3 && $jumlahSkema < 8) {
                $fpdiNoLastPage->SetFont('cambriab', 'B', 15.5);
                $fpdiNoLastPage->SetXY(76, 84.4);
                $fpdiNoLastPage->Write(0, $request->nomor . '/LSP LPK GTK B.008-B/' . $monthRoman . '/' . date('Y'));
            }
            if ($i === 4 && $jumlahSkema >= 8 && $request->jenisTUK === 'Sewaktu') {
                $fpdiNoLastPage->SetFont('cambriab', 'B', 15.5);
                $fpdiNoLastPage->SetXY(76, 84.4);
                $fpdiNoLastPage->Write(0, $request->nomor . '/LSP LPK GTK B.008-B/' . $monthRoman . '/' . date('Y'));
            }
            if ($i === 5 && ($metodeGabungan || $metodeTerpisah) && $request->jenisTUK === 'Sewaktu' && $jumlahSkema < 8) {
                $fpdiNoLastPage->SetFont('cambriab', 'B', 15.5);
                $fpdiNoLastPage->SetXY(76, 84.4);
                $fpdiNoLastPage->Write(0, $request->nomor . '/LSP LPK GTK B.008-B/' . $monthRoman . '/' . date('Y'));
            }
              if ($i === 6 && ($metodeGabungan || $metodeTerpisah) && $jumlahSkema >= 8 && $request->jenisTUK === 'Sewaktu') {
                $fpdiNoLastPage->SetFont('cambriab', 'B', 15.5);
                $fpdiNoLastPage->SetXY(76, 84.4);
                $fpdiNoLastPage->Write(0, $request->nomor . '/LSP LPK GTK B.008-B/' . $monthRoman . '/' . date('Y'));
            }
        }

        $finalPdfNoLastPage = $fpdiNoLastPage->Output('', 'S');

        // Save the version without the last page
        $result_paperless = Storage::disk("public")->put("tuk-paperless/" . $yesterday->format('Y-m-d') . "/" . strtoupper($request->tuk) . "/$filename_verifikasi", $finalPdfNoLastPage);

        if (!$result_save && !$result_paperless) {
            throw new \Exception("Failed to save the verification file to storage.");
        }

        // Store the file in a temporary location
        $tempFpdiPath = tempnam(sys_get_temp_dir(), 'pdf');
        file_put_contents($tempFpdiPath, $fpdiContents);

        Verification::create([
            'no_surat' => $request->nomor,
            'tuk' => $request->tuk,
            'link' => $filename_verifikasi,
            'verificator' => $verifikator1->Nama,
            'ketua' => $request->ketua_tuk,
            'validator' => $validator?->Nama,
            'filetype' => $jumlahSkema < 8 ? 2 : 1,
            'jenis_tuk' => $request->jenisTUK,
            'skema_observasi' => json_encode($skemaObservasi),
            'skema_portofolio' => json_encode($skemaPortofolio),
            'jabatan_kerja' => $tanggal1->format('Y-m-d'), // Simpan tanggal asesmen untuk ketua_tuk QR
            'created_at' => $yesterday
        ]);

        if (!empty($outputPath)) $this->safeUnlink($outputPath);
        if (!empty($tempFpdiPath)) $this->safeUnlink($tempFpdiPath);
        if (!empty($tempFinalPdfPath)) $this->safeUnlink($tempFinalPdfPath);

        if (!empty($tempFinalObservasiPaths)) {
            foreach ($tempFinalObservasiPaths as $file) {
                if (!empty($file)) $this->safeUnlink($file);
            }
        }

        if (!empty($tempFinalPortofolioPaths)) {
            foreach ($tempFinalPortofolioPaths as $file) {
                if (!empty($file)) $this->safeUnlink($file);
            }
        }
        $link_file = Storage::disk('public')->url('tuk-paperless/' . $yesterday->format('Y-m-d') . "/" . strtoupper($request->tuk) . "/" . rawurlencode($filename_verifikasi));
        return redirect("/sewaktu")->with("success", "Berhasil buat surat verifikasi TUK! (<a target='blank' href='$link_file'>$link_file</a>)");
        // return response()->download($zipFilePath, 'DOKUMEN VERIFIKASI ' . strtoupper($request->tuk) . '.zip')->deleteFileAfterSend(true);
    }
}