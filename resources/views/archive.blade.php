@extends('layouts.dashboard')

@section('title', 'Archive Verifikasi TUK')

@section('pageTitle', 'Archive Verifikasi TUK')

@section('content')
   

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="glass rounded-xl p-6 card-hover animate-fade-in" style="animation-delay: 0.1s;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-700">Total Surat</p>
                    <p class="text-2xl font-bold text-gray-900">{{ count($all_files_view) }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="glass rounded-xl p-6 card-hover animate-fade-in" style="animation-delay: 0.2s;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-700">Total TUK</p>
                    <p class="text-2xl font-bold text-gray-900">{{ count($tuk_filtered) }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="glass rounded-xl p-6 card-hover animate-fade-in" style="animation-delay: 0.3s;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-700">Bulan Ini</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ collect($all_files_view)->filter(function($file) {
                            return \Carbon\Carbon::parse($file['created_at'])->isCurrentMonth();
                        })->count() }}
                    </p>
                </div>
                <div class="p-3 bg-amber-100 rounded-lg">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="glass rounded-xl p-6 card-hover animate-fade-in" style="animation-delay: 0.4s;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-700">Tersedia</p>
                    <p class="text-2xl font-bold text-green-600">
                        {{ collect($all_files_view)->filter(function($file) {
                            return file_exists(public_path('files/' . $file['no_surat']));
                        })->count() }}
                    </p>
                </div>
                <div class="p-3 bg-emerald-100 rounded-lg">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="glass rounded-2xl shadow-xl overflow-hidden animate-slide-in">
        <div class="p-6 bg-gradient-to-r from-indigo-500 to-indigo-600">
            <h3 class="text-xl font-bold text-white flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v1a1 1 0 001 1h4a1 1 0 001-1v-1m3-2V8a2 2 0 00-2-2H8a2 2 0 00-2 2v6m12 0H6"/>
                </svg>
                Daftar Surat Verifikasi TUK
            </h3>
        </div>

        <div class="p-6">
            <div class="overflow-x-auto">
                <table id="listTable" class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-3 px-4 font-semibold text-gray-900">No Surat</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-900">TUK</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-900">Tanggal Dibuat</th>
                            <th class="text-center py-3 px-4 font-semibold text-gray-900">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($all_files_view as $file)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors duration-150">
                                <td class="py-4 px-4">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-mono text-sm bg-gray-100 px-2 py-1 rounded">{{ $file['no_surat'] }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                                        {{ $file['tuk'] }}
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center text-sm text-gray-700">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        {{ \Carbon\Carbon::parse($file['created_at'])->format('d M Y') }}
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <a href="/files/{{ $file['no_surat'] }}"
                                       class="inline-flex items-center space-x-2 px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        <span>Lihat File</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Empty State -->
    @if (count($all_files_view) === 0)
        <div class="glass rounded-2xl p-12 text-center animate-fade-in">
            <div class="w-24 h-24 mx-auto mb-6 bg-gray-100 rounded-full flex items-center justify-center">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum ada data surat verifikasi</h3>
            <p class="text-gray-700">Silakan tambah surat verifikasi baru untuk melihat data di sini.</p>
        </div>
    @endif
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Initialize DataTable with custom settings
            $('#listTable').DataTable({
                order: [[2, 'desc']],
                pageLength: 10,
                responsive: true,
                language: {
                    search: "Cari surat:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ surat",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    },
                    emptyTable: "Tidak ada data surat tersedia",
                    zeroRecords: "Tidak ditemukan surat yang cocok"
                }
            });

            // Enhanced filter functionality
            $('#status').on('change', function() {
                var selectedTuk = $(this).val();
                $('#listTable').DataTable().column(1).search(selectedTuk).draw();
            });

        });
    </script>
@endsection