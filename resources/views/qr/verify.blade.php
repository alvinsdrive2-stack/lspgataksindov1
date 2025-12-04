<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Verifikasi QR - LSP LPK Gataksindo</title>
    @vite('resources/css/app.css')
</head>
<body>
    <main class="w-full h-screen flex justify-center items-center bg-gray-900">
        <div class="w-full max-w-2xl mx-4">
            <div class="bg-gray-800 rounded-xl p-8">
                <div class="text-center mb-8">
                    <i class="fas fa-qrcode text-6xl text-blue-400 mb-4"></i>
                    <h1 class="text-3xl font-bold text-white mb-2">Verifikasi QR Code Manual</h1>
                    <p class="text-gray-300">Masukkan UUID QR Code untuk verifikasi</p>
                </div>

                <form id="qr-verify-form" class="mb-8">
                    @csrf
                    <div class="mb-6">
                        <label for="uuid" class="block text-white font-bold mb-3">
                            <i class="fas fa-keyboard mr-2"></i>
                            Masukkan UUID QR Code:
                        </label>
                        <div class="flex gap-3">
                            <input type="text"
                                   class="flex-1 px-4 py-3 bg-gray-700 text-white rounded-lg border-2 border-gray-600 focus:border-blue-400 focus:outline-none"
                                   id="uuid"
                                   name="uuid"
                                   placeholder="Contoh: 123e4567-e89b-12d3-a456-426614174000"
                                   required
                                   pattern="[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"
                                   title="Format UUID: xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-lg transition duration-300">
                                <i class="fas fa-search mr-2"></i> Verifikasi
                            </button>
                        </div>
                        <p class="text-gray-400 text-sm mt-2">
                            <i class="fas fa-info-circle mr-1"></i>
                            Masukkan 32 karakter UUID dari QR Code
                        </p>
                    </div>
                </form>

                <div id="qr-result" class="hidden">
                    <!-- Result will be loaded here -->
                </div>
            </div>

            <div class="bg-gray-800 rounded-xl p-6 mt-4">
                <h3 class="text-white font-bold mb-4">
                    <i class="fas fa-question-circle text-green-400 mr-2"></i>
                    Cara Penggunaan:
                </h3>
                <ol class="text-gray-300 space-y-3">
                    <li class="flex items-start">
                        <span class="bg-blue-500 text-white rounded-full w-6 h-6 flex items-center justify-center mr-3 mt-0.5 flex-shrink-0">1</span>
                        <span>Scan QR Code menggunakan aplikasi scanner (QR Scanner, WhatsApp, dll)</span>
                    </li>
                    <li class="flex items-start">
                        <span class="bg-blue-500 text-white rounded-full w-6 h-6 flex items-center justify-center mr-3 mt-0.5 flex-shrink-0">2</span>
                        <span>Salin UUID yang muncul (format: xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx)</span>
                    </li>
                    <li class="flex items-start">
                        <span class="bg-blue-500 text-white rounded-full w-6 h-6 flex items-center justify-center mr-3 mt-0.5 flex-shrink-0">3</span>
                        <span>Paste UUID ke dalam form di atas</span>
                    </li>
                    <li class="flex items-start">
                        <span class="bg-blue-500 text-white rounded-full w-6 h-6 flex items-center justify-center mr-3 mt-0.5 flex-shrink-0">4</span>
                        <span>Klik tombol "Verifikasi" untuk melihat hasil</span>
                    </li>
                </ol>
            </div>

            <div class="text-center mt-6">
                <a href="{{ url('/') }}" class="bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-lg transition duration-300">
                    <i class="fas fa-home mr-2"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    </main>

    <!-- jQuery and AJAX -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#qr-verify-form').on('submit', function(e) {
            e.preventDefault();

            var uuid = $('#uuid').val();
            var resultDiv = $('#qr-result');

            // Show loading
            resultDiv.html(`
                <div class="text-center py-6">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-400"></div>
                    <p class="text-gray-300 mt-3">Memverifikasi QR Code...</p>
                </div>
            `);
            resultDiv.removeClass('hidden');

            // Make AJAX request
            $.ajax({
                url: '{{ url("/qr-verify") }}',
                method: 'POST',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'uuid': uuid
                },
                success: function(response) {
                    if (response.success) {
                        // Success response
                        var data = response.data;
                        var html = `
                            <div class="bg-green-600 rounded-lg p-6">
                                <h3 class="text-white font-bold mb-4">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    QR Code Valid!
                                </h3>
                                <div class="space-y-3">
                                    <div class="flex justify-between text-green-100">
                                        <span>UUID:</span>
                                        <span class="font-mono text-sm">${data.qr.uuid}</span>
                                    </div>
                                    <div class="flex justify-between text-green-100">
                                        <span>Tipe:</span>
                                        <span class="bg-blue-500 text-white px-2 py-1 rounded text-sm">${data.qr.type}</span>
                                    </div>
                                    <div class="flex justify-between text-green-100">
                                        <span>TUK:</span>
                                        <span>${data.verification.tuk}</span>
                                    </div>
                                    <div class="flex justify-between text-green-100">
                                        <span>Verifikator:</span>
                                        <span>${data.verification.verificator}</span>
                                    </div>
                                    <div class="flex justify-between text-green-100">
                                        <span>Di Scan:</span>
                                        <span>${data.qr.scanned_at}</span>
                                    </div>
                                </div>
                            </div>
                        `;
                        resultDiv.html(html);
                    } else {
                        // Error response
                        resultDiv.html(`
                            <div class="bg-red-600 rounded-lg p-6">
                                <h3 class="text-white font-bold mb-2">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    QR Code Tidak Valid!
                                </h3>
                                <p class="text-red-100">${response.error}</p>
                                <p class="text-red-200 text-sm mt-2">Kode: ${response.code}</p>
                            </div>
                        `);
                    }
                },
                error: function(xhr) {
                    // AJAX error
                    var errorMessage = 'Terjadi kesalahan saat memverifikasi QR Code';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMessage = xhr.responseJSON.error;
                    }

                    resultDiv.html(`
                        <div class="bg-red-600 rounded-lg p-6">
                            <h3 class="text-white font-bold mb-2">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Error!
                            </h3>
                            <p class="text-red-100">${errorMessage}</p>
                        </div>
                    `);
                }
            });
        });
    });
    </script>
</body>
</html>