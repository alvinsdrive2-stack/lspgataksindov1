<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} - LSP LPK Gataksindo</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo-banner.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite('resources/css/app.css')

    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/v/dt/dt-1.13.4/datatables.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
        /* Page Transition Styles */
        .page-transition-enter {
            opacity: 0;
            transform: translateY(20px);
        }

        .page-transition-enter-active {
            opacity: 1;
            transform: translateY(0);
            transition: opacity 0.5s ease, transform 0.5s ease;
        }

        .page-transition-exit {
            opacity: 1;
            transform: translateY(0);
        }

        .page-transition-exit-active {
            opacity: 0;
            transform: translateY(-20px);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        /* Loading Overlay */
        #page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #1F3A73 0%, #0F1A36 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.5s ease, visibility 0.5s ease;
        }

        #page-loader.hidden {
            opacity: 0;
            visibility: hidden;
        }

        .loader-content {
            text-align: center;
            color: white;
        }

        .loader-logo {
            width: 80px;
            height: 80px;
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }

        .loader-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }

        /* Smooth scroll behavior */
        html {
            scroll-behavior: smooth;
        }

        /* Link transitions */
        .smooth-link {
            position: relative;
            transition: all 0.3s ease;
        }

        .smooth-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -2px;
            left: 50%;
            background-color: currentColor;
            transition: all 0.3s ease;
        }

        .smooth-link:hover::after {
            width: 100%;
            left: 0;
        }

        /* Fade in animation for content */
        .fade-in {
            animation: fadeIn 0.8s ease forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Custom scrollbar styling */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #1F3A73;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #3F5FA8;
        }

        /* Selection color */
        ::selection {
            background-color: #1F3A73;
            color: white;
        }

        ::-moz-selection {
            background-color: #1F3A73;
            color: white;
        }
    </style>
</head>

<body class="font-sans antialiased bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <!-- Page Loader -->
    <div id="page-loader">
        <div class="loader-content">
            <img src="/images/logo-banner.png" alt="Loading..." class="loader-logo">
            <div class="loader-spinner"></div>
            <p class="text-lg font-medium">Memuat...</p>
        </div>
    </div>

    <!-- Main Content Wrapper -->
    <div id="app" class="page-transition-enter">
    <!-- Background Pattern -->
    <div class="fixed inset-0 opacity-5 pointer-events-none">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%231F3A73\" fill-opacity=\"0.4\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <!-- Navigation Header -->
    <header class="bg-white/80 backdrop-blur-lg border-b border-gray-200/50 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <img src="/images/logo-banner.png" alt="LSP LPK Gataksindo" class="w-10 h-10 object-contain">
                        <div class="ml-3">
                            <h1 class="text-xl font-bold text-[#1F3A73]">{{ $pageTitle ?? 'Dashboard' }}</h1>
                            <p class="text-xs text-gray-700">Lembaga Sertifikasi Profesi LPK Gataksindo</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <!-- Navigation -->
                    <nav class="hidden md:flex space-x-6">
                        <a href="/archive" class="text-gray-800 hover:text-[#1F3A73] font-medium transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"/>
                            </svg>
                            Archive
                        </a>
                        @if (auth()->user() && in_array(auth()->user()->role, ['direktur', 'validator']))
                            <a href="{{ auth()->user()->role === 'direktur' ? '/confirm' : '/validation' }}"
                               class="text-gray-800 hover:text-[#C1272D] font-medium transition-colors flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ auth()->user()->role === 'direktur' ? 'Konfirmasi' : 'Validasi' }}
                            </a>
                        @endif
                    </nav>

                    <!-- User Menu & Logout -->
                    <div class="flex items-center space-x-3">
                        @if (auth()->user())
                            <div class="hidden md:flex items-center space-x-2 px-3 py-2 bg-gradient-to-r from-[#1F3A73] to-[#3F5FA8] rounded-full">
                                <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                                <span class="text-white text-sm font-medium">
                                    @php
    $role = auth()->user()->role;

    // Replace underscore with space + remove numbers
    $role = preg_replace('/[0-9]/', '', str_replace('_', ' ', $role));

    // Split words
    $words = explode(' ', $role);

    // Capitalize each word, except if word is 'lsp'
    $words = array_map(function($w) {
        return strtolower($w) === 'lsp' ? 'LSP' : ucfirst(strtolower($w));
    }, $words);

    // Join back
    $roleFormatted = implode(' ', $words);
