<?php

namespace App\Http\Controllers;

use App\Models\Verification;
use Illuminate\Http\Request;
use mikehaertl\pdftk\Pdf;
use setasign\Fpdi\Tcpdf\Fpdi;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Ramsey\Uuid\Uuid;
ini_set('max_execution_time', 3800);

class GenerateController extends Controller
{
    public function generateSewaktu()
    {
        $direktur_1 = (string) Uuid::uuid4();
        $direktur_2 = (string) Uuid::uuid4();
        $barcodeVerifikator1 = (string) Uuid::uuid4();
        $barcodeValidator = (string) Uuid::uuid4();
        $barcodeKetuaTUK = (string) Uuid::uuid4();
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

        $all_files = [];
        $open = fopen(storage_path("app/data5_2.csv"), "r");
        try {
            while (($data = fgetcsv($open, 1000, ";")) !== false) {
                $data[0] = preg_replace('/^\xEF\xBB\xBF/', '', $data[0]);
                error_log($data[0]);
                $dataSkema = $data[8];
                $skemaArray = explode(', ', $dataSkema);

                $verifikator1 = DB::connection("mygatensi")->table("myasesorbnsp")
                    ->select('Noreg')
                    ->where('Nama', $data[5])
                    ->first();

                if ($data[5] === 'PUJI WIDODO, S.H.') {
                    $verifikator1 = (object)['Noreg' => 'MET.000.009264 2023'];
                } else if ($data[5] === 'JUMRIL AMALYA') {
                    $verifikator1 = (object)['Noreg' => 'MET.000.002946 2024'];
                } else if ($data[5] === 'SOFIAN KAEFA') {
                    $verifikator1 = (object)['Noreg' => 'MET.000.002942 2024'];
                }

                // Formatting tanggal1
                $tanggal1 = \DateTime::createFromFormat('d/m/Y', $data[2]);
                $dayOfWeekEnglish1 = $tanggal1->format('l');
                $dayOfWeekIndonesian1 = $daysIndonesian[$dayOfWeekEnglish1];
                $day1 = $tanggal1->format('d');
                $month1 = $monthsIndonesian[$tanggal1->format('n')];
                $year1 = $tanggal1->format('Y');
                $formattedTanggal1 = "$dayOfWeekIndonesian1 / $day1 $month1 $year1";

                // Formatting tanggal2 (-2 day)
                $yesterday = (clone $tanggal1)->modify('-2 day');
                $day2 = $yesterday->format('d');
                $month2 = $monthsIndonesian[$yesterday->format('n')];
                $year2 = $yesterday->format('Y');
                $formattedTanggal2 = "Jakarta, $day2 $month2 $year2";

                // Formatting tanggal3 (-1 day)
                $currentDate = (clone $tanggal1)->modify('-1 day');
                $dayOfWeekEnglish3 = $currentDate->format('l');
                $dayOfWeekIndonesian3 = $daysIndonesian[$dayOfWeekEnglish3];
                $day3 = $currentDate->format('d');
                $month3 = $monthsIndonesian[$currentDate->format('n')];
                $year3 = $currentDate->format('Y');
                $formattedTanggal3 = "$dayOfWeekIndonesian3 / $day3 $month3 $year3";

                // Formatting tanggal4 (-1 day, formatted like tanggal2)
                $day4 = $currentDate->format('d');
                $month4 = $monthsIndonesian[$currentDate->format('n')];
                $year4 = $currentDate->format('Y');
                $formattedTanggal4 = "Jakarta, $day4 $month4 $year4";

                $monthRoman = $romanMonths[(int)$currentDate->format('n')];

                $maxAlamat1Length = 62;

                $alamatWords = explode(' ', $data[3]);
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

                $template = count($skemaArray) < 7 ? '/home/lspgatensi/new-balai/veriftuk/app/Http/Controllers/templatePdf/dokumenVerifikasi2.pdf' : '/home/lspgatensi/new-balai/veriftuk/app/Http/Controllers/templatePdf/dokumenVerifikasi1.pdf';
                $outputPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'DOKUMEN VERIFIKASI' . '.pdf';
                $pdf = new Pdf($template);
                $formFields = [
                    'no1' => $data[0] . '/LSP LPK GTK B.006-B/' . $monthRoman . '/' . (int)$tanggal1->format('Y'),
                    'no4' => $data[0] . '/LSP LPK GTK C.005-F/' . $monthRoman . '/' . (int)$currentDate->format('Y'),
                    'tanggal1' => $formattedTanggal1,
                    'tanggal2' => $formattedTanggal2,
                    'tanggal3' => $formattedTanggal3,
                    'tanggal4' => $formattedTanggal4,
                    'tuk' => $data[1],
                    'alamat1' => $alamat1,
                    'alamat2' => $alamat2,
                    'metode' => 'Observasi',
                    'peserta' => $data[4] . ' peserta',
                    'verifikator1' => "$data[5] ($verifikator1->Noreg)",
                    'verifikatorlist1' => $verifikator1 !== null ? "•  $data[5] ($verifikator1->Noreg)" : null,
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
                $fpdi->SetCreator('LSP Gatensi');
                $fpdi->SetAuthor('LSP Gatensi');
                
                // Load the existing PDF
                $pageCount = $fpdi->setSourceFile($tempFpdiPath);

                // Iterate through each page of the original PDF
                for ($i = 1; $i <= $pageCount; $i++) {
                    $templateId = $fpdi->importPage($i);
                    $fpdi->SetAutoPageBreak(false, 0);
                    $fpdi->SetMargins(0, 0, 0);
                    $fpdi->setPrintHeader(false);
                    $fpdi->addPage();
                    $fpdi->useTemplate($templateId);

                    if ($i === 1) {
                        $fpdi->SetFont('cambriab', 'B', 15.5);
                        $fpdi->SetXY(76, 81);
                        $fpdi->Write(0, $data[0] . '/LSP LPK GTK B.007-D/' . $monthRoman . '/' . (int)$currentDate->format('Y'));
                        if (count($skemaArray) < 7) {
                            DB::connection('reguler')->table('barcodes')->insert([
                                'nama' => 'Radinal Efendy, S.T.',
                                'id_izin' => '2220910001',
                                'jabatan' => 'Direktur LSP',
                                'url' => 'https://barcode.lspgatensi.id/' . $direktur_1,
                                'created_at' => $yesterday
                            ]);
                            $fpdi->write2DBarcode('https://barcode.lspgatensi.id/' . $direktur_1, 'QRCODE,H', 30, 240, 20, 20);
                        }
                    }
                    if ($i === 2 && count($skemaArray) < 7) {
                        $fpdi->SetFont('cambriab', 'B', 15.5);
                        $fpdi->SetXY(76, 84.4);
                        $fpdi->Write(0, $data[0] . '/LSP LPK GTK B.008-D/' . $monthRoman . '/' . date('Y'));
                    }
                    if ($i === 2 && count($skemaArray) >= 7) {
                        DB::connection('reguler')->table('barcodes')->insert([
                            'nama' => 'Radinal Efendy, S.T.',
                            'id_izin' => '2220910001',
                            'jabatan' => 'Direktur LSP',
                            'url' => 'https://barcode.lspgatensi.id/' . $direktur_1,
                            'created_at' => $yesterday
                        ]);
                        $fpdi->write2DBarcode('https://barcode.lspgatensi.id/' . $direktur_1, 'QRCODE,H', 30, 49, 20, 20);
                    }
                    if ($i === 3 && count($skemaArray) >= 7) {
                        $fpdi->SetFont('cambriab', 'B', 15.5);
                        $fpdi->SetXY(76, 84.4);
                        $fpdi->Write(0, $data[0] . '/LSP LPK GTK B.008-D/' . $monthRoman . '/' . date('Y'));
                    }
                    if ($i === 3 && count($skemaArray) < 7) {
                        DB::connection('reguler')->table('barcodes')->insert([
                            'nama' => $data[5],
                            'id_izin' => $verifikator1->Noreg,
                            'jabatan' => 'Verifikator',
                            'url' => 'https://barcode.lspgatensi.id/' . $barcodeVerifikator1,
                            'created_at' => $currentDate
                        ]);
                        $fpdi->write2DBarcode('https://barcode.lspgatensi.id/' . $barcodeVerifikator1, 'QRCODE,H', 156, 51, 20, 20);
                    }
                    if ($i === 4 && count($skemaArray) >= 7) {
                        DB::connection('reguler')->table('barcodes')->insert([
                            'nama' => $data[5],
                            'id_izin' => $verifikator1->Noreg,
                            'jabatan' => 'Verifikator',
                            'url' => 'https://barcode.lspgatensi.id/' . $barcodeVerifikator1,
                            'created_at' => $currentDate
                        ]);
                        $fpdi->write2DBarcode('https://barcode.lspgatensi.id/' . $barcodeVerifikator1, 'QRCODE,H', 155, 51, 20, 20);
                    }
                }

                $finalPdf = $fpdi->Output('', 'S');

                $tempFinalPdfPath = tempnam(sys_get_temp_dir(), 'final_') . '.pdf';
                file_put_contents($tempFinalPdfPath, $finalPdf);

                DB::connection('reguler')->table('barcodes')->insert([
                    'nama' => $data[6],
                    'id_izin' => '',
                    'jabatan' => 'Ketua TUK',
                    'url' => 'https://barcode.lspgatensi.id/' . $barcodeKetuaTUK,
                    'created_at' => $currentDate
                ]);
                DB::connection('reguler')->table('barcodes')->insert([
                    'nama' => $data[7],
                    'id_izin' => $verifikator1->Noreg,
                    'jabatan' => 'Auditor TUK',
                    'url' => 'https://barcode.lspgatensi.id/' . $barcodeValidator,
                    'created_at' => $currentDate
                ]);

                // Generate skema pdf
                $tempFinalSkemaPaths = [];
                foreach ($skemaArray as $index => $skema) {
                    $jabker = DB::connection('mygatensi')->table('myjabatankerja')->where('jabatan_kerja', $skema)->select(['jenjang_id', 'peralatan'])->first();
                    if (!isset($jabker)) {
                        dd($skema . ' tidak ditemukan di database');
                    }
                    if (!empty($skema)) {
                        if ($jabker->jenjang_id > 3) {
                            $templateskema = '/home/lspgatensi/new-balai/veriftuk/app/Http/Controllers/templatePdf/dokumenSkema-j4-9.pdf';
                            $isSesuai = "Yes"; 
                        } elseif ($jabker->peralatan === null) {
                            $templateskema = '/home/lspgatensi/new-balai/veriftuk/app/Http/Controllers/templatePdf/dokumenSkema-j1-3-noalat.pdf';
                            $isSesuai = "Yes";
                        } else {
                            $templateskema = '/home/lspgatensi/new-balai/veriftuk/app/Http/Controllers/templatePdf/dokumenSkema-j1-3.pdf';
                            $isSesuai = "Yes"; 
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
                        $metode = 'obs';

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
                            "tuk" => $data[1],
                            "alamat1" => $alamat1,
                            "alamat2" => $alamat2,
                            "jabker1" => ucwords($skema1),
                            "jabker2" => ucwords($skema2),
                            "jenjang" => $jabker->jenjang_id,
                            "tanggal" => $formattedTanggal4,
                            "ketua" => ucwords($data[6]),
                            "asesor" => ucwords($data[5]),
                            "manager" => ucwords($data[7]),
                            "observasi" => 'Yes',
                            "portofolio" => 'Off',
                            "{$metode}_gedung_ada" => 'Yes',
                            "{$metode}_parkir_ada" => 'Yes',
                            "{$metode}_bangunan_ada" => 'Yes',
                            "{$metode}_ruangan_ada" => 'Yes',
                            "{$metode}_1/2pk_ada" => 'Off',
                            "{$metode}_3/4pk_ada" => 'Off',
                            "{$metode}_1pk_ada" => 'Yes',
                            "{$metode}_1,5pk_ada" => 'Off',
                            "{$metode}_kipas_ada" => 'Off',
                            "{$metode}_internet_ada" => 'Yes',
                            "{$metode}_mejaasesor_ada" => 'Yes',
                            "{$metode}_mejaasesi_ada" => 'Yes',
                            "{$metode}_pc_ada" => 'Yes',
                            "{$metode}_kabel_ada" => 'Yes',
                            "{$metode}_komunikasi_ada" => 'Yes',
                            "{$metode}_dokumentasi_ada" => 'Yes',
                            "{$metode}_pulpen_ada" => 'Yes',
                            "{$metode}_pensil_ada" => 'Yes',
                            "{$metode}_tipex_ada" => 'Yes',
                            "{$metode}_penghapus_ada" => 'Yes',
                            "{$metode}_spidol_ada" => 'Yes',
                            "{$metode}_penggaris_ada" => 'Yes',
                            "{$metode}_hvs_ada" => 'Yes',
                            "{$metode}_p3k_ada" => 'Yes',
                            "{$metode}_apar_ada" => 'Yes',
                            "{$metode}_rambu_ada" => 'Yes',
                            "{$metode}_helm_ada" => 'Yes',
                            "{$metode}_sarung_ada" => 'Yes',
                            "{$metode}_sepatu_ada" => 'Yes',
                            "{$metode}_rompi_ada" => 'Yes',
                            "{$metode}_masker_ada" => 'Yes',
                            "{$metode}_telinga_ada" => 'Yes',
                            "{$metode}_harness_ada" => 'Yes',
                            "{$metode}_kacamata_ada" => 'Yes',
                            "{$metode}_gedung_tidakada" => 'Off',
                            "{$metode}_parkir_tidakada" => 'Off',
                            "{$metode}_bangunan_tidakada" => 'Off',
                            "{$metode}_ruangan_tidakada" => 'Off',
                            "{$metode}_1/2pk_tidakada" => 'Off',
                            "{$metode}_3/4pk_tidakada" => 'Off',
                            "{$metode}_1pk_tidakada" => 'Off',
                            "{$metode}_1,5pk_tidakada" => 'Off',
                            "{$metode}_kipas_tidakada" => 'Off',
                            "{$metode}_internet_tidakada" => 'Off',
                            "{$metode}_mejaasesor_tidakada" => 'Off',
                            "{$metode}_mejaasesi_tidakada" => 'Off',
                            "{$metode}_pc_tidakada" => 'Off',
                            "{$metode}_kabel_tidakada" => 'Off',
                            "{$metode}_komunikasi_tidakada" => 'Off',
                            "{$metode}_dokumentasi_tidakada" => 'Off',
                            "{$metode}_pulpen_tidakada" => 'Off',
                            "{$metode}_pensil_tidakada" => 'Off',
                            "{$metode}_tipex_tidakada" => 'Off',
                            "{$metode}_penghapus_tidakada" => 'Off',
                            "{$metode}_spidol_tidakada" => 'Off',
                            "{$metode}_penggaris_tidakada" => 'Off',
                            "{$metode}_hvs_tidakada" => 'Off',
                            "{$metode}_p3k_tidakada" => 'Off',
                            "{$metode}_apar_tidakada" => 'Off',
                            "{$metode}_rambu_tidakada" => 'Off',
                            "{$metode}_helm_tidakada" => 'Off',
                            "{$metode}_sarung_tidakada" => 'Off',
                            "{$metode}_sepatu_tidakada" => 'Off',
                            "{$metode}_rompi_tidakada" => 'Off',
                            "{$metode}_masker_tidakada" => 'Off',
                            "{$metode}_telinga_tidakada" => 'Off',
                            "{$metode}_harness_tidakada" => 'Off',
                            "{$metode}_kacamata_tidakada" => 'Off',
                            "{$metode}_gedung_sesuai" => 'Yes',
                            "{$metode}_parkir_sesuai" => 'Yes',
                            "{$metode}_bangunan_sesuai" => 'Yes',
                            "{$metode}_ruangan_sesuai" => 'Yes',
                            "{$metode}_1/2pk_sesuai" => 'Off',
                            "{$metode}_3/4pk_sesuai" => 'Off',
                            "{$metode}_1pk_sesuai" => 'Yes',
                            "{$metode}_1,5pk_sesuai" => 'Off',
                            "{$metode}_kipas_sesuai" => 'Off',
                            "{$metode}_internet_sesuai" => 'Yes',
                            "{$metode}_mejaasesor_sesuai" => 'Yes',
                            "{$metode}_mejaasesi_sesuai" => 'Yes',
                            "{$metode}_pc_sesuai" => 'Yes',
                            "{$metode}_kabel_sesuai" => 'Yes',
                            "{$metode}_komunikasi_sesuai" => 'Yes',
                            "{$metode}_dokumentasi_sesuai" => 'Yes',
                            "{$metode}_pulpen_sesuai" => 'Yes',
                            "{$metode}_pensil_sesuai" => 'Yes',
                            "{$metode}_tipex_sesuai" => 'Yes',
                            "{$metode}_penghapus_sesuai" => 'Yes',
                            "{$metode}_spidol_sesuai" => 'Yes',
                            "{$metode}_penggaris_sesuai" => 'Yes',
                            "{$metode}_hvs_sesuai" => 'Yes',
                            "{$metode}_p3k_sesuai" => 'Yes',
                            "{$metode}_apar_sesuai" => 'Yes',
                            "{$metode}_rambu_sesuai" => 'Yes',
                            "{$metode}_helm_sesuai" => 'Yes',
                            "{$metode}_sarung_sesuai" => 'Yes',
                            "{$metode}_sepatu_sesuai" => 'Yes',
                            "{$metode}_rompi_sesuai" => 'Yes',
                            "{$metode}_masker_sesuai" => 'Yes',
                            "{$metode}_telinga_sesuai" => 'Yes',
                            "{$metode}_harness_sesuai" => 'Yes',
                            "{$metode}_kacamata_sesuai" => 'Yes',
                            "{$metode}_gedung_tidaksesuai" => 'Off',
                            "{$metode}_parkir_tidaksesuai" => 'Off',
                            "{$metode}_bangunan_tidaksesuai" => 'Off',
                            "{$metode}_ruangan_tidaksesuai" => 'Off',
                            "{$metode}_internet_tidaksesuai" => 'Off',
                            "{$metode}_mejaasesor_tidaksesuai" => 'Off',
                            "{$metode}_mejaasesi_tidaksesuai" => 'Off',
                            "{$metode}_pc_tidaksesuai" => 'Off',
                            "{$metode}_kabel_tidaksesuai" => 'Off',
                            "{$metode}_komunikasi_tidaksesuai" => 'Off',
                            "{$metode}_dokumentasi_tidaksesuai" => 'Off',
                            "{$metode}_pulpen_tidaksesuai" => 'Off',
                            "{$metode}_pensil_tidaksesuai" => 'Off',
                            "{$metode}_tipex_tidaksesuai" => 'Off',
                            "{$metode}_penghapus_tidaksesuai" => 'Off',
                            "{$metode}_spidol_tidaksesuai" => 'Off',
                            "{$metode}_penggaris_tidaksesuai" => 'Off',
                            "{$metode}_hvs_tidaksesuai" => 'Off',
                            "{$metode}_p3k_tidaksesuai" => 'Off',
                            "{$metode}_apar_tidaksesuai" => 'Off',
                            "{$metode}_rambu_tidaksesuai" => 'Off',
                            "{$metode}_helm_tidaksesuai" => 'Off',
                            "{$metode}_sarung_tidaksesuai" => 'Off',
                            "{$metode}_sepatu_tidaksesuai" => 'Off',
                            "{$metode}_rompi_tidaksesuai" => 'Off',
                            "{$metode}_masker_tidaksesuai" => 'Off',
                            "{$metode}_telinga_tidaksesuai" => 'Off',
                            "{$metode}_harness_tidaksesuai" => 'Off',
                            "{$metode}_kacamata_tidaksesuai" => 'Off',
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
            
                                $formSkema["praktik{$key}_ada"] = 'Yes';
                                $formSkema["praktik{$key}_tidakada"] = 'Off';
                                $formSkema["praktik{$key}_sesuai"] = 'Yes';
                                $formSkema["praktik{$key}_tidaksesuai"] = 'Off';
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
                        $fpdiSkema->SetCreator('LSP Gatensi');
                        $fpdiSkema->SetAuthor('LSP Gatensi');
                        
                        // Load the existing PDF
                        $pageCount = $fpdiSkema->setSourceFile($tempFpdiSkema);
                        for ($i = 1; $i <= $pageCount; $i++) {
                            $templateId = $fpdiSkema->importPage($i);
                            $fpdiSkema->SetAutoPageBreak(false, 0);
                            $fpdiSkema->SetMargins(0, 0, 0);
                            $fpdiSkema->setPrintHeader(false);
                            $fpdiSkema->addPage();
                            $fpdiSkema->useTemplate($templateId);

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

                                $fpdiSkema->write2DBarcode('https://barcode.lspgatensi.id/' . $barcodeKetuaTUK, 'QRCODE,H', 35, 200, 19, 19);
                                $fpdiSkema->write2DBarcode('https://barcode.lspgatensi.id/' . $barcodeVerifikator1, 'QRCODE,H', 97, 200, 19, 19);
                                $fpdiSkema->write2DBarcode('https://barcode.lspgatensi.id/' . $barcodeValidator, 'QRCODE,H', 161, 200, 19, 19);
                            }
                            if ($i === 9 && $jabker->jenjang_id < 4) {
                                $fpdiSkema->SetFont('arialnarrow_b', 'B', 10);
                                $fpdiSkema->SetXY(73, 91.5);
                                if ($jabker->peralatan === null) {
                                    $fpdiSkema->Write(0, $isSesuai === 'Yes' ? 'Sesuai' : 'Tidak Sesuai');
                                } else {
                                    $fpdiSkema->Write(0, $isSesuai === 'Yes' && $alatSesuai === 'Yes' ? 'Sesuai' : 'Tidak Sesuai');
                                }

                                $fpdiSkema->write2DBarcode('https://barcode.lspgatensi.id/' . $barcodeKetuaTUK, 'QRCODE,H', 35, 200, 19, 19);
                                $fpdiSkema->write2DBarcode('https://barcode.lspgatensi.id/' . $barcodeVerifikator1, 'QRCODE,H', 97, 200, 19, 19);
                                $fpdiSkema->write2DBarcode('https://barcode.lspgatensi.id/' . $barcodeValidator, 'QRCODE,H', 161, 200, 19, 19);
                            }
                        }

                        $finalPdfSkema = $fpdiSkema->Output('', 'S');
                        $tempFinalPdfSkema = tempnam(sys_get_temp_dir(), 'final_') . '.pdf';
                        file_put_contents($tempFinalPdfSkema, $finalPdfSkema);

                        $tempFinalSkemaPaths[] = $tempFinalPdfSkema;
                    }
                }

                $templateSK = '/home/lspgatensi/new-balai/veriftuk/app/Http/Controllers/templatePdf/suratKeputusan.pdf';
                $outputSkPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'Surat Keputusan' . '.pdf';
                $pdfSk = new Pdf($templateSK);
                $formFieldSK = [
                    'no4' => $data[0] . '/LSP LPK GTK C.005-F/' . $monthRoman . '/' . date('Y'),
                    'tanggal4' => $formattedTanggal4,
                    'memutuskan' => $data[1] . ',',
                    'ketuaTuk' => $data[6] . ' sebagai Penanggungjawab ' . $data[1]
                ];

                $pdfSk->fillForm($formFieldSK)->flatten()->saveAs($outputSkPath);

                $skContents = file_get_contents($outputSkPath);
                
                if (!$skContents) {
                    throw new \Exception("Failed to fetch the PDF from the URL");
                }

                // Store the file in a temporary location
                $tempSkPath = tempnam(sys_get_temp_dir(), 'pdf');
                file_put_contents($tempSkPath, $skContents);

                // Initialize FPDI with TCPDF
                $fpdiSk = new Fpdi();

                // Set document information (Optional)
                $fpdiSk->SetCreator('LSP LPK Gataksindo');
                $fpdiSk->SetAuthor('LSP LPK Gataksindo');
                
                // Load the existing PDF
                $pageCountSk = $fpdiSk->setSourceFile($tempSkPath);

                // Iterate through each page of the original PDF
                for ($i = 1; $i <= $pageCountSk; $i++) {
                    $templateId = $fpdiSk->importPage($i);
                    $fpdiSk->SetAutoPageBreak(false, 0);
                    $fpdiSk->SetMargins(0, 0, 0);
                    $fpdiSk->setPrintHeader(false);
                    $fpdiSk->addPage();
                    $fpdiSk->useTemplate($templateId);

                    if ($i === 1) {
                        DB::connection('reguler')->table('barcodes')->insert([
                            'nama' => 'Radinal Efendy, S.T.',
                            'id_izin' => '2220910001',
                            'jabatan' => 'Direktur LSP',
                            'url' => 'https://barcode.lspgatensi.id/' . $direktur_2,
                            'created_at' => $currentDate
                        ]);
                        $fpdiSk->write2DBarcode('https://barcode.lspgatensi.id/' . $direktur_2, 'QRCODE,H', 30, 240, 20, 20);
                    }
                }

                $finalSk = $fpdiSk->Output('', 'S');

                $tempFinalSkPath = tempnam(sys_get_temp_dir(), 'final_') . '.pdf';
                file_put_contents($tempFinalSkPath, $finalSk);

                $mergedPdf = new Fpdi();
                $mergedPdf->SetCreator(creator: 'LSP LPK Gataksindo');
                $mergedPdf->SetAuthor('LSP LPK Gataksindo');

                $pageCountMain = $mergedPdf->setSourceFile($tempFinalPdfPath);
                for ($i = 1; $i <= $pageCountMain; $i++) {
                    $templateId = $mergedPdf->importPage($i);
                    $mergedPdf->SetAutoPageBreak(false, 0);
                    $mergedPdf->SetMargins(0, 0, 0);
                    $mergedPdf->setPrintHeader(false);
                    $mergedPdf->addPage();
                    $mergedPdf->useTemplate($templateId);
                }

                foreach ($tempFinalSkemaPaths as $skemaPath) {
                    $pageCountSkema = $mergedPdf->setSourceFile($skemaPath);

                    for ($i = 1; $i <= $pageCountSkema; $i++) {
                        $templateId = $mergedPdf->importPage($i);
                        $mergedPdf->SetAutoPageBreak(false, 0);
                        $mergedPdf->SetMargins(0, 0, 0);
                        $mergedPdf->setPrintHeader(false);
                        $mergedPdf->addPage();
                        $mergedPdf->useTemplate($templateId);
                    }
                }

                $pageCountSk = $mergedPdf->setSourceFile($tempFinalSkPath);
                for ($i = 1; $i <= $pageCountSk; $i++) {
                    $templateId = $mergedPdf->importPage($i);
                    $mergedPdf->SetAutoPageBreak(false, 0);
                    $mergedPdf->SetMargins(0, 0, 0);
                    $mergedPdf->setPrintHeader(false);
                    $mergedPdf->addPage();
                    $mergedPdf->useTemplate($templateId);
                }

                // Output the merged file (you can also save it to disk or FTP if needed)
                $mergedOutput = $mergedPdf->Output('', 'S');

                $mergedFileName = 'DOKUMEN VERIFIKASI DAN SKEMA ' . strtoupper($data[1]) . '.pdf';
                $result_save = Storage::disk("public")->put("tuk/" . $currentDate->format('Y-m-d') . "/" . strtoupper($data[1]) . "/$mergedFileName", $mergedOutput);
                $link = Storage::disk('public')->url('tuk/' . $currentDate->format('Y-m-d') . "/" . strtoupper($data[1]) . "/$mergedFileName");

                if (!$result_save) {
                    return back()->with('error', 'File not found');
                }

                array_push($all_files, [
                    'id_jadwal' => $data[9],
                    'link' => $link
                ]);

                Verification::create([
                    'no_surat' => $data[0],
                    'tuk' => $data[1],
                    'link' => $link,
                    'verificator' => $data[5],
                    'created_at' => $yesterday
                ]);

                unlink($outputPath);
                unlink($tempFpdiPath);
                unlink($outputSkPath);
                unlink($tempSkPath);
                unlink($tempFinalPdfPath);
                foreach ($tempFinalSkemaPaths as $tempFinalSkemaPath) {
                    unlink($tempFinalSkemaPath);
                }
            }
        } catch (\Exception $e) {
            dd($e);
            return redirect('/asesor-lsp');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header columns
        $headers = [
            'No', 'ID Jadwal', 'File Verifikasi TUK'
        ];

        $sheet->fromArray($headers, NULL, 'A1');

        // Populate rows with data
        $row = 2;
        foreach ($all_files as $index => $file) {
            $sheet->fromArray([
                $index + 1,
                $file['id_jadwal'],
                $file['link'],
            ], NULL, 'A' . $row);
            $row++;
        }

        // Download as Excel file
        $filename = "File Verifikasi TUK.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function revisiTUK()
    {
        $all_files = [];
        $open = fopen(storage_path("app/revisi.csv"), "r");
        try {
            while (($data = fgetcsv($open, 1000, ";")) !== false) {
                $jadwalArray = explode(', ', $data[0]);
                $jadwals = DB::connection('reguler')->table('jadwal_bnsp_table')->select('id_izin')->whereIn('jadwal_id', $jadwalArray)->get();
                if ($jadwals->isEmpty()) {
                    $jadwals = DB::connection('balai')->table('jadwal_bnsp_table')->select('id_izin')->whereIn('jadwal_id', $jadwalArray)->get();
                }
                if ($jadwals->isEmpty()) {
                    $jadwals = DB::connection('fg')->table('jadwal_bnsp_table')->select('id_izin')->whereIn('jadwal_id', $jadwalArray)->get();
                }
                if ($jadwals->isem()) {
                    foreach ($jadwals as $jadwal) {
                        array_push($all_files, [
                            'id_izin' => $jadwal->id_izin,
                            'link' => $data[1]
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            dd($e);
            return redirect('/asesor-lsp');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header columns
        $headers = [
            'No', 'ID Izin', 'File Verifikasi TUK'
        ];

        $sheet->fromArray($headers, NULL, 'A1');

        // Populate rows with data
        $row = 2;
        foreach ($all_files as $index => $file) {
            $sheet->fromArray([
                $index + 1,
                $file['id_izin'],
                $file['link'],
            ], NULL, 'A' . $row);
            $row++;
        }

        // Download as Excel file
        $filename = "File Verifikasi TUK.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
