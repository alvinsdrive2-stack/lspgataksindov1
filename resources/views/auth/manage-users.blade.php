@extends('layouts.dashboard')

@section('title', 'Manage Users')

@section('pageTitle', 'Kelola Users - Superadmin')

@section('content')
    <!-- Main Content -->
    <div class="glass rounded-2xl shadow-xl p-8 animate-fade-in">
        <!-- Header -->
        <div class="mb-8 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Manajemen Users</h2>
                <p class="text-gray-600">Kelola semua pengguna sistem LSP Gataksindo</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                <!-- Search Form -->
                <div class="flex-1 lg:flex-initial">
                    <div class="relative">
                        <input type="text"
                               id="searchInput"
                               value="{{ request('search') }}"
                               placeholder="Cari berdasarkan nama, email, atau TUK..."
                               class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        @if(request('search'))
                            <button onclick="clearSearch()" class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600" title="Clear search">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>
                <a href="{{ route('register.new') }}"
                   class="flex items-center justify-center px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    Tambah User
                </a>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 animate-slideDown">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <p class="font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6 animate-slideDown">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <p class="font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Search Results Info -->
        @if(request('search'))
            <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                        </svg>
                        <p class="font-medium">
                            Menampilkan <strong>{{ $users->count() }}</strong> hasil untuk "<strong>{{ request('search') }}</strong>"
                        </p>
                    </div>
                    <a href="{{ route('users.manage') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                        Tampilkan semua
                    </a>
                </div>
            </div>
        @endif

        <!-- Users Table -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-[#1F3A73] to-[#3F5FA8] px-6 py-4">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Daftar Users
                </h3>
            </div>

            <!-- Loading indicator -->
            <div id="searchLoader" class="hidden px-6 py-4 bg-blue-50 border-b border-blue-200">
                <div class="flex items-center">
                    <svg class="animate-spin h-5 w-5 mr-3 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-blue-800">Mencari...</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TUK</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telepon</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="userTableBody" class="bg-white divide-y divide-gray-200">
                        @forelse ($users as $index => $user)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    @if(request('search'))
                                        @php
                                            $highlighted = preg_replace('/(' . preg_quote(request('search'), '/') . ')/i', '<mark class="bg-yellow-200">$1</mark>', $user->name);
                                            echo $highlighted;
                                        @endphp
                                    @else
                                        {{ $user->name }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $user->email }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                        @if($user->role === 'superadmin') bg-purple-100 text-purple-800
                                        @elseif($user->role === 'direktur') bg-yellow-100 text-yellow-800
                                        @elseif($user->role === 'admin_lsp') bg-blue-100 text-blue-800
                                        @elseif($user->role === 'validator') bg-green-100 text-green-800
                                        @elseif($user->role === 'ketua_tuk') bg-indigo-100 text-indigo-800
                                        @elseif($user->role === 'verifikator') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $user->nama_tuk ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $user->notel ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex space-x-2">
                                        <!-- Edit Button - Sama seperti di change-password -->
                                        <a href="{{ route('users.edit', $user->id) }}"
                                           class="inline-flex items-center px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded transition-colors">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Edit
                                        </a>

                                        <!-- Change Password Button -->
                                        <a href="{{ route('users.change.password.form', $user->id) }}"
                                           class="text-yellow-600 hover:text-yellow-800 transition-colors"
                                           title="Ubah Password">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                            </svg>
                                        </a>

                                        <!-- Delete Button (if not self and not superadmin) -->
                                        @if($user->id !== auth()->id() && $user->role !== 'superadmin')
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="text-red-600 hover:text-red-800 transition-colors"
                                                        title="Hapus User">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                    @if(request('search'))
                                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <p class="mb-2">Tidak ada user yang cocok dengan pencarian "<strong>{{ request('search') }}</strong>"</p>
                                        <a href="{{ route('users.manage') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                            Hapus pencarian
                                        </a>
                                    @else
                                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                        <p>Belum ada user yang terdaftar</p>
                                        <a href="{{ route('register.new') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                            Tambah user baru
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Table Footer -->
            <div class="bg-gray-50 border-t border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-gray-700">
                        Total <strong>{{ $users->count() }}</strong> user
                    </p>
                    <div class="text-sm text-gray-500">
                        <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 rounded">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Login: {{ auth()->user()->name }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Users</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $users->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Aktif Hari Ini</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $users->where('created_at', '>=', now()->today())->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center w-12 h-12 bg-purple-100 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Roles</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $roles->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
console.log('Search script loaded');
let searchTimeout;

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded');
    const searchInput = document.getElementById('searchInput');
    console.log('Search input:', searchInput);

    // Add debounce for real-time search
    searchInput.addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        const searchTerm = e.target.value;

        // Show/hide clear button
        const clearButton = e.target.nextElementSibling?.nextElementSibling;
        if (searchTerm) {
            if (clearButton && clearButton.tagName === 'BUTTON') {
                clearButton.style.display = 'block';
            }
        } else {
            if (clearButton && clearButton.tagName === 'BUTTON') {
                clearButton.style.display = 'none';
            }
        }

        // Debug: log input changes
        console.log('Search term changed:', searchTerm);

        // Debounce search - wait 500ms after user stops typing
        searchTimeout = setTimeout(() => {
            performSearch(searchTerm);
        }, 500);
    });
});

function performSearch(searchTerm) {
    console.log('Performing search for:', searchTerm);
    const loader = document.getElementById('searchLoader');
    const tableBody = document.getElementById('userTableBody');

    // Show loader
    if (loader) loader.classList.remove('hidden');

    // Debug: Check if elements exist
    console.log('Loader element:', loader);
    console.log('Table body element:', tableBody);

    // Fetch search results
    fetch(`{{ route('users.manage') }}?search=${encodeURIComponent(searchTerm)}&ajax=1`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        // Update table body
        tableBody.innerHTML = data.html;

        // Hide loader
        loader.classList.add('hidden');

        // Update search info if exists
        updateSearchInfo(searchTerm, data.count);
    })
    .catch(error => {
        console.error('Error:', error);
        loader.classList.add('hidden');
    });
}

function clearSearch() {
    const searchInput = document.getElementById('searchInput');
    searchInput.value = '';
    performSearch('');
}

function updateSearchInfo(searchTerm, count) {
    // Find and update search results info
    let searchInfo = document.querySelector('.bg-blue-50.border-blue-200');

    if (searchTerm && count !== undefined) {
        if (!searchInfo) {
            // Create search info element if it doesn't exist
            const messagesDiv = document.querySelector('.mb-6:last-of-type');
            searchInfo = document.createElement('div');
            searchInfo.className = 'bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg mb-6';
            searchInfo.innerHTML = `
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                        </svg>
                        <p class="font-medium">
                            Menampilkan <strong>${count}</strong> hasil untuk "<strong>${searchTerm}</strong>"
                        </p>
                    </div>
                    <a href="#" onclick="clearSearch()" class="text-blue-600 hover:text-blue-800 font-medium">
                        Tampilkan semua
                    </a>
                </div>
            `;
            messagesDiv.parentNode.insertBefore(searchInfo, messagesDiv.nextSibling);
        } else {
            // Update existing search info
            searchInfo.querySelector('p').innerHTML = `Menampilkan <strong>${count}</strong> hasil untuk "<strong>${searchTerm}</strong>"`;
        }
    } else {
        // Remove search info if no search term
        if (searchInfo) {
            searchInfo.remove();
        }
    }
}
</script>
@endpush