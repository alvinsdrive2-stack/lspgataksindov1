@extends('layouts.dashboard')

@section('title', 'Registrasi Berhasil')

@section('pageTitle', 'Registrasi Berhasil - Superadmin')

@section('content')
    <!-- Success Header -->
    <div class="glass rounded-2xl shadow-xl p-8 animate-fade-in">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-full mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Registrasi Berhasil!</h2>
            <p class="text-gray-600">User baru telah berhasil ditambahkan ke sistem</p>
        </div>

        <!-- Success Message -->
        <div class="bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-lg mb-8">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="font-medium text-green-800">Registrasi user berhasil dilakukan!</p>
                    <p class="text-green-700 text-sm">Password telah dienkripsi untuk keamanan database</p>
                </div>
            </div>
        </div>

        <!-- Excel-like Table -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-[#1F3A73] to-[#3F5FA8] px-6 py-4">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    Data User Baru
                </h3>
            </div>

            <!-- Copy to Excel Instructions -->
            <div class="bg-blue-50 border-b border-blue-200 px-6 py-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-blue-800 text-sm font-medium">Data berikut dapat langsung disalin ke Excel:</span>
                    </div>
                    <button onclick="copyToClipboard()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-6M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3-3"/>
                        </svg>
                        Salin Semua
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table id="excelTable" class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TUK</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NAMA</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">EMAIL</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Password</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-900">1</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $registrationData['nama_tuk'] ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $registrationData['name'] }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $registrationData['email'] }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 font-mono">
                                <span class="inline-flex items-center px-2 py-1 bg-yellow-100 text-yellow-800 rounded">
                                    {{ $registrationData['password'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ ucfirst($registrationData['role']) }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Additional Info -->
            <div class="bg-gray-50 border-t border-gray-200 px-6 py-4">
                <div class="flex items-start space-x-2">
                    <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-sm text-gray-600">
                        <p><strong>Informasi Tambahan:</strong></p>
                        <p>No. Telepon: {{ $registrationData['notel'] ?? 'Tidak diisi' }}</p>
                        <p>Tanggal Registrasi: {{ now()->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
            <button onclick="copyToClipboard()"
                    class="flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-6M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3-3"/>
                </svg>
                Salin Data ke Clipboard
            </button>

            <a href="{{ route('register.new') }}"
               class="flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                Tambah User Baru
            </a>

            <a href="{{ url()->previous() }}"
               class="flex items-center justify-center px-6 py-3 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                </svg>
                Kembali ke Form
            </a>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="fixed bottom-4 right-4 hidden">
        <div class="bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2 animate-slideIn">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 20 20">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span>Data berhasil disalin ke clipboard!</span>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Function to copy data to clipboard
        function copyToClipboard() {
            const registrationData = {
                'No': 1,
                'TUK': '{{ $registrationData['nama_tuk'] ?? '-' }}',
                'NAMA': '{{ $registrationData['name'] }}',
                'EMAIL': '{{ $registrationData['email'] }}',
                'Password': '{{ $registrationData['password'] }}',
                'Role': '{{ ucfirst($registrationData['role']) }}'
            };

            // Convert to tab-separated format for easy Excel pasting
            const headers = Object.keys(registrationData).join('\t');
            const values = Object.values(registrationData).join('\t');
            const dataToCopy = headers + '\n' + values;

            // Create temporary textarea element
            const textarea = document.createElement('textarea');
            textarea.value = dataToCopy;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);

            // Select and copy
            textarea.select();
            document.execCommand('copy');

            // Remove temporary element
            document.body.removeChild(textarea);

            // Show toast notification
            showToast();
        }

        // Function to show toast notification
        function showToast() {
            const toast = document.getElementById('toast');
            toast.classList.remove('hidden');

            // Hide toast after 3 seconds
            setTimeout(() => {
                toast.classList.add('hidden');
            }, 3000);
        }

        // Auto-hide toast on page load if visible
        document.addEventListener('DOMContentLoaded', function() {
            const toast = document.getElementById('toast');
            if (toast && !toast.classList.contains('hidden')) {
                setTimeout(() => {
                    toast.classList.add('hidden');
                }, 3000);
            }
        });
    </script>
@endsection