@endphp

<span class="text-white text-sm font-medium">
    {{ $roleFormatted }}
</span>

                                </span>
                            </div>
                        @endif
                        <a href="{{ route('logout') }}"
                           class="flex items-center space-x-2 px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            <span class="font-medium">Logout</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Success Message -->
    @if (session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-800 px-6 py-4 rounded-xl shadow-sm flex items-center animate-slideDown">
                <svg class="w-6 h-6 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="font-medium">{!! session('success') !!}</p>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 relative">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white/70 backdrop-blur-lg border-t border-gray-200/50 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-center md:text-left">
                    <p class="text-sm text-gray-700">© 2024 LSP LPK Gataksindo</p>
                    <p class="text-xs text-gray-600">Sistem Verifikasi TUK v1.0.0</p>
                </div>
                <div class="flex items-center space-x-2 mt-4 md:mt-0">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="text-sm text-gray-700">System Online</span>
                </div>
            </div>
        </div>
    </footer>

    </div> <!-- End of app div -->

    <!-- jQuery & DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/v/dt/dt-1.13.4/datatables.min.js"></script>

    <!-- Custom JavaScript -->
    <script>
        // Page loader management
        window.addEventListener('load', function() {
            // Hide loader after page loads
            setTimeout(() => {
                const loader = document.getElementById('page-loader');
                const app = document.getElementById('app');

                loader.classList.add('hidden');
                app.classList.remove('page-transition-enter');
                app.classList.add('page-transition-enter-active');
            }, 500);
        });

        // Smooth navigation for all internal links
        document.addEventListener('DOMContentLoaded', function() {
            // Animate elements on load
            const elements = document.querySelectorAll('.animate-fade-in, .animate-slide-in');
            elements.forEach((el, index) => {
                setTimeout(() => {
                    el.style.opacity = '0';
                    el.style.transform = 'translateY(20px)';
                    el.style.transition = 'all 0.6s ease-out';

                    setTimeout(() => {
                        el.style.opacity = '1';
                        el.style.transform = 'translateY(0)';
                    }, 100);
                }, index * 100);
            });

            // Add loading state to all internal links
            const internalLinks = document.querySelectorAll('a[href^="/"]:not([target="_blank"]):not([data-no-transition])');

            internalLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');

                    // Skip if it's a hash link or current page
                    if (href.startsWith('#') || href === window.location.pathname) {
                        return;
                    }

                    e.preventDefault();

                    // Show loader with transition
                    const loader = document.getElementById('page-loader');
                    const app = document.getElementById('app');

                    // Start exit animation
                    app.classList.remove('page-transition-enter-active');
                    app.classList.add('page-transition-exit-active');

                    setTimeout(() => {
                        loader.classList.remove('hidden');
                        // Navigate to new page
                        window.location.href = href;
                    }, 300);
                });
            });

            // Add smooth scroll for anchor links
            const anchorLinks = document.querySelectorAll('a[href^="#"]');
            anchorLinks.forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Intersection Observer for fade-in animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            // Observe elements with fade-in class
            document.querySelectorAll('.fade-in').forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(30px)';
                el.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
                observer.observe(el);
            });
        });

        // Handle browser back/forward buttons
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                // Page is being restored from cache
                const loader = document.getElementById('page-loader');
                loader.classList.add('hidden');
            }
        });

        // Prevent FOUC (Flash of Unstyled Content)
        document.documentElement.classList.add('js');

        // Enhanced DataTable styling
        $(document).ready(function() {
            if (typeof $.fn.DataTable !== 'undefined') {
                $.extend(true, $.fn.dataTable.defaults, {
                    language: {
                        search: "Cari:",
                        lengthMenu: "Tampilkan _MENU_ data per halaman",
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        paginate: {
                            first: "Pertama",
                            last: "Terakhir",
                            next: "Selanjutnya",
                            previous: "Sebelumnya"
                        },
                        emptyTable: "Tidak ada data tersedia",
                        zeroRecords: "Tidak ditemukan data yang cocok"
                    },
                    pageLength: 10,
                    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]]
                });
            }
        });
    </script>

    @yield('scripts')
    @stack('scripts')
</body>
</html>