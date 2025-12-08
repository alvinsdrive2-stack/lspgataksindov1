@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
.animate-fade-in-up {
    animation: fadeInUp 0.8s ease-out forwards;
    transform: none;
}

.animate-fade-in-down {
    animation: fadeInDown 0.8s ease-out forwards;
    transform: none;
}

.animate-scale-in {
    animation: scaleIn 0.6s ease-out forwards;
    transform: none;
}


        .animate-delay-100 { animation-delay: 0.1s; opacity: 0; animation-fill-mode: forwards; }
        .animate-delay-200 { animation-delay: 0.2s; opacity: 0; animation-fill-mode: forwards; }
        .animate-delay-300 { animation-delay: 0.3s; opacity: 0; animation-fill-mode: forwards; }
        .animate-delay-400 { animation-delay: 0.4s; opacity: 0; animation-fill-mode: forwards; }
        .animate-delay-500 { animation-delay: 0.5s; opacity: 0; animation-fill-mode: forwards; }
        .animate-delay-600 { animation-delay: 0.6s; opacity: 0; animation-fill-mode: forwards; }
        .role-link {
    pointer-events: none;
}


        .role-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            transform-style: preserve-3d;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            position: relative;
            z-index: 1;
            pointer-events: auto;
        }

        .role-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 30px 60px -15px rgba(31, 58, 115, 0.3);
            z-index: 10;
        }

        .text-shadow {
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Smooth link animation */
        .smooth-link {
            position: relative;
            transition: all 0.3s ease;
        }

        .smooth-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transform: translateX(-100%);
            transition: transform 0.6s;
        }

        .smooth-link:hover::before {
            transform: translateX(100%);
        }
    </style>

    <!-- Background Pattern -->
    <div class="fixed inset-0 opacity-5 pointer-events-none">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%231F3A73" fill-opacity="0.4"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <!-- Main Container -->
    <div class="relative min-h-screen flex flex-col">
        <!-- Header Section -->
        <header class="flex-1 flex items-center justify-center px-4 py-12">
            <div class="text-center max-w-5xl mx-auto fade-in">
                <div class="mb-8 fade-in" style="animation-delay: 0.2s;">
                    <div class="relative inline-block">
                        <div class="absolute inset-0 bg-blue-500/20 blur-2xl rounded-full scale-150 animate-pulse"></div>
                        <img
                            src="/images/logo-banner.png"
                            alt="LSP LPK Gataksindo Logo"
                            class="relative w-40 h-40 lg:w-48 lg:h-48 object-contain drop-shadow-2xl"
                        />
                    </div>
                </div>

                <h1 class="text-5xl sm:text-6xl lg:text-7xl font-black text-gray-900 mb-2 text-shadow fade-in" style="animation-delay: 0.3s;">
                    SELAMAT DATANG
                </h1>
                <h2 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-[#1F3A73] mb-6 fade-in" style="animation-delay: 0.4s;">
                    SISTEM VERIFIKASI TUK
                </h2>
                <p class="text-xl sm:text-2xl text-gray-700 font-medium max-w-3xl mx-auto px-4 fade-in" style="animation-delay: 0.5s;">
                    Lembaga Sertifikasi Profesi LPK Gataksindo
                </p>
            </div>
        </header>

        <!-- Login Options Section -->
        <section class="bg-gradient-to-b from-[#1F3A73] to-[#0F1A36] py-20 lg:py-24 relative overflow-hidden">
            <!-- Background Elements -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-10 left-10 w-40 h-40 bg-white rounded-full blur-3xl"></div>
                <div class="absolute bottom-10 right-10 w-60 h-60 bg-white rounded-full blur-3xl"></div>
            </div>

            <div class="container mx-auto px-4 relative">
                <div class="text-center mb-16 fade-in">
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white mb-4">
                        PILIH PERAN ANDA
                    </h1>
                    <div class="w-32 h-1 bg-[#C1272D] mx-auto rounded-full"></div>
                </div>

                <!-- Role Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10 lg:gap-12 max-w-7xl mx-auto">
                    <!-- Admin LSP -->
                    <div class="animate-fade-in-up animate-delay-100">
                        <a href="/login" class="group block h-full smooth-link group block h-full smooth-link role-link">
                            <div class="role-card rounded-3xl p-8 pb-12 shadow-lg hover:shadow-2xl h-full flex flex-col items-center justify-center space-y-6 border-2 border-transparent hover:border-[#1F3A73]/20">
                                <div class="w-24 h-24 bg-gradient-to-br from-[#1F3A73] to-[#3F5FA8] rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                    <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                    </svg>
                                </div>
                                <div class="text-center">
                                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Admin LSP</h3>
                                    <p class="text-gray-600 font-medium">Menambah Verifikasi</p>
                                </div>
                                <div class="flex items-center text-[#1F3A73] font-semibold">
                                    <span>Masuk</span>
                                    <svg class="w-5 h-5 ml-2 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Direktur LSP -->
                    <div class="animate-fade-in-up animate-delay-200">
                        <a href="/login-direktur" class="group block h-full smooth-link group block h-full smooth-link role-link">
                            <div class="role-card rounded-3xl p-8 pb-12 shadow-2xl hover:shadow-3xl h-full flex flex-col items-center justify-center space-y-6 border-2 border-transparent hover:border-[#C1272D]/20">
                                <div class="w-24 h-24 bg-gradient-to-br from-[#C1272D] to-[E53935] rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                    <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                </div>
                                <div class="text-center">
                                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Direktur LSP</h3>
                                    <p class="text-gray-600 font-medium">Approval</p>
                                </div>
                                <div class="flex items-center text-[#C1272D] font-semibold">
                                    <span>Masuk</span>
                                    <svg class="w-5 h-5 ml-2 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Verifikator -->
                    <div class="animate-fade-in-up animate-delay-300">
                        <a href="/login-verifikator" class="group block h-full smooth-link group block h-full smooth-link role-link">
                            <div class="role-card rounded-3xl p-8 pb-12 shadow-lg hover:shadow-2xl h-full flex flex-col items-center justify-center space-y-6 border-2 border-transparent hover:border-green-500/20">
                                <div class="w-24 h-24 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                    <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                                    </svg>
                                </div>
                                <div class="text-center">
                                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Verifikator</h3>
                                    <p class="text-gray-600 font-medium">Proses verifikasi</p>
                                </div>
                                <div class="flex items-center text-green-600 font-semibold">
                                    <span>Masuk</span>
                                    <svg class="w-5 h-5 ml-2 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Ketua TUK -->
                    <div class="animate-fade-in-up animate-delay-400">
                        <a href="/login-tuk" class="group block h-full smooth-link group block h-full smooth-link role-link">
                            <div class="role-card rounded-3xl p-8 pb-12 shadow-lg hover:shadow-2xl h-full flex flex-col items-center justify-center space-y-6 border-2 border-transparent hover:border-purple-500/20">
                                <div class="w-24 h-24 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                    <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                    </svg>
                                </div>
                                <div class="text-center">
                                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Ketua TUK</h3>
                                    <p class="text-gray-600 font-medium">Manajemen TUK</p>
                                </div>
                                <div class="flex items-center text-purple-600 font-semibold">
                                    <span>Masuk</span>
                                    <svg class="w-5 h-5 ml-2 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Validator -->
                    <div class="animate-fade-in-up animate-delay-500">
                        <a href="/login-validator" class="group block h-full smooth-link group block h-full smooth-link role-link">
                            <div class="role-card rounded-3xl p-8 pb-12 shadow-lg hover:shadow-2xl h-full flex flex-col items-center justify-center space-y-6 border-2 border-transparent hover:border-amber-500/20">
                                <div class="w-24 h-24 bg-gradient-to-br from-amber-500 to-orange-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                    <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/>
                                    </svg>
                                </div>
                                <div class="text-center">
                                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Validator</h3>
                                    <p class="text-gray-600 font-medium">Validasi sertifikasi</p>
                                </div>
                                <div class="flex items-center text-amber-600 font-semibold">
                                    <span>Masuk</span>
                                    <svg class="w-5 h-5 ml-2 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Archive -->
                    <div class="animate-fade-in-up animate-delay-600">
                        <a href="/archive" class="group block h-full smooth-link group block h-full smooth-link role-link">
                            <div class="role-card rounded-3xl p-8 pb-12 shadow-lg hover:shadow-2xl h-full flex flex-col items-center justify-center space-y-6 border-2 border-transparent hover:border-indigo-500/20">
                                <div class="w-24 h-24 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                    <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M20 6h-8l-2-2H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm0 12H4V8h16v10z"/>
                                    </svg>
                                </div>
                                <div class="text-center">
                                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Archive</h3>
                                    <p class="text-gray-600 font-medium">Surat Verifikasi</p>
                                </div>
                                <div class="flex items-center text-indigo-600 font-semibold">
                                    <span>Buka</span>
                                    <svg class="w-5 h-5 ml-2 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-[#0F1A36] text-white py-8 fade-in" style="animation-delay: 0.7s;">
            <div class="container mx-auto px-4">
                <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                    <div class="text-center md:text-left">
                        <p class="text-lg font-semibold">© 2024 LSP LPK Gataksindo</p>
                        <p class="text-sm text-gray-400">All rights reserved</p>
                    </div>
                    <div class="flex items-center space-x-6">
                        <span class="text-sm text-gray-400">Version 1.0.0</span>
                        <div class="flex space-x-3">
                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                            <span class="text-sm text-gray-400">System Online</span>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
@endsection