<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use mikehaertl\pdftk\Pdf;
use setasign\Fpdi\Tcpdf\Fpdi;
use Illuminate\Support\Facades\Storage;
use App\Models\Verification;
use TCPDF;
use ZipArchive;
use Illuminate\Support\Facades\DB;

ini_set('max_execution_time', 3800);

class FileController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function sewaktu()
    {
        $allJabker = DB::connection("mygatensi")->table("myjabatankerja")->select(['id_jabatan_kerja', 'jabatan_kerja'])->get();

        return view('file.sewaktu', compact('allJabker'));
    }

    public function mandiri()
    {
        $allSubklas = DB::connection("mygatensi")->table("mysubklasifikasi")->where('id_klasifikasi', 'SI')->select(['kode_subklasifikasi', 'deskripsi_subklasifikasi'])->get();

        return view('file.mandiri', compact('allSubklas'));
    }

    public function archive()
    {
        // Ambil data invoices
        $all_invoices = Verification::select(['no_surat', 'tuk', 'created_at'])->orderBy('id', 'desc')->get()->unique("tuk");

        $all_files_view = [];
        foreach ($all_invoices as $invoice) {
            array_push($all_files_view, [
                "no_surat" => $invoice->no_surat,
                "tuk" => $invoice->tuk,
                "created_at" => \Carbon\Carbon::parse($invoice->created_at)->setTimezone('Asia/Jakarta')->isoFormat('dddd, D MMMM YYYY'),
            ]);
        }

        // For filtered invoice
        $all_invoices_filtered_tuk = Verification::select(['tuk'])->get()->unique("tuk");
        $tuk_filtered = [];
        foreach ($all_invoices_filtered_tuk as $tuk) {
            array_push($tuk_filtered, [
                "nama_tuk" => $tuk->tuk,
            ]);
        }

        return view("archive", compact('all_invoices_view', 'tuk_filtered'));
    }

    public function viewFiles($no)
    {
        // Ambil data invoices
        $all_invoices = Verification::where('no_surat', $no)->orderBy('id', 'desc')->get();

        $all_files_view = [];
        foreach ($all_invoices as $invoice) {
            array_push($all_files_view, [
                "no_surat" => $invoice->no_surat,
                "tuk" => $invoice->tuk,
                "link" => $invoice->link,
                "created_at" => \Carbon\Carbon::parse($invoice->created_at)->setTimezone('Asia/Jakarta')->isoFormat('dddd, D MMMM YYYY'),
            ]);
        }

        // For filtered invoice
        $all_invoices_filtered_tuk = Verification::select(['tuk'])->get();
        $tuk_filtered = [];
        foreach ($all_invoices_filtered_tuk as $tuk) {
            array_push($tuk_filtered, [
                "nama_tuk" => $tuk->tuk,
            ]);
        }

        return view("file.files", compact('all_invoices_view', 'tuk_filtered'));
    }
    
    public function createFileSewaktu(Request $request)
    {
        $skemaArray = $request->input('skema');
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

        // Formatting tanggal1
        $tanggal1 = new \DateTime($request->tanggal1);
        $dayOfWeekEnglish1 = $tanggal1->format('l');
        $dayOfWeekIndonesian1 = $daysIndonesian[$dayOfWeekEnglish1];
        $day1 = $tanggal1->format('d');
        $month1 = $monthsIndonesian[$tanggal1->format('n')];
        $year1 = $tanggal1->format('Y');
        $formattedTanggal1 = "$dayOfWeekIndonesian1 / $day1 $month1 $year1";

        // Formatting tanggal2 (yesterday's date)
        $yesterday = new \DateTime('yesterday');
        $day2 = $yesterday->format('d');
        $month2 = $monthsIndonesian[$yesterday->format('n')];
        $year2 = $yesterday->format('Y');
        $formattedTanggal2 = "Jakarta, $day2 $month2 $year2";

        // Formatting tanggal3 (current date)
        $currentDate = new \DateTime(); // Current date
        $dayOfWeekEnglish3 = $currentDate->format('l');
        $dayOfWeekIndonesian3 = $daysIndonesian[$dayOfWeekEnglish3];
        $day3 = $currentDate->format('d');
        $month3 = $monthsIndonesian[$currentDate->format('n')];
        $year3 = $currentDate->format('Y');
        $formattedTanggal3 = "$dayOfWeekIndonesian3 / $day3 $month3 $year3";

        // Formatting tanggal4 (current date, formatted like tanggal2)
        $day4 = $currentDate->format('d');
        $month4 = $monthsIndonesian[$currentDate->format('n')];
        $year4 = $currentDate->format('Y');
        $formattedTanggal4 = "Jakarta, $day4 $month4 $year4";

        $monthRoman = $romanMonths[date('n')];

        $maxAlamat1Length = 62; // adjust this value based on your specific requirement

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

        $template = $request->skema7 === null ? '/home/lspgatensi/new-balai/verif-tuk/app/Http/Controllers/template2-sewaktu.pdf' : '/home/lspgatensi/new-balai/verif-tuk/app/Http/Controllers/template1-sewaktu.pdf';
        $outputPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'DOKUMEN VERIFIKASI' . '.pdf';
        $pdf = new Pdf($template);
        $formFields = [
            'no1' => $request->nomor . '/LSP LPK GTK B.006-B/' . $monthRoman . '/' . date('Y'),
            'no4' => $request->nomor . '/LSP LPK GTK C.003-F/' . $monthRoman . '/' . date('Y'),
            'tanggal1' => $formattedTanggal1,
            'tanggal2' => $formattedTanggal2,
            'tanggal3' => $formattedTanggal3,
            'tanggal4' => $formattedTanggal4,
            'tuk' => $request->tuk,
            'alamat1' => $alamat1,
            'alamat2' => $alamat2,
            'memutuskan' => $request->tuk . ',',
            'metode' => $request->metode,
            'peserta' => $request->peserta . ' peserta',
            'verifikator1' => $request->verifikator1,
            'verifikator2' => $request->verifikator2,
            'verifikatorlist1' => $request->verifikator1 !== null ? '•  ' . $request->verifikator1 : null,
            'verifikatorlist2' => $request->verifikator2 !== null ? '•  ' . $request->verifikator2 : null,
            'mejapemateri' => ceil($request->peserta / 10),
            'kursipemateri' => ceil($request->peserta / 5),
            'mejakerja' => ceil($request->peserta / 2),
            'kursikerja' => $request->peserta
        ];

        foreach ($skemaArray as $index => $skema) {
            $i = $index + 1;
            $formFields["skema$i"] = $skema;
            $formFields["noskema$i"] = $skema !== null ? "$i." : null;
            $formFields["skemalist$i"] = $skema !== null ? '•  ' . $skema : null;
        }

        $pdf->fillForm($formFields)->flatten()->saveAs($outputPath);

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
            $fpdi->useTemplate($templateId);
            $fpdi->SetFillColor(255, 255, 255); // Set color to white
            $fpdi->Rect(0, 0, 210, 10, 'F');

            if($i === 1) {
                $fpdi->write2DBarcode("Radinal Efendy, ST\n" . "$day2 $month2 $year2" . "\n\n" . "2220910001", 'QRCODE,H', 30, 224, 20, 20);
            }
            if ($i === 2 && $request->skema7 === null) {
                $fpdi->write2DBarcode("Radinal Efendy, ST\n" . "$day2 $month2 $year2" . "\n\n" . "2220910001", 'QRCODE,H', 30, 240, 20, 20);
            }
            if ($i === 2) {
                $fpdi->SetFont('cambriab', 'B', 15.5);
                $fpdi->SetXY(76, 81);
                $fpdi->Write(0, $request->nomor . '/LSP LPK GTK B.007-B/' . $monthRoman . '/' . date('Y'));
            }
            if ($i === 3 && $request->skema7 === null) {
                $fpdi->SetFont('cambriab', 'B', 15.5);
                $fpdi->SetXY(76, 84.4);
                $fpdi->Write(0, $request->nomor . '/LSP LPK GTK B.008-B/' . $monthRoman . '/' . date('Y'));
            }
            if ($i === 3 && $request->skema7 !== null) {
                $fpdi->write2DBarcode("Radinal Efendy, ST\n" . "$day3 $month3 $year3" . "\n\n" . "2220910001", 'QRCODE,H', 30, 49, 20, 20);
            }
            if ($i === 4 && $request->skema7 !== null) {
                $fpdi->SetFont('cambriab', 'B', 15.5);
                $fpdi->SetXY(76, 84.4);
                $fpdi->Write(0, $request->nomor . '/LSP LPK GTK B.008-B/' . $monthRoman . '/' . date('Y'));
            }
            if ($i === 4 && $request->skema7 === null) {
                $fpdi->write2DBarcode($request->verifikator1 . "\n" . $formattedTanggal4 . "\n" . $request->met1, 'QRCODE,H', 156, 51, 20, 20);
                if (isset($request->verifikator2)) {
                    $fpdi->write2DBarcode($request->verifikator2 . "\n" . $formattedTanggal4 . "\n" . $request->met2, 'QRCODE,H', 156, 75, 20, 20);
                }
            } 
            if ($i === 6 && $request->skema7 === null) {
                $fpdi->write2DBarcode($request->verifikator1 . "\n" . $formattedTanggal4 . "\n" . $request->met1, 'QRCODE,H', 155, 166, 20, 20);
                if (isset($request->verifikator2)) {
                    $fpdi->write2DBarcode($request->verifikator2 . "\n" . $formattedTanggal4 . "\n" . $request->met2, 'QRCODE,H', 155, 190, 20, 20);
                }
            } 
            if ($i === 5 && $request->skema7 !== null) {
                $fpdi->write2DBarcode($request->verifikator1 . "\n" . $formattedTanggal4 . "\n" . $request->met1, 'QRCODE,H', 156, 51, 20, 20);
                if (isset($request->verifikator2)) {
                    $fpdi->write2DBarcode($request->verifikator2 . "\n" . $formattedTanggal4 . "\n" . $request->met2, 'QRCODE,H', 156, 75, 20, 20);
                }
            }
            if ($i === 7 && $request->skema7 === null) {
                $fpdi->write2DBarcode("Radinal Efendy, ST\n" . "$day4 $month4 $year4" . "\n\n" . "2220910001", 'QRCODE,H', 30, 240, 20, 20);
            }
            if ($i === 7 && $request->skema7 !== null) {
                $fpdi->write2DBarcode($request->verifikator1 . "\n" . $formattedTanggal4 . "\n" . $request->met1, 'QRCODE,H', 155, 166, 20, 20);
                if (isset($request->verifikator2)) {
                    $fpdi->write2DBarcode($request->verifikator2 . "\n" . $formattedTanggal4 . "\n" . $request->met2, 'QRCODE,H', 155, 190, 20, 20);
                }
            }
            if ($i === 8 && $request->skema7 !== null) {
                $fpdi->write2DBarcode("Radinal Efendy, ST\n" . "$day4 $month4 $year4" . "\n\n" . "2220910001", 'QRCODE,H', 30, 240, 20, 20);
            }
        }

        $finalPdf = $fpdi->Output('', 'S');
        $tempFinalPdfPath = tempnam(sys_get_temp_dir(), 'final_') . '.pdf';
        file_put_contents($tempFinalPdfPath, $finalPdf);

        $filename_verifikasi = 'DOKUMEN VERIFIKASI ' . strtoupper($request->tuk) . '.pdf';
        $result_save = Storage::disk("public")->put("tuk/" . \Carbon\Carbon::now()->setTimezone('Asia/Jakarta')->toDateString() . "/" . strtoupper($request->tuk) . "/$filename_verifikasi", $finalPdf);

        if (!$result_save) {
            throw new \Exception("Failed to save the verification file to storage.");
        }

        Verification::create([
            'no_surat' => $request->nomor,
            'tuk' => $request->tuk,
            'link' => $filename_verifikasi
        ]);

        // Generate skema pdf
        $tempFinalSkemaPaths = [];
        foreach ($skemaArray as $index => $skema) {
            $jabker = DB::connection('mygatensi')->table('myjabatankerja')->where('jabatan_kerja', $skema)->select(['jenjang_id', 'peralatan'])->first();
            if (!empty($skema)) {
                if ($jabker->jenjang_id > 3) {
                    $templateskema = '/home/lspgatensi/new-balai/verif-tuk/app/Http/Controllers/tuksewaktu-j4-9.pdf';
                    $isSesuai = $request->gedung && $request->parkir && $request->bangunan && $request->ruangan && $request->pendingin && $request->internet && $request->mejaasesor && $request->mejaasesi && $request->pc && $request->kabel && $request->komunikasi && $request->dokumentasi && $request->pulpen && $request->pensil && $request->tipex && $request->penghapus && $request->spidol && $request->penggaris && $request->hvs && $request->p3k ? "Yes" : "Off"; 
                } elseif ($jabker->peralatan === null) {
                    $templateskema = '/home/lspgatensi/new-balai/verif-tuk/app/Http/Controllers/tuksewaktu-j1-3-noalat.pdf';
                    $isSesuai = $request->gedung && $request->parkir && $request->bangunan && $request->ruangan && $request->pendingin && $request->internet && $request->mejaasesor && $request->mejaasesi && $request->pc && $request->kabel && $request->komunikasi && $request->dokumentasi && $request->pulpen && $request->pensil && $request->tipex && $request->penghapus && $request->spidol && $request->penggaris && $request->hvs && $request->p3k && $request->apar && $request->rambu && $request->helm && $request->sarung && $request->sepatu && $request->rompi && $request->masker && $request->telinga && $request->harness && $request->kacamata ? "Yes" : "Off";
                } else {
                    $templateskema = '/home/lspgatensi/new-balai/verif-tuk/app/Http/Controllers/tuksewaktu-j1-3.pdf';
                    $isSesuai = $request->gedung && $request->parkir && $request->bangunan && $request->ruangan && $request->pendingin && $request->internet && $request->mejaasesor && $request->mejaasesi && $request->pc && $request->kabel && $request->komunikasi && $request->dokumentasi && $request->pulpen && $request->pensil && $request->tipex && $request->penghapus && $request->spidol && $request->penggaris && $request->hvs && $request->p3k && $request->apar && $request->rambu && $request->helm && $request->sarung && $request->sepatu && $request->rompi && $request->masker && $request->telinga && $request->harness && $request->kacamata ? "Yes" : "Off"; 
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
                $metode = $request->metode === 'Observasi' ? 'obs' : 'port';

                $maxSkema1Length = 46;
                $skemaWords = explode(' ', $skema);
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
                $pdfSkema = new Pdf($templateskema);
                $formSkema = [
                    "tuk" => $request->tuk,
                    "alamat1" => $alamat1,
                    "alamat2" => $alamat2,
                    "jabker1" => ucwords($skema1),
                    "jabker2" => ucwords($skema2),
                    "jenjang" => $jabker->jenjang_id,
                    "tanggal" => $formattedTanggal4,
                    "ketua" => ucwords($request->ketua),
                    "asesor" => ucwords($request->asesor),
                    "manager" => "Akhmadi Hafid",
                    "observasi" => $request->metode === 'Observasi' ? 'Yes' : 'Off',
                    "portofolio" => $request->metode === 'Portofolio' ? 'Yes' : 'Off',
                    "{$metode}_gedung_ada" => $request->gedung,
                    "{$metode}_parkir_ada" => $request->parkir,
                    "{$metode}_bangunan_ada" => $request->bangunan,
                    "{$metode}_ruangan_ada" => $request->ruangan,
                    "{$metode}_1/2pk_ada" => $request->pendingin === '1/2pk' ? 'Yes' : 'Off',
                    "{$metode}_3/4pk_ada" => $request->pendingin === '3/4pk' ? 'Yes' : 'Off',
                    "{$metode}_1pk_ada" => $request->pendingin === '1pk' ? 'Yes' : 'Off',
                    "{$metode}_1,5pk_ada" => $request->pendingin === '1,5pk' ? 'Yes' : 'Off',
                    "{$metode}_kipas_ada" => $request->pendingin === 'kipas' ? 'Yes' : 'Off',
                    "{$metode}_internet_ada" => $request->internet,
                    "{$metode}_mejaasesor_ada" => $request->mejaasesor,
                    "{$metode}_mejaasesi_ada" => $request->mejaasesi,
                    "{$metode}_pc_ada" => $request->pc,
                    "{$metode}_kabel_ada" => $request->kabel,
                    "{$metode}_komunikasi_ada" => $request->komunikasi,
                    "{$metode}_dokumentasi_ada" => $request->dokumentasi,
                    "{$metode}_pulpen_ada" => $request->pulpen,
                    "{$metode}_pensil_ada" => $request->pensil,
                    "{$metode}_tipex_ada" => $request->tipex,
                    "{$metode}_penghapus_ada" => $request->penghapus,
                    "{$metode}_spidol_ada" => $request->spidol,
                    "{$metode}_penggaris_ada" => $request->penggaris,
                    "{$metode}_hvs_ada" => $request->hvs,
                    "{$metode}_p3k_ada" => $request->p3k,
                    "{$metode}_apar_ada" => $request->apar,
                    "{$metode}_rambu_ada" => $request->rambu,
                    "{$metode}_helm_ada" => $request->helm,
                    "{$metode}_sarung_ada" => $request->sarung,
                    "{$metode}_sepatu_ada" => $request->sepatu,
                    "{$metode}_rompi_ada" => $request->rompi,
                    "{$metode}_masker_ada" => $request->masker,
                    "{$metode}_telinga_ada" => $request->telinga,
                    "{$metode}_harness_ada" => $request->harness,
                    "{$metode}_kacamata_ada" => $request->kacamata,
                    "{$metode}_gedung_tidakada" => $request->gedung === null && 'Yes',
                    "{$metode}_parkir_tidakada" => $request->parkir === null && 'Yes',
                    "{$metode}_bangunan_tidakada" => $request->bangunan === null && 'Yes',
                    "{$metode}_ruangan_tidakada" => $request->ruangan === null && 'Yes',
                    "{$metode}_1/2pk_tidakada" => $request->pendingin === null ? 'Yes' : 'Off',
                    "{$metode}_3/4pk_tidakada" => $request->pendingin === null ? 'Yes' : 'Off',
                    "{$metode}_1pk_tidakada" => $request->pendingin === null ? 'Yes' : 'Off',
                    "{$metode}_1,5pk_tidakada" => $request->pendingin === null ? 'Yes' : 'Off',
                    "{$metode}_kipas_tidakada" => $request->pendingin === null ? 'Yes' : 'Off',
                    "{$metode}_internet_tidakada" => $request->internet === null && 'Yes',
                    "{$metode}_mejaasesor_tidakada" => $request->mejaasesor === null && 'Yes',
                    "{$metode}_mejaasesi_tidakada" => $request->mejaasesi === null && 'Yes',
                    "{$metode}_pc_tidakada" => $request->pc === null && 'Yes',
                    "{$metode}_kabel_tidakada" => $request->kabel === null && 'Yes',
                    "{$metode}_komunikasi_tidakada" => $request->komunikasi === null && 'Yes',
                    "{$metode}_dokumentasi_tidakada" => $request->dokumentasi === null && 'Yes',
                    "{$metode}_pulpen_tidakada" => $request->pulpen === null && 'Yes',
                    "{$metode}_pensil_tidakada" => $request->pensil === null && 'Yes',
                    "{$metode}_tipex_tidakada" => $request->tipex === null && 'Yes',
                    "{$metode}_penghapus_tidakada" => $request->penghapus === null && 'Yes',
                    "{$metode}_spidol_tidakada" => $request->spidol === null && 'Yes',
                    "{$metode}_penggaris_tidakada" => $request->penggaris === null && 'Yes',
                    "{$metode}_hvs_tidakada" => $request->hvs === null && 'Yes',
                    "{$metode}_p3k_tidakada" => $request->p3k === null && 'Yes',
                    "{$metode}_apar_tidakada" => $request->apar === null && 'Yes',
                    "{$metode}_rambu_tidakada" => $request->rambu === null && 'Yes',
                    "{$metode}_helm_tidakada" => $request->helm === null && 'Yes',
                    "{$metode}_sarung_tidakada" => $request->sarung === null && 'Yes',
                    "{$metode}_sepatu_tidakada" => $request->sepatu === null && 'Yes',
                    "{$metode}_rompi_tidakada" => $request->rompi === null && 'Yes',
                    "{$metode}_masker_tidakada" => $request->masker === null && 'Yes',
                    "{$metode}_telinga_tidakada" => $request->telinga === null && 'Yes',
                    "{$metode}_harness_tidakada" => $request->harness === null && 'Yes',
                    "{$metode}_kacamata_tidakada" => $request->kacamata === null && 'Yes',
                    "{$metode}_gedung_sesuai" => $request->gedung,
                    "{$metode}_parkir_sesuai" => $request->parkir,
                    "{$metode}_bangunan_sesuai" => $request->bangunan,
                    "{$metode}_ruangan_sesuai" => $request->ruangan,
                    "{$metode}_1/2pk_sesuai" => $request->pendingin === '1/2pk' ? 'Yes' : 'Off',
                    "{$metode}_3/4pk_sesuai" => $request->pendingin === '3/4pk' ? 'Yes' : 'Off',
                    "{$metode}_1pk_sesuai" => $request->pendingin === '1pk' ? 'Yes' : 'Off',
                    "{$metode}_1,5pk_sesuai" => $request->pendingin === '1,5pk' ? 'Yes' : 'Off',
                    "{$metode}_kipas_sesuai" => $request->pendingin === 'kipas' ? 'Yes' : 'Off',
                    "{$metode}_internet_sesuai" => $request->internet,
                    "{$metode}_mejaasesor_sesuai" => $request->mejaasesor,
                    "{$metode}_mejaasesi_sesuai" => $request->mejaasesi,
                    "{$metode}_pc_sesuai" => $request->pc,
                    "{$metode}_kabel_sesuai" => $request->kabel,
                    "{$metode}_komunikasi_sesuai" => $request->komunikasi,
                    "{$metode}_dokumentasi_sesuai" => $request->dokumentasi,
                    "{$metode}_pulpen_sesuai" => $request->pulpen,
                    "{$metode}_pensil_sesuai" => $request->pensil,
                    "{$metode}_tipex_sesuai" => $request->tipex,
                    "{$metode}_penghapus_sesuai" => $request->penghapus,
                    "{$metode}_spidol_sesuai" => $request->spidol,
                    "{$metode}_penggaris_sesuai" => $request->penggaris,
                    "{$metode}_hvs_sesuai" => $request->hvs,
                    "{$metode}_p3k_sesuai" => $request->p3k,
                    "{$metode}_apar_sesuai" => $request->apar,
                    "{$metode}_rambu_sesuai" => $request->rambu,
                    "{$metode}_helm_sesuai" => $request->helm,
                    "{$metode}_sarung_sesuai" => $request->sarung,
                    "{$metode}_sepatu_sesuai" => $request->sepatu,
                    "{$metode}_rompi_sesuai" => $request->rompi,
                    "{$metode}_masker_sesuai" => $request->masker,
                    "{$metode}_telinga_sesuai" => $request->telinga,
                    "{$metode}_harness_sesuai" => $request->harness,
                    "{$metode}_kacamata_sesuai" => $request->kacamata,
                    "{$metode}_gedung_tidaksesuai" => $request->gedung === null && 'Yes',
                    "{$metode}_parkir_tidaksesuai" => $request->parkir === null && 'Yes',
                    "{$metode}_bangunan_tidaksesuai" => $request->bangunan === null && 'Yes',
                    "{$metode}_ruangan_tidaksesuai" => $request->ruangan === null && 'Yes',
                    "{$metode}_internet_tidaksesuai" => $request->internet === null && 'Yes',
                    "{$metode}_mejaasesor_tidaksesuai" => $request->mejaasesor === null && 'Yes',
                    "{$metode}_mejaasesi_tidaksesuai" => $request->mejaasesi === null && 'Yes',
                    "{$metode}_pc_tidaksesuai" => $request->pc === null && 'Yes',
                    "{$metode}_kabel_tidaksesuai" => $request->kabel === null && 'Yes',
                    "{$metode}_komunikasi_tidaksesuai" => $request->komunikasi === null && 'Yes',
                    "{$metode}_dokumentasi_tidaksesuai" => $request->dokumentasi === null && 'Yes',
                    "{$metode}_pulpen_tidaksesuai" => $request->pulpen === null && 'Yes',
                    "{$metode}_pensil_tidaksesuai" => $request->pensil === null && 'Yes',
                    "{$metode}_tipex_tidaksesuai" => $request->tipex === null && 'Yes',
                    "{$metode}_penghapus_tidaksesuai" => $request->penghapus === null && 'Yes',
                    "{$metode}_spidol_tidaksesuai" => $request->spidol === null && 'Yes',
                    "{$metode}_penggaris_tidaksesuai" => $request->penggaris === null && 'Yes',
                    "{$metode}_hvs_tidaksesuai" => $request->hvs === null && 'Yes',
                    "{$metode}_p3k_tidaksesuai" => $request->p3k === null && 'Yes',
                    "{$metode}_apar_tidaksesuai" => $request->apar === null && 'Yes',
                    "{$metode}_rambu_tidaksesuai" => $request->rambu === null && 'Yes',
                    "{$metode}_helm_tidaksesuai" => $request->helm === null && 'Yes',
                    "{$metode}_sarung_tidaksesuai" => $request->sarung === null && 'Yes',
                    "{$metode}_sepatu_tidaksesuai" => $request->sepatu === null && 'Yes',
                    "{$metode}_rompi_tidaksesuai" => $request->rompi === null && 'Yes',
                    "{$metode}_masker_tidaksesuai" => $request->masker === null && 'Yes',
                    "{$metode}_telinga_tidaksesuai" => $request->telinga === null && 'Yes',
                    "{$metode}_harness_tidaksesuai" => $request->harness === null && 'Yes',
                    "{$metode}_kacamata_tidaksesuai" => $request->kacamata === null && 'Yes',
                    "{$metode}_standarsarana_yes" => $isSesuai,
                    "{$metode}_standarsarana_no" => $isSesuai === "Off" ? "Yes" : "Off",
                    "{$metode}_standaralat_yes" => $isSesuai,
                    "{$metode}_standaralat_no" => $isSesuai === "Off" ? "Yes" : "Off",
                ];

                $alatSesuai = "Yes";
                
                if($jabker->peralatan !== null) {
                    foreach ($peralatanArray as $index => $peralatan) {
                        $i = $index + 1;
                        $formSkema["alat$i"] = $peralatan;
                        $formSkema["praktik{$i}_ada"] = 'No';
                        $formSkema["praktik{$i}_tidakada"] = "Yes";
                        $formSkema["praktik{$i}_sesuai"] = 'No';
                        $formSkema["praktik{$i}_tidaksesuai"] = "Yes";
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

                // Initialize FPDI with TCPDF
                $fpdiSkema = new Fpdi();
                $fpdiSkema->SetCreator('LSP LPK Gataksindo');
                $fpdiSkema->SetAuthor('LSP LPK Gataksindo');
                
                // Load the existing PDF
                $pageCount = $fpdiSkema->setSourceFile($tempFpdiSkema);
                for ($i = 1; $i <= $pageCount; $i++) {
                    $templateId = $fpdiSkema->importPage($i);
                    $fpdiSkema->addPage();
                    $fpdiSkema->SetAutoPageBreak(false, 0);
                    $fpdiSkema->useTemplate($templateId);
                    $fpdiSkema->SetFillColor(255, 255, 255); // Set color to white
                    $fpdiSkema->Rect(0, 0, 210, 10, 'F');

                    if ($i === 1) {
                        $fpdiSkema->SetFont('arialnarrow_b', 'B', 18);
                        $text1Width = $fpdiSkema->GetStringWidth(ucwords($skema1));
                        
                        // Calculate the X position to center the text
                        $pageWidth = 210;  // A4 page width in mm
                        $xPosition1 = ($pageWidth - $text1Width) / 2;
                        
                        $fpdiSkema->SetXY($xPosition1, 61);
                        $fpdiSkema->Write(0, ucwords($skema1));

                        $text2Width = $fpdiSkema->GetStringWidth(ucwords($skema2));
                        
                        // Calculate the X position to center the text
                        $xPosition2 = ($pageWidth - $text2Width) / 2;
                        
                        $fpdiSkema->SetXY($xPosition2, 68);
                        $fpdiSkema->Write(0, ucwords($skema2));
                    }
                    if ($i === 8 && $jabker->jenjang_id > 3 || $i === 8 && $jabker->jenjang_id < 4 && $jabker->peralatan === null) {
                        $fpdiSkema->SetFont('arialnarrow_b', 'B', 10);
                        $fpdiSkema->SetXY(73, 91.5);
                        $fpdiSkema->Write(0, $isSesuai === 'Yes' ? 'Sesuai' : 'Tidak Sesuai');

                        $fpdiSkema->write2DBarcode(ucwords($request->ketua) . "\n" . "$day4 $month4 $year4" . "\n" . "Ketua TUK", 'QRCODE,H', 35, 200, 19, 19);
                        $fpdiSkema->write2DBarcode(ucwords($request->asesor) . "\n" . "$day4 $month4 $year4" . "\n" . "Asesor Kompetensi", 'QRCODE,H', 97, 200, 19, 19);
                        $fpdiSkema->write2DBarcode("Akhmadi Hafid\n" . "$day4 $month4 $year4" . "\n" . "Manager Sertifikasi LSP LPK GTK", 'QRCODE,H', 161, 200, 19, 19);
                    }
                    if ($i === 9 && $jabker->jenjang_id < 4) {
                        $fpdiSkema->SetFont('arialnarrow_b', 'B', 10);
                        $fpdiSkema->SetXY(73, 91.5);
                        if ($jabker->peralatan === null) {
                            $fpdiSkema->Write(0, $isSesuai === 'Yes' ? 'Sesuai' : 'Tidak Sesuai');
                        } else {
                            $fpdiSkema->Write(0, $isSesuai === 'Yes' && $alatSesuai === 'Yes' ? 'Sesuai' : 'Tidak Sesuai');
                        }

                        $fpdiSkema->write2DBarcode(ucwords($request->ketua) . "\n" . "$day4 $month4 $year4" . "\n" . "Ketua TUK", 'QRCODE,H', 35, 200, 19, 19);
                        $fpdiSkema->write2DBarcode(ucwords($request->asesor) . "\n" . "$day4 $month4 $year4" . "\n" . "Asesor Kompetensi", 'QRCODE,H', 97, 200, 19, 19);
                        $fpdiSkema->write2DBarcode("Akhmadi Hafid\n" . "$day4 $month4 $year4" . "\n" . "Manager Sertifikasi LSP LPK GTK", 'QRCODE,H', 161, 200, 19, 19);
                    }
                }

                $finalPdfSkema = $fpdiSkema->Output('', 'S');
                $tempFinalPdfSkema = tempnam(sys_get_temp_dir(), 'final_') . '.pdf';
                file_put_contents($tempFinalPdfSkema, $finalPdfSkema);

                $filename_skema = 'DOKUMEN SKEMA ' . str_replace('/', '', strtoupper($skema)) . '.pdf';
                $result_save = Storage::disk("public")->put("tuk/" . \Carbon\Carbon::now()->setTimezone('Asia/Jakarta')->toDateString() . "/" . strtoupper($request->tuk) . "/$filename_skema", $finalPdfSkema);

                if (!$result_save) {
                    throw new \Exception("Failed to save the skema file to storage.");
                }

                Verification::create([
                    'no_surat' => $request->nomor,
                    'tuk' => $request->tuk,
                    'link' => $filename_skema
                ]);

                $tempFinalSkemaPaths[] = $tempFinalPdfSkema;
            }
        }

        $zip = new ZipArchive();
        $zipFilePath = tempnam(sys_get_temp_dir(), 'pdf_zip_') . '.zip';

        if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
            // Add the main PDF file
            if (file_exists($tempFinalPdfPath)) {
                $zip->addFile($tempFinalPdfPath, 'DOKUMEN VERIFIKASI ' . strtoupper($request->tuk) . '.pdf');
            } else {
                throw new \Exception('Main PDF file not found: ' . $tempFinalPdfPath);
            }

            // Add the skema PDF files
            foreach ($tempFinalSkemaPaths as $index => $tempFinalSkemaPath) {
                if (file_exists($tempFinalSkemaPath)) {
                    $zip->addFile($tempFinalSkemaPath, 'DOKUMEN SKEMA ' . str_replace('/', '', strtoupper($skemaArray[$index])) . '.pdf');
                } else {
                    throw new \Exception('Skema PDF file not found: ' . $tempFinalSkemaPath);
                }
            }

            $zip->close();
        } else {
            throw new \Exception('Failed to create zip file');
        }

        unlink($outputPath);
        unlink($tempFpdiPath);
        unlink($tempFinalPdfPath);
        foreach ($tempFinalSkemaPaths as $tempFinalSkemaPath) {
            unlink($tempFinalSkemaPath);
        }

        return response()->download($zipFilePath, 'DOKUMEN VERIFIKASI ' . strtoupper($request->tuk) . '.zip')->deleteFileAfterSend(true);
    }

    public function createFileMandiri(Request $request)
    {
        $subklasArray = $request->input('subklas');
        $jenjangArray = $request->input('jenjang');
        $skemaArray = [];

        $additionalData = [
            'Gedung' => [
                'subklas' => ['Bangunan Persampahan', 'Bangunan Air Minum', 'Bangunan Air Limbah', 'Geoteknik dan Pondasi', 'Grouting'],
                'skema' => ['Ahli Geodesi Dan Bangunan Gedung', 'Operator Utama Survei Terestris', 'Teknisi Survei Terestris', 'Surveyor Rekayasa', 'Surveyor Terestris', 'Juru Ukur (Surveyor) Level 2', 'Operator Muda Survei Terestris', 'Operator Madya Survei Terestris']
            ],
            'Jalan' => [
                'subklas' => ['Material', 'Drainase Perkotaan', 'Geoteknik dan Pondasi', 'Jalan Rel', 'Grouting', 'Terowongan'],
                'skema' => ['Ahli Geodesi Untuk Perencanaan Teknis Jalan dan Jembatan', 'Operator Utama Survei Terestris', 'Teknisi Survei Terestris', 'Surveyor Rekayasa', 'Surveyor Terestris', 'Juru Ukur (Surveyor) Level 2', 'Operator Muda Survei Terestris', 'Operator Madya Survei Terestris']
            ],
            'Jembatan' => [
                'subklas' => ['Material', 'Jalan', 'Grouting', 'Geoteknik dan Pondasi', 'Bangunan Pelabuhan']
            ],
            'Bendung dan Bendungan' => [
                'subklas' => ['Geoteknik dan Pondasi', 'Grouting', 'Terowongan', 'Bangunan Pelabuhan']
            ],
            'Air Tanah dan Air Baku' => [
                'subklas' => ['Bendung dan Bendungan', 'Irigasi dan Rawa', 'Sungai dan Pantai', 'Bangunan Air Minum', 'Drainase Perkotaan']
            ]
        ];

        foreach ($additionalData as $key => $data) {
            if (in_array($key, $subklasArray)) {
                $subklasArray = array_unique(array_merge($subklasArray, $data['subklas']));
                if (isset($data['skema'])) {
                    $skemaArray = array_unique(array_merge($skemaArray, $data['skema']));
                }
            }
        }

        // Handle jenjang adjustment
        foreach ($subklasArray as $index => $subklas) {
            $jenjang = isset($jenjangArray[$index]) ? $jenjangArray[$index] : null;
            // Adjust the jenjang value
            if ($jenjang === 7) {
                $jenjang = 6;
            } elseif ($jenjang === 4 || $jenjang === 5) {
                $jenjang = 3;
            }

            if ($jenjang !== null) {
                // Query based on both subklas and jenjang
                $jabatans = DB::connection('mygatensi')->table('myjabatankerja')
                    ->where('subklasifikasi', $subklas)
                    ->where('jenjang_id', '<=', $jenjang)
                    ->whereIn('id_jabatan_kerja', [
                    'SI011001', 'SI011002', 'SI011007', 'SI011011', 'SI011012', 'SI011013', 'SI011015', 'SI011016', 'SI011017', 
                    'SI011018', 'SI011019', 'SI011020', 'SI011021', 'SI011022', 'SI011023', 'SI011024', 'SI011025', 'SI012001', 
                    'SI012002', 'SI012003', 'SI012018', 'SI012019', 'SI012022', 'SI012023', 'SI012024', 'SI012025', 'SI012026', 
                    'SI012027', 'SI013001', 'SI013002', 'SI013003', 'SI013013', 'SI013015', 'SI013016', 'SI013017', 'SI013020', 
                    'SI013023', 'SI013026', 'SI013027', 'SI013028', 'SI013029', 'SI013030', 'SI013039', 'SI013040', 'SI013041', 
                    'SI013044', 'SI013045', 'SI013046', 'SI013047', 'SI013048', 'SI013049', 'SI013050', 'SI013051', 'SI013052', 
                    'SI013053', 'SI013054', 'SI013055', 'SI013056', 'SI021002', 'SI021005', 'SI021006', 'SI022001', 'SI022003', 
                    'SI022012', 'SI022013', 'SI022014', 'SI023001', 'SI031006', 'SI031010', 'SI031012', 'SI031013', 'SI031014', 
                    'SI031015', 'SI031016', 'SI031017', 'SI031018', 'SI031019', 'SI031020', 'SI032002', 'SI032004', 'SI032006', 
                    'SI032014', 'SI032015', 'SI032016', 'SI032017', 'SI032018', 'SI033001', 'SI033006', 'SI033007', 'SI033008', 
                    'SI033009', 'SI033015', 'SI041003', 'SI041010', 'SI041011', 'SI041012', 'SI041013', 'SI041014', 'SI041015', 
                    'SI041016', 'SI041017', 'SI041018', 'SI041019', 'SI042002', 'SI042003', 'SI042005', 'SI042007', 'SI042008', 
                    'SI042009', 'SI042010', 'SI042011', 'SI061001', 'SI061008', 'SI071002', 'SI071004', 'SI071005', 'SI071006', 
                    'SI071007', 'SI071008', 'SI071009', 'SI072001', 'SI072004', 'SI072005', 'SI072006', 'SI073001', 'SI081003', 
                    'SI081004', 'SI081007', 'SI081008', 'SI081009', 'SI081010', 'SI081011', 'SI081012', 'SI081013', 'SI082003', 
                    'SI082004', 'SI082006', 'SI082008', 'SI082009', 'SI082010', 'SI082011', 'SI083001', 'SI083002', 'SI091001', 
                    'SI091002', 'SI091004', 'SI091006', 'SI091007', 'SI091008', 'SI091009', 'SI091010', 'SI092001', 'SI092002', 
                    'SI092003', 'SI092004', 'SI093001', 'SI093002', 'SI101001', 'SI101002', 'SI101003', 'SI101007', 'SI101011', 
                    'SI101012', 'SI101013', 'SI101014', 'SI101015', 'SI101016', 'SI111001', 'SI112001', 'SI112002', 'SI113001', 
                    'SI122001', 'SI132001', 'SI132002', 'SI132004', 'SI132005', 'SI141001', 'SI141002', 'SI141003', 'SI142001', 
                    'SI142002', 'SI142003', 'SI151001', 'SI151002', 'SI151005', 'SI151006', 'SI151007', 'SI151008', 'SI151009', 
                    'SI151010', 'SI152001', 'SI152004', 'SI161001', 'SI161002', 'SI161003', 'SI161004', 'SI161005', 'SI161006', 
                    'SI161007', 'SI161008', 'SI161009', 'SI161010', 'SI161011', 'SI161012', 'SI161013', 'SI161014', 'SI162003', 
                    'SI162004', 'SI162005', 'SI162006', 'SI163001', 'SI163005', 'SI163006', 'SI163007', 'SI171001', 'SI171003', 
                    'SI172001', 'SI172002', 'SI191001', 'SI191002', 'SI191003', 'SI221001', 'SI221002', 'SI221003', 'SI232001', 
                    'SI233001', 'SI233002'
                ])
                ->pluck('jabatan_kerja')
                ->toArray();
            $skemaArray = array_unique(array_merge($skemaArray, $jabatans));
            }
        }
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

        // Formatting tanggal1
        $tanggal1 = new \DateTime($request->tanggal1);
        $dayOfWeekEnglish1 = $tanggal1->format('l');
        $dayOfWeekIndonesian1 = $daysIndonesian[$dayOfWeekEnglish1];
        $day1 = $tanggal1->format('d');
        $month1 = $monthsIndonesian[$tanggal1->format('n')];
        $year1 = $tanggal1->format('Y');
        $formattedTanggal1 = "$dayOfWeekIndonesian1 / $day1 $month1 $year1";

        // Formatting tanggal2 (yesterday's date)
        $yesterday = new \DateTime('yesterday');
        $day2 = $yesterday->format('d');
        $month2 = $monthsIndonesian[$yesterday->format('n')];
        $year2 = $yesterday->format('Y');
        $formattedTanggal2 = "Jakarta, $day2 $month2 $year2";

        // Formatting tanggal3 (current date)
        $currentDate = new \DateTime(); // Current date
        $dayOfWeekEnglish3 = $currentDate->format('l');
        $dayOfWeekIndonesian3 = $daysIndonesian[$dayOfWeekEnglish3];
        $day3 = $currentDate->format('d');
        $month3 = $monthsIndonesian[$currentDate->format('n')];
        $year3 = $currentDate->format('Y');
        $formattedTanggal3 = "$dayOfWeekIndonesian3 / $day3 $month3 $year3";

        // Formatting tanggal4 (current date, formatted like tanggal2)
        $day4 = $currentDate->format('d');
        $month4 = $monthsIndonesian[$currentDate->format('n')];
        $year4 = $currentDate->format('Y');
        $formattedTanggal4 = "Jakarta, $day4 $month4 $year4";

        $monthRoman = $romanMonths[date('n')];

        $maxAlamat1Length = 62; // adjust this value based on your specific requirement

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

        $template = count($skemaArray) > 136 ? '/home/lspgatensi/new-balai/verif-tuk/app/Http/Controllers/template1-mandiri.pdf' : '/home/lspgatensi/new-balai/verif-tuk/app/Http/Controllers/template2-mandiri.pdf';
        $outputPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'DOKUMEN VERIFIKASI' . '.pdf';
        $pdf = new Pdf($template);
        $formFields = [
            'no1' => $request->nomor . '/LSP LPK GTK B.006-A/' . $monthRoman . '/' . date('Y'),
            'no4' => $request->nomor . '/LSP LPK GTK C.003-E/' . $monthRoman . '/' . date('Y'),
            'tanggal1' => $formattedTanggal1,
            'tanggal2' => $formattedTanggal2,
            'tanggal3' => $formattedTanggal3,
            'tanggal4' => $formattedTanggal4,
            'tuk' => $request->tuk,
            'alamat1' => $alamat1,
            'alamat2' => $alamat2,
            'memutuskan' => $request->tuk . ',',
            'metode' => $request->metode,
            'peserta' => $request->peserta . ' peserta',
            'mejapemateri' => ceil($request->peserta / 10),
            'kursipemateri' => ceil($request->peserta / 5),
            'mejakerja' => ceil($request->peserta / 2),
            'kursikerja' => $request->peserta
        ];

        foreach ($skemaArray as $index => $skema) {
            $i = $index + 1;
            $formFields["skemalist$i"] = $skema !== null ? '•  ' . $skema : null;
        }

        $pdf->fillForm($formFields)->flatten()->saveAs($outputPath);

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
            $fpdi->useTemplate($templateId);
            $fpdi->SetFillColor(255, 255, 255); // Set color to white
            $fpdi->Rect(0, 0, 210, 10, 'F');

            if($i === 1) {
                $fpdi->write2DBarcode("Radinal Efendy, ST\n" . "$day2 $month2 $year2" . "\n\n" . "2220910001", 'QRCODE,H', 30, 219, 20, 20);
            }
            if ($i === 2) {
                $fpdi->SetFont('cambriab', 'B', 15.5);
                $fpdi->SetXY(76, 77.8);
                $fpdi->Write(0, $request->nomor . '/LSP LPK GTK B.007-A/' . $monthRoman . '/' . date('Y'));
                $fpdi->write2DBarcode("Radinal Efendy, ST\n" . "$day2 $month2 $year2" . "\n\n" . "2220910001", 'QRCODE,H', 30, 189, 20, 20);
            }
            if ($i === 3) {
                $fpdi->SetFont('cambriab', 'B', 15.5);
                $fpdi->SetXY(76, 84.4);
                $fpdi->Write(0, $request->nomor . '/LSP LPK GTK B.008-A/' . $monthRoman . '/' . date('Y'));
                $fpdi->write2DBarcode("Dr. Ir. Sugimin Pranoto, ST., M.Eng., IPM., ASEAN Eng.\n" . $formattedTanggal4 . "\nMET.000.004966 2021", 'QRCODE,H', 156, 170, 20, 20);
            }
            if ($i === 4) {
                $fpdi->write2DBarcode("Dr. Ir. Sugimin Pranoto, ST., M.Eng., IPM., ASEAN Eng.\n" . $formattedTanggal4 . "\nMET.000.004966 2021", 'QRCODE,H', 156, 161, 20, 20);
            }
            if ($i === 5) {
                $fpdi->write2DBarcode("Radinal Efendy, ST\n" . "$day4 $month4 $year4" . "\n\n" . "2220910001", 'QRCODE,H', 30, 240, 20, 20);
            }
        }

        $finalPdf = $fpdi->Output('', 'S');
        $tempFinalPdfPath = tempnam(sys_get_temp_dir(), 'final_') . '.pdf';
        file_put_contents($tempFinalPdfPath, $finalPdf);

        $filename_verifikasi = 'DOKUMEN VERIFIKASI ' . strtoupper($request->tuk) . '.pdf';
        $result_save = Storage::disk("public")->put("tuk/" . \Carbon\Carbon::now()->setTimezone('Asia/Jakarta')->toDateString() . "/" . strtoupper($request->tuk) . "/$filename_verifikasi", $finalPdf);

        if (!$result_save) {
            throw new \Exception("Failed to save the verification file to storage.");
        }

        Verification::create([
            'no_surat' => $request->nomor,
            'tuk' => $request->tuk,
            'link' => $filename_verifikasi
        ]);

        // Generate skema pdf
        $tempFinalSkemaPaths = [];
        foreach ($skemaArray as $index => $skema) {
            $jabker = DB::connection('mygatensi')->table('myjabatankerja')->where('jabatan_kerja', $skema)->select(['jenjang_id', 'peralatan'])->first();
            if (!empty($skema)) {
                if ($jabker->jenjang_id > 3) {
                    $templateskema = '/home/lspgatensi/new-balai/verif-tuk/app/Http/Controllers/tukmandiri-j4-9.pdf';
                } elseif ($jabker->peralatan === null) {
                    $templateskema = '/home/lspgatensi/new-balai/verif-tuk/app/Http/Controllers/tukmandiri-j1-3-noalat.pdf';
                } else {
                    $templateskema = '/home/lspgatensi/new-balai/verif-tuk/app/Http/Controllers/tukmandiri-j1-3.pdf';
                    $peralatanArray = explode(',', $jabker->peralatan);
                }
                $metode = $request->metode === 'Observasi' ? 'obs' : 'port';

                $maxSkema1Length = 46;
                $skemaWords = explode(' ', $skema);
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
                $pdfSkema = new Pdf($templateskema);
                $formSkema = [
                    "tuk" => $request->tuk,
                    "alamat1" => $alamat1,
                    "alamat2" => $alamat2,
                    "jabker1" => ucwords($skema1),
                    "jabker2" => ucwords($skema2),
                    "jenjang" => $jabker->jenjang_id,
                    "tanggal" => $formattedTanggal4,
                    "ketua" => ucwords($request->ketua),
                    "asesor" => ucwords($request->asesor),
                ];
                
                if($jabker->peralatan !== null) {
                    foreach ($peralatanArray as $index => $peralatan) {
                        $i = $index + 1;
                        $formSkema["alat$i"] = $peralatan;
                        $formSkema["praktik{$i}_ada"] = "Yes";
                        $formSkema["praktik{$i}_tidakada"] = "Off";
                        $formSkema["praktik{$i}_sesuai"] = "Yes";
                        $formSkema["praktik{$i}_tidaksesuai"] = "Off";
                    }
                }
                $pdfSkema->fillForm($formSkema)->flatten()->saveAs($skemaOutputPath);
                $fpdiSkema = file_get_contents($skemaOutputPath);
                $tempFpdiSkema = tempnam(sys_get_temp_dir(), 'pdf_skema');
                file_put_contents($tempFpdiSkema, $fpdiSkema);

                // Initialize FPDI with TCPDF
                $fpdiSkema = new Fpdi();
                $fpdiSkema->SetCreator('LSP LPK Gataksindo');
                $fpdiSkema->SetAuthor('LSP LPK Gataksindo');
                
                // Load the existing PDF
                $pageCount = $fpdiSkema->setSourceFile($tempFpdiSkema);
                for ($i = 1; $i <= $pageCount; $i++) {
                    $templateId = $fpdiSkema->importPage($i);
                    $fpdiSkema->addPage();
                    $fpdiSkema->SetAutoPageBreak(false, 0);
                    $fpdiSkema->useTemplate($templateId);
                    $fpdiSkema->SetFillColor(255, 255, 255); // Set color to white
                    $fpdiSkema->Rect(0, 0, 210, 10, 'F');

                    if ($i === 1) {
                        $fpdiSkema->SetFont('arialnarrow_b', 'B', 18);
                        $text1Width = $fpdiSkema->GetStringWidth(ucwords($skema1));
                        
                        // Calculate the X position to center the text
                        $pageWidth = 210;  // A4 page width in mm
                        $xPosition1 = ($pageWidth - $text1Width) / 2;
                        
                        $fpdiSkema->SetXY($xPosition1, 61);
                        $fpdiSkema->Write(0, ucwords($skema1));

                        $text2Width = $fpdiSkema->GetStringWidth(ucwords($skema2));
                        
                        // Calculate the X position to center the text
                        $xPosition2 = ($pageWidth - $text2Width) / 2;
                        
                        $fpdiSkema->SetXY($xPosition2, 68);
                        $fpdiSkema->Write(0, ucwords($skema2));
                    }
                    if ($i === 8 && $jabker->jenjang_id > 3 || $i === 8 && $jabker->jenjang_id < 4 && $jabker->peralatan === null) {
                        $fpdiSkema->SetFont('arialnarrow_b', 'B', 10);
                        $fpdiSkema->SetXY(73, 91.5);
                        $fpdiSkema->Write(0, 'Sesuai');

                        $fpdiSkema->write2DBarcode(ucwords($request->ketua) . "\n" . "$day4 $month4 $year4" . "\n" . "Ketua TUK", 'QRCODE,H', 35, 200, 19, 19);
                        $fpdiSkema->write2DBarcode(ucwords($request->asesor) . "\n" . "$day4 $month4 $year4" . "\n" . "Asesor Kompetensi", 'QRCODE,H', 97, 200, 19, 19);
                        $fpdiSkema->write2DBarcode("Dr. Ir. Sugimin Pranoto, ST., M.Eng., IPM., ASEAN Eng.\n" . "$day4 $month4 $year4" . "\n" . "Asesor Lisensi LSP LPK GTK", 'QRCODE,H', 161, 200, 19, 19);
                    }
                    if ($i === 9 && $jabker->jenjang_id < 4) {
                        $fpdiSkema->SetFont('arialnarrow_b', 'B', 10);
                        $fpdiSkema->SetXY(73, 91.5);
                        $fpdiSkema->Write(0, 'Sesuai');

                        $fpdiSkema->write2DBarcode(ucwords($request->ketua) . "\n" . "$day4 $month4 $year4" . "\n" . "Ketua TUK", 'QRCODE,H', 35, 200, 19, 19);
                        $fpdiSkema->write2DBarcode(ucwords($request->asesor) . "\n" . "$day4 $month4 $year4" . "\n" . "Asesor Kompetensi", 'QRCODE,H', 97, 200, 19, 19);
                        $fpdiSkema->write2DBarcode("Dr. Ir. Sugimin Pranoto, ST., M.Eng., IPM., ASEAN Eng.\n" . "$day4 $month4 $year4" . "\n" . "Asesor Lisensi LSP LPK GTK", 'QRCODE,H', 161, 200, 19, 19);
                    }
                }

                $finalPdfSkema = $fpdiSkema->Output('', 'S');
                $tempFinalPdfSkema = tempnam(sys_get_temp_dir(), 'final_') . '.pdf';
                file_put_contents($tempFinalPdfSkema, $finalPdfSkema);

                $filename_skema = 'DOKUMEN SKEMA ' . str_replace('/', '', strtoupper($skema)) . '.pdf';
                $result_save = Storage::disk("public")->put("tuk/" . \Carbon\Carbon::now()->setTimezone('Asia/Jakarta')->toDateString() . "/" . strtoupper($request->tuk) . "/$filename_skema", $finalPdfSkema);

                if (!$result_save) {
                    throw new \Exception("Failed to save the skema file to storage.");
                }

                Verification::create([
                    'no_surat' => $request->nomor,
                    'tuk' => $request->tuk,
                    'link' => $filename_skema
                ]);

                $tempFinalSkemaPaths[] = $tempFinalPdfSkema;
            }
        }

        $zip = new ZipArchive();
        $zipFilePath = tempnam(sys_get_temp_dir(), 'pdf_zip_') . '.zip';

        if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
            // Add the main PDF file
            if (file_exists($tempFinalPdfPath)) {
                $zip->addFile($tempFinalPdfPath, 'DOKUMEN VERIFIKASI ' . strtoupper($request->tuk) . '.pdf');
            } else {
                throw new \Exception('Main PDF file not found: ' . $tempFinalPdfPath);
            }

            // Add the skema PDF files
            foreach ($tempFinalSkemaPaths as $index => $tempFinalSkemaPath) {
                if (file_exists($tempFinalSkemaPath)) {
                    $zip->addFile($tempFinalSkemaPath, 'DOKUMEN SKEMA ' . str_replace('/', '', strtoupper($skemaArray[$index])) . '.pdf');
                } else {
                    throw new \Exception('Skema PDF file not found: ' . $tempFinalSkemaPath);
                }
            }

            $zip->close();
        } else {
            throw new \Exception('Failed to create zip file');
        }

        unlink($outputPath);
        unlink($tempFpdiPath);
        unlink($tempFinalPdfPath);
        foreach ($tempFinalSkemaPaths as $tempFinalSkemaPath) {
            unlink($tempFinalSkemaPath);
        }

        return response()->download($zipFilePath, 'DOKUMEN VERIFIKASI ' . strtoupper($request->tuk) . '.zip')->deleteFileAfterSend(true);
    }
}