<?php

namespace App\Http\Controllers;

use App\Models\Confirm;
use App\Models\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use mikehaertl\pdftk\Pdf;
use Ramsey\Uuid\Uuid;
use setasign\Fpdi\Tcpdf\Fpdi;

class ConfirmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   private function publicPath(string $path): string
    {
    // Ambil full path dari Laravel
    $fullPath = Storage::disk('public')->path($path);

    // Normalisasi agar backslash Windows jadi slash
    return str_replace(['\\', '//'], '/', $fullPath);
    }
    public function index()
    {
        if (Auth::user()->role !== 'direktur') {
            return redirect('/');
        }
        $all_verifications = Verification::where('link', 'LIKE', '%DOKUMEN VERIFIKASI%')->where('approved', null)->get();
        $all_sk = Verification::where('link', 'LIKE', '%DOKUMEN VERIFIKASI%')->where('approved', true)->where('verified', true)->where('confirmed_tuk', true)->get();
        return view('confirm', compact('all_verifications', 'all_sk'));
    }

    public function confirmTukView()
    {
        if (Auth::user()->role !== 'ketua_tuk') {
            return redirect('/');
        }
        $all_verifications = Verification::where('link', 'LIKE', '%DOKUMEN VERIFIKASI%')->where('approved', true)->where('verified', true)->where('confirmed_tuk', null)->get();
        return view('tuk.confirm', compact('all_verifications'));
    }

    public function confirmTuk($id)
    {
        $ketuaTuk = (string) Uuid::uuid4();
        
        $verification = Verification::where('id', $id)->first();
        $url = $this->publicPath('tuk/' . \Carbon\Carbon::parse($verification->created_at)->format('Y-m-d') . '/' . strtoupper($verification->tuk) . '/' . $verification->link);
        $file = file_get_contents( $url);
        $url_paperless = $this->publicPath('tuk-paperless/' . \Carbon\Carbon::parse($verification->created_at)->format('Y-m-d') . '/' . strtoupper($verification->tuk) . '/' . $verification->link);
        $file_paperless = file_get_contents( $url_paperless);
        $currentDate = new \DateTime($verification->created_at);
        $currentDate = $currentDate->modify('+1 day');
        $skemaObservasi = json_decode($verification->skema_observasi, true);
        $skemaPortofolio = json_decode($verification->skema_portofolio, true);
        $observasiCount = is_array($skemaObservasi) ? count($skemaObservasi) : 0;
        $portofolioCount = is_array($skemaPortofolio) ? count($skemaPortofolio) : 0;

        if (!$file) {
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

        if ($verification->jenis_tuk === 'Mandiri') {
            $signaturePage = $pageCount - 4;
        }else {
            $signaturePage = $pageCount - 1;
        }

        DB::connection('reguler')->table('barcodes')->insert([
            'nama' => $verification->ketua_tuk,
            'id_izin' => $verification->ketua_tuk,
            'jabatan' => 'Ketua TUK',
            'url' => 'https://barcode.lspgatensi.id/' . $ketuaTuk,
            'created_at' => $verification->created_at
        ]);

        // Iterate through each page of the original PDF
        for ($i = 1; $i <= $pageCount; $i++) {
            $templateId = $fpdi->importPage($i);
            $fpdi->addPage();
            $fpdi->SetAutoPageBreak(false, 0);
            $fpdi->SetFillColor(255, 255, 255); // Set color to white
            $fpdi->Rect(0, 0, 210, 10, 'F');
            $fpdi->useTemplate($templateId);

            if ($i === ($signaturePage - $portofolioCount - 3) && $isBothMethod) {
                if ($verification->jenis_tuk === 'Mandiri') {
                    $fpdi->write2DBarcode('https://barcode.lspgatensi.id/' . $ketuaTuk, 'QRCODE,H', 35, 180, 20, 20);
                } else {
                    $fpdi->write2DBarcode('https://barcode.lspgatensi.id/' . $ketuaTuk, 'QRCODE,H', 52, 180, 20, 20);
                }
            }
            if ($i === $signaturePage - 1) {
                if ($verification->jenis_tuk === 'Mandiri') {
                    $fpdi->write2DBarcode('https://barcode.lspgatensi.id/' . $ketuaTuk, 'QRCODE,H', 35, 180, 20, 20);
                } else {
                    $fpdi->write2DBarcode('https://barcode.lspgatensi.id/' . $ketuaTuk, 'QRCODE,H', 52, 180, 20, 20);
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

        // Store the file in a temporary location
        $tempPaperlessPath = tempnam(sys_get_temp_dir(), 'pdf');
        file_put_contents($tempPaperlessPath, $file_paperless);

        // Initialize FPDI with TCPDF
        $fpdiPaperless = new Fpdi();

        // Set document information (Optional)
        $fpdiPaperless->SetCreator('LSP LPK Gataksindo');
        $fpdiPaperless->SetAuthor('LSP LPK Gataksindo');
        
        // Load the existing PDF
        $paperlessCount = $fpdiPaperless->setSourceFile($tempPaperlessPath);

        // Iterate through each page of the original PDF
        for ($index = 1; $index <= $paperlessCount; $index++) {
            $templatePaperless = $fpdiPaperless->importPage($index);
            $fpdiPaperless->addPage();
            $fpdiPaperless->SetAutoPageBreak(false, 0);
            $fpdiPaperless->SetFillColor(255, 255, 255); // Set color to white
            $fpdiPaperless->Rect(0, 0, 210, 10, 'F');
            $fpdiPaperless->useTemplate($templatePaperless);

            if ($index === ($paperlessCount - $portofolioCount - 2) && $isBothMethod) {
                if ($verification->jenis_tuk === 'Mandiri') {
                    $fpdiPaperless->write2DBarcode('https://barcode.lspgatensi.id/' . $ketuaTuk, 'QRCODE,H', 35, 180, 20, 20);
                } else {
                    $fpdiPaperless->write2DBarcode('https://barcode.lspgatensi.id/' . $ketuaTuk, 'QRCODE,H', 52, 180, 20, 20);
                }
            }
            if ($index === $paperlessCount) {
                if ($verification->jenis_tuk === 'Mandiri') {
                    $fpdiPaperless->write2DBarcode('https://barcode.lspgatensi.id/' . $ketuaTuk, 'QRCODE,H', 35, 180, 20, 20);
                } else {
                    $fpdiPaperless->write2DBarcode('https://barcode.lspgatensi.id/' . $ketuaTuk, 'QRCODE,H', 52, 180, 20, 20);
                }
            }
        }

        $finalPaperless = $fpdiPaperless->Output('', 'S');
        $tempFinalPaperlessPath = tempnam(sys_get_temp_dir(), 'final_') . '.pdf';
        file_put_contents($tempFinalPaperlessPath, $finalPaperless);

        Storage::disk("public")->delete("tuk-paperless/" . \Carbon\Carbon::parse($verification->created_at)->format('Y-m-d') . '/' . strtoupper($verification->tuk) . '/' . $verification->link);
        $result_save = Storage::disk("public")->put("tuk-paperless/" . \Carbon\Carbon::parse($verification->created_at)->format('Y-m-d') . '/' . strtoupper($verification->tuk) . '/' . $verification->link, $finalPaperless);

        $verification->update([
            'confirmed_tuk' => true
        ]);

        $link_file = Storage::disk('public')->url('tuk-paperless/' . \Carbon\Carbon::parse($verification->created_at)->format('Y-m-d') . '/' . strtoupper($verification->tuk) . '/' . $verification->link);

        unlink($tempFpdiPath);
        unlink($tempPaperlessPath);
        unlink($tempFinalPdfPath);
        unlink($tempFinalPaperlessPath);

        return back()->with('success', "Berhasil konfirmasi Surat Verifikasi! (<a target='blank' href='$link_file'>$link_file</a>)");
    }

    public function sk($id)
    {
        $verification = Verification::where('id', $id)->first();
        $verification->update([
            'approved' => false
        ]);
        $url = Storage::disk('public')->url('tuk/' . \Carbon\Carbon::parse($verification->created_at)->format('Y-m-d') . '/' . strtoupper($verification->tuk) . '/' . $verification->link);

        return back()->with('success', "Berhasil konfirmasi Surat Verifikasi! (<a target='blank' href='$url'>$url</a>)");
    }

    public function confirm($id)
    {
        $direktur_2 = (string) Uuid::uuid4();
        $direktur_3 = (string) Uuid::uuid4();
        
        $verification = Verification::where('id', $id)->first();
        $url = $this->publicPath('tuk/' . \Carbon\Carbon::parse($verification->created_at)->format('Y-m-d') . '/' . strtoupper($verification->tuk) . '/' . $verification->link);
        $file = file_get_contents($url);
        $url_paperless = $this->publicPath('tuk-paperless/' . \Carbon\Carbon::parse($verification->created_at)->format('Y-m-d') . '/' . strtoupper($verification->tuk) . '/' . $verification->link);
        $file_paperless = file_get_contents($url_paperless);
        $currentDate = new \DateTime($verification->created_at);
        $currentDate = $currentDate->modify('+1 day');

        if (!$file) {
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
            $signaturePage = $pageCount - 4;
        }else {
            $signaturePage = $pageCount - 1;
        }

        // Iterate through each page of the original PDF
        for ($i = 1; $i <= $pageCount; $i++) {
            $templateId = $fpdi->importPage($i);
            $fpdi->addPage();
            $fpdi->SetAutoPageBreak(false, 0);
            $fpdi->SetFillColor(255, 255, 255); // Set color to white
            $fpdi->Rect(0, 0, 210, 10, 'F');
            $fpdi->useTemplate($templateId);

            if($i === 1) {
                DB::connection('reguler')->table('barcodes')->insert([
                    'nama' => 'Radinal Efendy, S.T.',
                    'id_izin' => '2220910001',
                    'jabatan' => 'Direktur LSP',
                    'url' => 'https://barcode.lspgatensi.id/' . $direktur_2,
                    'created_at' => $verification->created_at
                ]);
                $fpdi->write2DBarcode('https://barcode.lspgatensi.id/' . $direktur_2, 'QRCODE,H', 30, 224, 20, 20);
            }
            if (($i === 2 && $verification->filetype === '2') || ($i === 2 && $verification->jenis_tuk === 'Mandiri')) {
                $fpdi->write2DBarcode('https://barcode.lspgatensi.id/' . $direktur_2, 'QRCODE,H', 30, 240, 20, 20);
            }
            if ($i === 3 && $verification->filetype === '1') {
                $fpdi->write2DBarcode('https://barcode.lspgatensi.id/' . $direktur_2, 'QRCODE,H', 30, 49, 20, 20);
            }
            if ($i === $signaturePage) {
                DB::connection('reguler')->table('barcodes')->insert([
                    'nama' => 'Radinal Efendy, S.T.',
                    'id_izin' => '2220910001',
                    'jabatan' => 'Direktur LSP',
                    'url' => 'https://barcode.lspgatensi.id/' . $direktur_3,
                    'created_at' => $currentDate
                ]);
                $fpdi->write2DBarcode('https://barcode.lspgatensi.id/' . $direktur_3, 'QRCODE,H', 30, 254, 20, 20);
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

        // Store the file in a temporary location
        $tempPaperlessPath = tempnam(sys_get_temp_dir(), 'pdf');
        file_put_contents($tempPaperlessPath, $file_paperless);

        // Initialize FPDI with TCPDF
        $fpdiPaperless = new Fpdi();

        // Set document information (Optional)
        $fpdiPaperless->SetCreator('LSP LPK Gataksindo');
        $fpdiPaperless->SetAuthor('LSP LPK Gataksindo');
        
        // Load the existing PDF
        $paperlessCount = $fpdiPaperless->setSourceFile($tempPaperlessPath);

        // Iterate through each page of the original PDF
        for ($index = 1; $index <= $paperlessCount; $index++) {
            $templatePaperless = $fpdiPaperless->importPage($index);
            $fpdiPaperless->addPage();
            $fpdiPaperless->SetAutoPageBreak(false, 0);
            $fpdiPaperless->SetFillColor(255, 255, 255); // Set color to white
            $fpdiPaperless->Rect(0, 0, 210, 10, 'F');
            $fpdiPaperless->useTemplate($templatePaperless);

            if($index === 1) {
                $fpdiPaperless->write2DBarcode('https://barcode.lspgatensi.id/' . $direktur_2, 'QRCODE,H', 30, 224, 20, 20);
            }
            if (($index === 2 && $verification->filetype === '2') || ($index === 2 && $verification->jenis_tuk === 'Mandiri')) {
                $fpdiPaperless->write2DBarcode('https://barcode.lspgatensi.id/' . $direktur_2, 'QRCODE,H', 30, 240, 20, 20);
            }
            if ($index === 3 && $verification->filetype === '1') {
                $fpdiPaperless->write2DBarcode('https://barcode.lspgatensi.id/' . $direktur_2, 'QRCODE,H', 30, 49, 20, 20);
            }
        }

        $finalPaperless = $fpdiPaperless->Output('', 'S');
        $tempFinalPaperlessPath = tempnam(sys_get_temp_dir(), 'final_') . '.pdf';
        file_put_contents($tempFinalPaperlessPath, $finalPaperless);

        Storage::disk("public")->delete("tuk-paperless/" . \Carbon\Carbon::parse($verification->created_at)->format('Y-m-d') . '/' . strtoupper($verification->tuk) . '/' . $verification->link);
        $result_save = Storage::disk("public")->put("tuk-paperless/" . \Carbon\Carbon::parse($verification->created_at)->format('Y-m-d') . '/' . strtoupper($verification->tuk) . '/' . $verification->link, $finalPaperless);

        $verification->update([
            'approved' => true
        ]);

        $link_file = Storage::disk('public')->url('tuk-paperless/' . \Carbon\Carbon::parse($verification->created_at)->format('Y-m-d') . '/' . strtoupper($verification->tuk) . '/' . $verification->link);

        unlink($tempFpdiPath);
        unlink($tempPaperlessPath);
        unlink($tempFinalPdfPath);
        unlink($tempFinalPaperlessPath);

        return back()->with('success', "Berhasil konfirmasi Surat Verifikasi! (<a target='blank' href='$link_file'>$link_file</a>)");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Confirm $confirm)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Confirm $confirm)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Confirm $confirm)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Confirm $confirm)
    {
        //
    }
}
