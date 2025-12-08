<?php

require_once 'vendor/autoload.php';

// Import TCPDF
use TCPDF;

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('Test PDF');
$pdf->SetAuthor('Test');
$pdf->SetTitle('TCPDF Test');

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('times', '', 12);

// Test HTML content
$html = '<h1 style="text-align:center;">Test PDF Generation</h1>';
$html .= '<p>This is a test to verify TCPDF is working correctly.</p>';
$html .= '<table style="width:100%; border:1px solid #000;">';
$html .= '<tr><td style="border:1px solid #000;"><strong>Nomor:</strong></td><td style="border:1px solid #000;">001/TEST/2024</td></tr>';
$html .= '<tr><td style="border:1px solid #000;"><strong>Tanggal:</strong></td><td style="border:1px solid #000;">' . date('d F Y') . '</td></tr>';
$html .= '<tr><td style="border:1px solid #000;"><strong>Status:</strong></td><td style="border:1px solid #000;">Sukses!</td></tr>';
$html .= '</table>';

// Write the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// Save the PDF
$filePath = 'test_tcpdf.pdf';
$pdf->Output($filePath, 'F');

echo "PDF test berhasil! File disimpan: $filePath\n";
echo "Ukuran file: " . filesize($filePath) . " bytes\n";