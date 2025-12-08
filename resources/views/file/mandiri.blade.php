@extends('layouts.dashboard')

@section('title', 'Penugasan Verifikator TUK Mandiri')

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
        <div class="glass rounded-2xl shadow-xl p-8 animate-fade-in">
            <!-- Form Header -->
            <div class="mb-8 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-[#1F3A73] to-[#3F5FA8] rounded-2xl mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Form Verifikasi TUK Mandiri</h2>
                <p class="text-gray-600">DATA KLASIFIKASI KUALIFIKASI</p>
            </div>

            <form action="{{ route('createFileMandiri') }}" method="POST" class="space-y-8">
                @csrf

                <!-- Informasi Utama Section -->
                <div class="bg-white rounded-xl p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold text-[#1F3A73] mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Informasi Utama
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label for="nomor" class="block text-sm font-medium text-gray-700 mb-2">
                                Nomor Surat
                            </label>
                            <input type="number" id="nomor" name="nomor" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1F3A73] focus:border-transparent transition-all duration-200 text-gray-900 placeholder-gray-500"
                                   placeholder="Masukkan nomor surat">
                        </div>

                        <div>
                            <label for="tuk" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama TUK
                            </label>
                            <input type="text" id="tuk" name="tuk" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1F3A73] focus:border-transparent transition-all duration-200 text-gray-900 placeholder-gray-500"
                                   placeholder="Masukkan nama TUK">
                        </div>

                        <div>
                            <label for="tanggal1" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Asesmen
                            </label>
                            <div class="relative">
                                <input type="text" id="tanggal1" name="tanggal1" required
                                       class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1F3A73] focus:border-transparent transition-all duration-200 text-gray-900 cursor-pointer"
                                       placeholder="Pilih tanggal asesmen" readonly>
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="peserta" class="block text-sm font-medium text-gray-700 mb-2">
                                Jumlah Peserta
                            </label>
                            <input type="number" id="peserta" name="peserta" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1F3A73] focus:border-transparent transition-all duration-200 text-gray-900 placeholder-gray-500"
                                   placeholder="Masukkan jumlah peserta">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">
                            Alamat
                        </label>
                        <input type="text" id="alamat" name="alamat" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1F3A73] focus:border-transparent transition-all duration-200 text-gray-900 placeholder-gray-500"
                               placeholder="Masukkan alamat lengkap">
                    </div>
                </div>

                <!-- Personil Section -->
                <div class="bg-white rounded-xl p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Personil Terkait
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <label for="ketua" class="block text-sm font-medium text-gray-700 mb-2">
                                Ketua TUK
                            </label>
                            <input type="text" id="ketua" name="ketua" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1F3A73] focus:border-transparent transition-all duration-200 text-gray-900 placeholder-gray-500"
                                   placeholder="Masukkan nama ketua TUK">
                        </div>

                        <div>
                            <label for="asesor" class="block text-sm font-medium text-gray-700 mb-2">
                                Asesor Kompetensi
                            </label>
                            <input type="text" id="asesor" name="asesor" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1F3A73] focus:border-transparent transition-all duration-200 text-gray-900 placeholder-gray-500"
                                   placeholder="Masukkan nama asesor">
                        </div>
                    </div>
                </div>

                <!-- Subklasifikasi Section -->
                <div class="bg-white rounded-xl p-6 border border-gray-200">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Subklasifikasi Kualifikasi
                        </h3>
                        <button type="button" id="addSubklasBtn"
                                class="inline-flex items-center space-x-2 px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            <span>Tambah Subklasifikasi</span>
                        </button>
                    </div>

                    <div id="subklasContainer" class="space-y-4">
                        <!-- Initial 4 subklasifikasi fields -->
                        @for ($i = 1; $i <= 4; $i++)
                            <div class="subklas-group bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <h4 class="font-medium text-gray-900 mb-3">Subklasifikasi #{{ $i }}</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="subklas{{ $i }}" class="block text-sm font-medium text-gray-700 mb-2">
                                            Subklasifikasi
                                        </label>
                                        <input list="jabker" type="text" id="subklas{{ $i }}" name="subklas[]" required
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1F3A73] focus:border-transparent transition-all duration-200 text-sm text-gray-900 placeholder-gray-500"
                                               placeholder="Pilih subklasifikasi">
                                        <datalist id="jabker">
                                            @foreach ($allSubklas as $subklas)
                                                <option value="{{ $subklas->deskripsi_subklasifikasi }}">{{ $subklas->kode_subklasifikasi }}</option>
                                            @endforeach
                                        </datalist>
                                    </div>

                                    <div>
                                        <label for="jenjang{{ $i }}" class="block text-sm font-medium text-gray-700 mb-2">
                                            Jenjang
                                        </label>
                                        <input type="number" id="jenjang{{ $i }}" name="jenjang[]" required
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1F3A73] focus:border-transparent transition-all duration-200 text-sm text-gray-900 placeholder-gray-500"
                                               placeholder="Masukkan jenjang">
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-center">
                    <button type="submit"
                            class="flex items-center space-x-3 px-8 py-3 bg-gradient-to-r from-[#1F3A73] to-[#3F5FA8] hover:from-[#3F5FA8] hover:to-[#1F3A73] text-white font-semibold rounded-lg transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Simpan Data Verifikasi</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Flatpickr CSS for modern datepicker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <style>
        /* Custom Flatpickr styling to match theme */
        .flatpickr-calendar {
            border-radius: 12px;
            box-shadow: 0 20px 25px -5px rgba(31, 58, 115, 0.1), 0 10px 10px -5px rgba(31, 58, 115, 0.04);
            border: 1px solid #e5e7eb;
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
            background: rgb(31, 58, 115);
            color: white;
            border-color: rgb(31, 58, 115);
        }

        .flatpickr-day.selected {
            background: rgb(31, 58, 115);
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
        let subklasCount = 4;

        // Add new subklasifikasi fields
        document.getElementById('addSubklasBtn').addEventListener('click', function() {
            subklasCount++;
            const container = document.getElementById('subklasContainer');

            const newField = document.createElement('div');
            newField.className = 'subklas-group bg-gray-50 rounded-lg p-4 border border-gray-200 animate-fade-in';
            newField.innerHTML = `
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-medium text-gray-900">Subklasifikasi #${subklasCount}</h4>
                    <button type="button" onclick="removeSubklas(this)" class="text-red-500 hover:text-red-700 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="subklas${subklasCount}" class="block text-sm font-medium text-gray-700 mb-2">
                            Subklasifikasi
                        </label>
                        <input list="jabker" type="text" id="subklas${subklasCount}" name="subklas[]" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1F3A73] focus:border-transparent transition-all duration-200 text-sm text-gray-900 placeholder-gray-500"
                               placeholder="Pilih subklasifikasi">
                    </div>
                    <div>
                        <label for="jenjang${subklasCount}" class="block text-sm font-medium text-gray-700 mb-2">
                            Jenjang
                        </label>
                        <input type="number" id="jenjang${subklasCount}" name="jenjang[]" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1F3A73] focus:border-transparent transition-all duration-200 text-sm text-gray-900 placeholder-gray-500"
                               placeholder="Masukkan jenjang">
                    </div>
                </div>
            `;

            container.appendChild(newField);
        });

        // Remove subklasifikasi field
        function removeSubklas(button) {
            button.closest('.subklas-group').remove();
        }

        // Initialize Flatpickr datepicker
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr("#tanggal1", {
                locale: "id",
                dateFormat: "Y-m-d",
                clickOpens: true,
                animate: true,
                disableMobile: "true"
            });
        });
    </script>

    <!-- Flatpickr JS for modern datepicker -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

    <script>
        // Initialize datepicker
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr("#tanggal1", {
                locale: "id",
                dateFormat: "Y-m-d",
                clickOpens: true,
                animate: true,
                disableMobile: "true"
            });
        });
    </script>
@endsection