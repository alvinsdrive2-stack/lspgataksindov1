@extends('layouts.dashboard')

@section('title', 'Konfirmasi Verifikasi TUK')

@section('pageTitle', 'Konfirmasi Verifikasi TUK - Direktur LSP')

@section('content')
    <!-- Statistics Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="glass rounded-xl p-6 card-hover animate-fade-in">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-700">Menunggu Konfirmasi</p>
                    <p class="text-2xl font-bold text-amber-600">{{ count($all_verifications) }}</p>
                </div>
                <div class="p-3 bg-amber-100 rounded-lg">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="glass rounded-xl p-6 card-hover animate-fade-in" style="animation-delay: 0.1s;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-700">Menunggu SK</p>
                    <p class="text-2xl font-bold text-blue-600">{{ count($all_sk) }}</p>
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
                    <p class="text-sm font-medium text-gray-700">Total Proses</p>
                    <p class="text-2xl font-bold text-gray-900">{{ count($all_verifications) + count($all_sk) }}</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="glass rounded-xl p-6 card-hover animate-fade-in" style="animation-delay: 0.3s;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-700">Hari Ini</p>
                    <p class="text-2xl font-bold text-green-600">
                        {{ collect($all_verifications)->merge($all_sk)->filter(function($item) {
                            return \Carbon\Carbon::parse($item['created_at'])->isToday();
                        })->count() }}
                    </p>
                </div>
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Verifications Table -->
    <div class="glass rounded-2xl shadow-xl overflow-hidden mb-8 animate-slide-in">
        <div class="p-6 bg-gradient-to-r from-amber-500 to-orange-600">
            <h3 class="text-xl font-bold text-white flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Menunggu Konfirmasi Direktur
            </h3>
        </div>

        <div class="p-6">
            @if (count($all_verifications) > 0)
                <div class="overflow-x-auto">
                    <table id="listTable" class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 px-4 font-semibold text-gray-900">No Surat</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-900">Nama File</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-900">Tanggal</th>
                                <th class="text-center py-3 px-4 font-semibold text-gray-900">File</th>
                                <th class="text-center py-3 px-4 font-semibold text-gray-900">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($all_verifications as $verification)
                                <tr class="border-b border-gray-100 hover:bg-amber-50 transition-colors duration-150">
                                    <td class="py-4 px-4">
                                        <span class="font-mono text-sm bg-gray-100 px-2 py-1 rounded">{{ $verification['no_surat'] }}</span>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <span class="font-medium text-gray-900">{{ $verification['link'] }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="text-sm text-gray-700">
                                            {{ \Carbon\Carbon::parse($verification['created_at'])->format('d M Y H:i') }}
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <a href="{{ Storage::disk('public')->url('tuk-paperless/' . \Carbon\Carbon::parse($verification['created_at'])->format('Y-m-d') . '/' . strtoupper($verification['tuk']) . '/' . $verification['link']) }}"
                                           target="_blank"
                                           class="inline-flex items-center space-x-2 px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition-all duration-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <span>Lihat</span>
                                        </a>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <a href="/confirm/{{ $verification['id'] }}"
                                           class="inline-flex items-center space-x-2 px-4 py-2 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span>Konfirmasi</span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 mx-auto mb-4 bg-amber-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">Tidak Ada Verifikasi Menunggu</h4>
                    <p class="text-gray-700">Semua verifikasi telah dikonfirmasi.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- SK Table -->
    <div class="glass rounded-2xl shadow-xl overflow-hidden animate-slide-in" style="animation-delay: 0.2s;">
        <div class="p-6 bg-gradient-to-r from-blue-500 to-blue-600">
            <h3 class="text-xl font-bold text-white flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Menunggu Pembuatan SK
            </h3>
        </div>

        <div class="p-6">
            @if (count($all_sk) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 px-4 font-semibold text-gray-900">No Surat</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-900">Nama File</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-900">Tanggal</th>
                                <th class="text-center py-3 px-4 font-semibold text-gray-900">File</th>
                                <th class="text-center py-3 px-4 font-semibold text-gray-900">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($all_sk as $sk)
                                <tr class="border-b border-gray-100 hover:bg-blue-50 transition-colors duration-150">
                                    <td class="py-4 px-4">
                                        <span class="font-mono text-sm bg-gray-100 px-2 py-1 rounded text-gray-900">{{ $sk['no_surat'] }}</span>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <span class="font-medium text-gray-900">{{ $sk['link'] }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="text-sm text-gray-700">
                                            {{ \Carbon\Carbon::parse($sk['created_at'])->format('d M Y H:i') }}
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <a href="{{ Storage::disk('public')->url('tuk-paperless/' . \Carbon\Carbon::parse($sk['created_at'])->format('Y-m-d') . '/' . strtoupper($sk['tuk']) . '/' . $sk['link']) }}"
                                           target="_blank"
                                           class="inline-flex items-center space-x-2 px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition-all duration-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <span>Lihat</span>
                                        </a>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <a href="/sk/{{ $sk['id'] }}"
                                           class="inline-flex items-center space-x-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <span>Buat SK</span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 mx-auto mb-4 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">Tidak Ada SK Menunggu</h4>
                    <p class="text-gray-700">Tidak ada SK yang perlu dibuat saat ini.</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Initialize DataTable for verifications table only
            $('#listTable').DataTable({
                order: [[2, 'desc']],
                pageLength: 10,
                responsive: true,
                language: {
                    search: "Cari verifikasi:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ verifikasi",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    },
                    emptyTable: "Tidak ada data verifikasi tersedia",
                    zeroRecords: "Tidak ditemukan verifikasi yang cocok"
                }
            });
        });
    </script>
@endsection