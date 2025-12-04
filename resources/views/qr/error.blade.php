<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>QR Error - Verifikasi TUK - LSP LPK Gataksindo</title>
    @vite('resources/css/app.css')
</head>
<body>
    <main class="w-full h-screen flex justify-center items-center bg-gray-900">
        <div class="w-full max-w-2xl mx-4">
            <div class="bg-red-600 rounded-xl p-8 text-center">
                <div class="mb-6">
                    <i class="fas fa-exclamation-triangle text-6xl text-white"></i>
                </div>

                <h1 class="text-3xl font-bold text-white mb-4">QR Code Tidak Valid</h1>

                <div class="bg-red-700 rounded-lg p-4 mb-6">
                    <p class="text-white text-lg">{{ $error }}</p>
                </div>

                <p class="text-red-100 mb-8">
                    QR Code yang Anda scan tidak valid atau sudah kadaluarsa.<br>
                    Silakan periksa kembali QR Code atau hubungi administrator.
                </p>

                <div class="flex justify-center gap-4">
                    <a href="{{ url('/') }}" class="bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-lg transition duration-300">
                        <i class="fas fa-home mr-2"></i> Kembali ke Beranda
                    </a>
                    <a href="{{ url('/qr-verify') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg transition duration-300">
                        <i class="fas fa-qrcode mr-2"></i> Verifikasi Manual
                    </a>
                </div>
            </div>

            <div class="bg-gray-800 rounded-xl p-6 mt-4">
                <h3 class="text-white font-bold mb-3">
                    <i class="fas fa-info-circle text-blue-400 mr-2"></i>
                    Informasi Bantuan:
                </h3>
                <ul class="text-gray-300 space-y-2">
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-400 mr-2 mt-1"></i>
                        <span>Pastikan QR Code masih dalam masa berlaku</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-400 mr-2 mt-1"></i>
                        <span>QR Code harus dari dokumen resmi LSP Gataksindo</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-400 mr-2 mt-1"></i>
                        <span>Hubungi administrator jika Anda membutuhkan bantuan lebih lanjut</span>
                    </li>
                </ul>
            </div>
        </div>
    </main>
</body>
</html>