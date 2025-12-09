@extends('layouts.dashboard-dark')

@section('title', 'Penugasan Verifikator TUK Sewaktu')

@section('pageTitle', 'Penugasan Verifikator TUK - Admin LSP')

@section('content')
    <!-- Success Message -->
    @if (session('success'))
        <div class="glass rounded-xl p-6 mb-8 animate-slideDown">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-green-800 font-medium">{!! session('success') !!}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Form Section -->
    <div class="w-full">
        <!-- Form Section -->
        <div class="glass-dark rounded-2xl shadow-xl p-8 animate-fade-in">
                <!-- Form Header -->
                <div class="mb-8 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-[#1F3A73] to-[#3F5FA8] rounded-2xl mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-white mb-2">Form Verifikasi TUK</h2>
                    <p class="text-gray-300">Lengkapi data verifikasi untuk TUK Sewaktu</p>
                </div>

                <form action="{{ route('createFileSewaktu') }}" method="POST" class="space-y-6 sewaktu-form">
                    @csrf

                    <!-- Informasi Utama Section -->
                    <div class="card-dark rounded-xl p-6 border border-gray-700">
                        <h3 class="text-lg font-semibold text-primary-dark mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Informasi Utama
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label for="nomor" class="block text-sm font-medium text-gray-300 mb-2">
                                    Nomor Surat
                                </label>
                                <input type="number" id="nomor" name="nomor" required
                                       class="w-full px-4 py-3 form-input-dark rounded-lg transition-all duration-200 placeholder-gray-400"
                                       placeholder="Masukkan nomor surat">
                            </div>

                            <div>
                                <label for="tuk" class="block text-sm font-medium text-gray-200 mb-2">
                                    Nama TUK
                                </label>
                                <input type="text" id="tuk" name="tuk" required
                                       class="w-full px-4 py-3 form-input-dark rounded-lg transition-all duration-200 placeholder-gray-400"
                                       placeholder="Masukkan nama TUK">
                            </div>

                            <div>
                                <label for="jenisTUK" class="block text-sm font-medium text-gray-200 mb-2">
                                    Jenis TUK
                                </label>
                                <select id="jenisTUK" name="jenisTUK" required
                                        class="w-full px-4 py-3 form-select-dark rounded-lg transition-all duration-200">
                                    <option value="">Pilih jenis TUK</option>
                                    <option value="Sewaktu">TUK Sewaktu</option>
                                    <option value="Mandiri">TUK Mandiri</option>
                                </select>
                            </div>

                            <div>
                                <label for="metodeVerif" class="block text-sm font-medium text-gray-200 mb-2">
                                    Metode Verifikasi
                                </label>
                                <select id="metodeVerif" name="metodeVerif" required
                                        class="w-full px-4 py-3 form-select-dark rounded-lg transition-all duration-200">
                                    <option value="">Pilih metode verifikasi</option>
                                    <option value="Luring">Luring</option>
                                    <option value="Daring">Daring</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Jadwal Section -->
                    <div class="card-dark rounded-xl p-6 border border-gray-700">
                        <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Jadwal & Lokasi
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label for="tanggal_asesmen" class="block text-sm font-medium text-gray-200 mb-2">
                                    Tanggal Asesmen
                                </label>
                                <div class="relative">
                                    <input type="text" id="tanggal_asesmen" name="tanggal_asesmen" required
                                           class="w-full px-4 py-3 pl-12 form-input-dark rounded-lg transition-all duration-200 cursor-pointer"
                                           placeholder="Pilih tanggal asesmen" readonly>
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="tanggal_verifikasi" class="block text-sm font-medium text-gray-200 mb-2">
                                    Tanggal Verifikasi
                                </label>
                                <div class="relative">
                                    <input type="text" id="tanggal_verifikasi" name="tanggal_verifikasi" required
                                           class="w-full px-4 py-3 pl-12 form-input-dark rounded-lg transition-all duration-200 cursor-pointer"
                                           placeholder="Pilih tanggal verifikasi" readonly>
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div class="lg:col-span-2">
                                <label for="alamat" class="block text-sm font-medium text-gray-200 mb-2">
                                    Alamat
                                </label>
                                <input type="text" id="alamat" name="alamat" required
                                       class="w-full px-4 py-3 form-input-dark rounded-lg transition-all duration-200 placeholder-gray-400"
                                       placeholder="Masukkan alamat lengkap">
                            </div>

                            <div>
                                <label for="peserta" class="block text-sm font-medium text-gray-200 mb-2">
                                    Jumlah Peserta
                                </label>
                                <input type="number" id="peserta" name="peserta" required
                                       class="w-full px-4 py-3 form-input-dark rounded-lg transition-all duration-200 placeholder-gray-400"
                                       placeholder="Masukkan jumlah peserta">
                            </div>
                        </div>
                    </div>

                    <!-- Personil Section -->
                    <div class="card-dark rounded-xl p-6 border border-gray-700">
                        <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            Personil Terkait
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label for="met1" class="block text-sm font-medium text-gray-200 mb-2">
                                    Validator <span class="text-gray-400 font-normal">(Opsional)</span>
                                </label>
                                <input list="asesor" type="text" id="met1" name="met1"
                                       class="w-full px-4 py-3 form-input-dark rounded-lg transition-all duration-200 placeholder-gray-400"
                                       placeholder="Cari validator...">
                                <datalist id="asesor">
                                    @foreach ($allAsesor as $asesor)
                                        <option value="{{ $asesor->Noreg }}">{{ $asesor->Nama }}</option>
                                    @endforeach
                                </datalist>
                            </div>

                            <div>
                                <label for="ketua" class="block text-sm font-medium text-gray-200 mb-2">
                                    Ketua TUK
                                </label>
                                <input list="ketua_tuk" type="text" id="ketua" name="ketua_tuk" required
                                       class="w-full px-4 py-3 border border-gray-700 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 text-gray-900 placeholder-gray-500"
                                       placeholder="Cari ketua TUK...">
                                <datalist id="ketua_tuk">
                                    @if($ketuaTukList->count() > 0)
                                        @foreach ($ketuaTukList as $ketua)
                                            <option value="{{ $ketua->name }}">{{ $ketua->nama_tuk }}</option>
                                        @endforeach
                                    @endif
                                </datalist>
                            </div>

                            <div>
                                <label for="asesor" class="block text-sm font-medium text-gray-200 mb-2">
                                    Verifikator
                                </label>
                                <input list="asesor" type="text" id="asesor" name="asesor" required
                                       class="w-full px-4 py-3 border border-gray-700 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 text-gray-900 placeholder-gray-500"
                                       placeholder="Cari verifikator...">
                            </div>

                            <div>
                                <label for="admin" class="block text-sm font-medium text-gray-200 mb-2">
                                    Admin TUK
                                </label>
                                <input type="text" id="admin" name="admin" required
                                       class="w-full px-4 py-3 border border-gray-700 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 text-gray-900 placeholder-gray-500"
                                       placeholder="Masukkan nama admin TUK">
                            </div>
                        </div>
                    </div>

                    <!-- Skema Section -->
                    <div class="card-dark rounded-xl p-6 border border-gray-700">
                        <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Skema Sertifikasi
                        </h3>

                        <div id="skemaContainer" class="space-y-4">
                            <!-- First Skema Group -->
                            <div class="skema-group bg-white rounded-lg p-4 border border-gray-700">
                                <h4 class="font-medium text-gray-200 mb-3">Skema #1</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <div>
                                        <label for="skema1" class="block text-sm font-medium text-gray-200 mb-2">
                                            Skema Sertifikasi
                                        </label>
                                        <input list="jabker" type="text" id="skema1" name="skema[]" required
                                               class="w-full px-3 py-2 border border-gray-700 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all duration-200 text-sm text-gray-900 placeholder-gray-500"
                                               placeholder="Pilih skema">
                                        <datalist id="jabker">
                                            @foreach ($allJabker as $jabker)
                                                <option value="{{ $jabker->jabatan_kerja }}">{{ $jabker->id_jabatan_kerja }}</option>
                                            @endforeach
                                        </datalist>
                                    </div>

                                    <div>
                                        <label for="jenjang1" class="block text-sm font-medium text-gray-200 mb-2">
                                            Jenjang
                                        </label>
                                        <input type="number" id="jenjang1" name="jenjang[]" required
                                               class="w-full px-3 py-2 border border-gray-700 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all duration-200 text-sm text-gray-900 placeholder-gray-500"
                                               placeholder="Masukkan jenjang">
                                    </div>

                                    <div>
                                        <label for="metode1" class="block text-sm font-medium text-gray-200 mb-2">
                                            Metode Asesmen
                                        </label>
                                        <select id="metode1" name="metode[]" required
                                                class="w-full px-3 py-2 border border-gray-700 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all duration-200 text-sm text-gray-900 placeholder-gray-500">
                                            <option value="">Pilih metode</option>
                                            <option value="Observasi">Observasi</option>
                                            <option value="Portofolio">Portofolio</option>
                                            <option value="Observasi & Portofolio">Observasi & Portofolio</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="button" id="addSkemaBtn"
                                class="mt-4 flex items-center space-x-2 px-4 py-2 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            <span>Tambah Skema</span>
                        </button>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-center">
                        <button type="submit"
                                class="btn-primary-dark flex items-center space-x-3 px-8 py-3 font-semibold rounded-lg shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Simpan Data Verifikasi</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    <!-- PDF Modal -->
    <div id="pdfModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="glass-dark rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
                <div class="flex items-center justify-between p-4 border-b border-gray-700">
                    <h3 class="text-lg font-semibold text-white">Preview Dokumen</h3>
                    <button onclick="closeModal()" class="p-2 hover:bg-gray-700 rounded-lg transition-colors text-gray-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="p-4">
                    <iframe id="pdfViewer" src="" width="100%" height="600px" class="border rounded-lg"></iframe>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Flatpickr CSS for modern datepicker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <style>
        /* Dark mode form overrides */
        .sewaktu-form input::placeholder {
            color: rgba(255, 255, 255, 0.5) !important;
        }

        /* Force white text for all form inputs */
        .sewaktu-form input,
        .sewaktu-form textarea,
        .sewaktu-form select {
            color: #ffffff !important;
            background-color: rgba(31, 58, 115, 0.3) !important;
        }

        /* White text when focused */
        .sewaktu-form input:focus,
        .sewaktu-form textarea:focus,
        .sewaktu-form select:focus {
            color: #ffffff !important;
            background-color: rgba(31, 58, 115, 0.5) !important;
        }

        /* Also ensure text is white when field has value */
        .sewaktu-form input:not(:placeholder-shown),
        .sewaktu-form textarea:not(:placeholder-shown) {
            color: #ffffff !important;
        }

        /* Override text-gray-900 class specifically */
        .text-gray-900 {
            color: #ffffff !important;
        }

        /* For input with datalist */
        input[list] {
            color: #ffffff !important;
        }

        input[list]:focus {
            color: #ffffff !important;
        }
        input:not([type="checkbox"]):not([type="radio"]):not([type="file"]):focus {
    color: #ffffff !important;
}

        .flatpickr-calendar {
            background: #1F3A73 !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
        }

        .flatpickr-day {
            color: #e5e7eb !important;
        }

        .flatpickr-day.selected {
            background: #3F5FA8 !important;
            border-color: #3F5FA8 !important;
        }

  
        .sewaktu-form select option {
            color: #000000 !important;
            background-color: #ffffff !important;
        }

        /* Remove any remaining forced white backgrounds */
        .sewaktu-form * {
            background-color: initial !important;
        }

        .sewaktu-form input,
        .sewaktu-form select,
        .sewaktu-form textarea {
            background-color: transparent !important;
        }

        
        /* Custom Flatpickr styling to match dark theme */
        .flatpickr-calendar {
            border-radius: 12px;
            box-shadow: 0 20px 25px -5px rgba(31, 58, 115, 0.3), 0 10px 10px -5px rgba(31, 58, 115, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: #1F3A73;
        }

        .flatpickr-months {
            background: rgb(31, 58, 115);
            border-radius: 12px 12px 0 0;
        }

        .flatpickr-month {
            background: transparent;
            color: white;
            fill: white;
        }

        .flatpickr-current-month {
            color: white !important;
            background: transparent;
        }

        .flatpickr-current-month .numInputWrapper {
            background: transparent;
            color: white !important;
        }

        .flatpickr-current-month .cur-year {
            color: white !important;
            font-weight: 600;
            background: transparent !important;
        }

        .flatpickr-current-month .cur-month {
            color: white !important;
            font-weight: 600;
            background: transparent !important;
        }

        .flatpickr-current-month input {
            color: white !important;
            background: transparent !important;
        }

        .flatpickr-current-month .numInputWrapper span.arrowUp:after,
        .flatpickr-current-month .numInputWrapper span.arrowDown:after {
            border-color: white !important;
        }

        .flatpickr-weekday {
            color: #6b7280;
            font-weight: 600;
        }

        .flatpickr-day {
            color: #374151;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .flatpickr-day:hover {
            background: rgb(116, 155, 240);
            color: white;
            border-color: rgb(255, 255, 255);
        }

        .flatpickr-day.selected {
            background: rgb(56, 91, 166);
            color: white;
            border-color: rgb(31, 58, 115);
        }

        .flatpickr-day.today {
            border-color: rgb(31, 58, 115);
        }

        .flatpickr-day.startRange,
        .flatpickr-day.endRange {
            background: rgb(31, 58, 115);
            color: white;
        }

        .flatpickr-months .flatpickr-prev-month,
        .flatpickr-months .flatpickr-next-month {
            color: white;
            fill: white;
        }

        .flatpickr-months .flatpickr-prev-month:hover,
        .flatpickr-months .flatpickr-next-month:hover {
            color: #f3f4f6;
            fill: #f3f4f6;
        }
    </style>

    <script>
        let skemaCount = 1;

        // Force white text on all input fields
        document.addEventListener('DOMContentLoaded', function() {
            // Apply to all input, textarea, and select elements
            const inputs = document.querySelectorAll('input, textarea, select');
            inputs.forEach(function(input) {
                // Add event listeners
                input.addEventListener('input', function() {
                    this.style.color = '#ffffff !important';
                });

                input.addEventListener('focus', function() {
                    this.style.color = '#ffffff !important';
                    this.style.backgroundColor = 'rgba(31, 58, 115, 0.5)';
                });

                input.addEventListener('blur', function() {
                    if (this.value) {
                        this.style.color = '#ffffff !important';
                    }
                });

                // Apply initial styles
                input.style.color = '#ffffff';
                input.style.caretColor = '#ffffff'; // Cursor color
            });
        });

        // Add new skema fields
        document.getElementById('addSkemaBtn').addEventListener('click', function() {
            skemaCount++;
            const container = document.getElementById('skemaContainer');

            const newField = document.createElement('div');
            newField.className = 'skema-group bg-white rounded-lg p-4 border border-gray-700 animate-fade-in';
            newField.innerHTML = `
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-medium text-gray-900">Skema #${skemaCount}</h4>
                    <button type="button" onclick="removeSkema(this)" class="text-red-500 hover:text-red-700 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label for="skema${skemaCount}" class="block text-sm font-medium text-gray-200 mb-2">
                            Skema Sertifikasi
                        </label>
                        <input list="jabker" type="text" id="skema${skemaCount}" name="skema[]" required
                               class="w-full px-3 py-2 border border-gray-700 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all duration-200 text-sm text-gray-900 placeholder-gray-500"
                               placeholder="Pilih skema">
                    </div>
                    <div>
                        <label for="jenjang${skemaCount}" class="block text-sm font-medium text-gray-200 mb-2">
                            Jenjang
                        </label>
                        <input type="number" id="jenjang${skemaCount}" name="jenjang[]" required
                               class="w-full px-3 py-2 border border-gray-700 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all duration-200 text-sm text-gray-900 placeholder-gray-500"
                               placeholder="Masukkan jenjang">
                    </div>
                    <div>
                        <label for="metode${skemaCount}" class="block text-sm font-medium text-gray-200 mb-2">
                            Metode Asesmen
                        </label>
                        <select id="metode${skemaCount}" name="metode[]" required
                                class="w-full px-3 py-2 border border-gray-700 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all duration-200 text-sm text-gray-900 placeholder-gray-500">
                            <option value="">Pilih metode</option>
                            <option value="Observasi">Observasi</option>
                            <option value="Portofolio">Portofolio</option>
                            <option value="Observasi & Portofolio">Observasi & Portofolio</option>
                        </select>
                    </div>
                </div>
            `;

            container.appendChild(newField);
        });

        // Remove skema field
        function removeSkema(button) {
            button.closest('.skema-group').remove();
        }

        // Modal functions
        function openModal(pdfUrl) {
            const modal = document.getElementById('pdfModal');
            const pdfViewer = document.getElementById('pdfViewer');
            pdfViewer.src = pdfUrl;
            modal.classList.remove('hidden');
        }

        function closeModal() {
            const modal = document.getElementById('pdfModal');
            const pdfViewer = document.getElementById('pdfViewer');
            pdfViewer.src = '';
            modal.classList.add('hidden');
        }

        // Enhanced autocomplete for ketua TUK
        const ketuaInput = document.getElementById('ketua');
        const ketuaDatalist = document.getElementById('ketua_tuk');

        ketuaInput.addEventListener('input', function() {
            const selectedValue = this.value;
            const options = ketuaDatalist.querySelectorAll('option');

            options.forEach(option => {
                if (option.value === selectedValue) {
                    this.setAttribute('data-tuk-name', option.textContent);
                }
            });
        });

        ketuaInput.addEventListener('change', function() {
            if (this.value && !this.getAttribute('data-tuk-name')) {
                const options = ketuaDatalist.querySelectorAll('option');
                for (let option of options) {
                    if (option.value.toLowerCase() === this.value.toLowerCase()) {
                        this.setAttribute('data-tuk-name', option.textContent);
                        break;
                    }
                }
            }
        });

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        // Close modal on background click
        document.getElementById('pdfModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>

    <!-- Flatpickr JS for modern datepicker -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

    <script>
        // Initialize Flatpickr datepickers
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize datepickers
            flatpickr("#tanggal_asesmen", {
                locale: "id",
                dateFormat: "Y-m-d",
                clickOpens: true,
                animate: true,
                disableMobile: "true"
            });

            flatpickr("#tanggal_verifikasi", {
                locale: "id",
                dateFormat: "Y-m-d",
                clickOpens: true,
                animate: true,
                disableMobile: "true"
            });
        });
    </script>
@endsection