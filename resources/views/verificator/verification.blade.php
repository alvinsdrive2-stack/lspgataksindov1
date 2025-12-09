@extends('layouts.dashboard-dark')

@section('title', 'Form Verifikasi TUK')

@section('pageTitle', 'Form Verifikasi - Verifikator')

@section('content')
    <!-- Success Message -->
    @if (session('success'))
        <div class="glass-dark rounded-xl p-6 mb-8 animate-slideDown">
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
    <div class="glass-dark rounded-2xl shadow-xl p-8 animate-fade-in">
        <!-- Form Header -->
        <div class="mb-8 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-[#1F3A73] to-[#3F5FA8] rounded-2xl mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-white mb-2">Form Verifikasi TUK</h2>
            <p class="text-gray-300">STANDAR PERSYARATAN JABATAN KERJA</p>
        </div>

        <form action="{{ route('verify') }}" method="POST" class="space-y-8">
            @csrf
            <input type="hidden" name="id" value="{{ $id }}">

            <!-- Standar Persyaratan Section -->
            <div class="card-dark rounded-xl p-6 border border-gray-700">
                <h3 class="text-lg font-semibold text-primary-dark mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Standar Persyaratan Umum
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Lokasi Gedung -->
                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="gedung" id="gedung" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="gedung" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">
                            Lokasi Gedung Dengan Akses Masuk & Keluar
                        </label>
                    </div>

                    <!-- Kondisi Bangunan -->
                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="bangunan" id="bangunan" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="bangunan" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">
                            Kondisi Bangunan Baik Dengan Penerangan Cukup
                        </label>
                    </div>

                    <!-- Ruangan Uji Tulis -->
                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="ruangan" id="ruangan" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="ruangan" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">
                            Ruangan Uji Tulis Sesuai Kapasitas Asesi
                        </label>
                    </div>

                    <!-- Internet -->
                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="internet" id="internet" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="internet" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">
                            Jaringan Internet Min. 10 Mbps
                        </label>
                    </div>

                    <!-- Proyektor -->
                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="proyektor" id="proyektor" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="proyektor" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">
                            Monitor / Proyektor
                        </label>
                    </div>

                    <!-- Laptop/Komputer -->
                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="pc" id="pc" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="pc" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">
                            Laptop / Komputer Min. 1
                        </label>
                    </div>

                    <!-- Meja Asesor -->
                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="mejaasesor" id="mejaasesor" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="mejaasesor" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">
                            Meja Dan Kursi Asesor
                        </label>
                    </div>

                    <!-- Meja Asesi -->
                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="mejaasesi" id="mejaasesi" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="mejaasesi" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">
                            Meja Dan Kursi Asesi
                        </label>
                    </div>

                    <!-- Alat Komunikasi -->
                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="komunikasi" id="komunikasi" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="komunikasi" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">
                            Alat Komunikasi (HT/HP)
                        </label>
                    </div>

                    <!-- Alat Dokumentasi -->
                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="dokumentasi" id="dokumentasi" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="dokumentasi" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">
                            Alat Dokumentasi (HP/Kamera)
                        </label>
                    </div>

                    <!-- Pendingin Ruangan -->
                    <div class="lg:col-span-3">
                        <label for="pendingin" class="block text-sm font-medium text-gray-300 mb-2">
                            Spesifikasi Pendingin Ruangan
                        </label>
                        <select id="pendingin" name="pendingin" class="w-full px-4 py-3 border border-gray-700 rounded-lg focus:ring-2 focus:ring-[#1F3A73] focus:border-transparent transition-all duration-200 text-gray-900">
                            <option value="">Pilih spesifikasi pendingin</option>
                            <option value="1/2pk">1/2 PK</option>
                            <option value="3/4pk">3/4 PK</option>
                            <option value="1pk">1 PK</option>
                            <option value="1,5pk">1.5 PK</option>
                            <option value="kipas">Kipas Angin / Kipas Blower</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Peralatan Tulis Menulis Section -->
            <div class="card-dark rounded-xl p-6 border border-gray-700">
                <h3 class="text-lg font-semibold text-primary-dark mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                    Peralatan Tulis Menulis
                </h3>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="pulpen" id="pulpen" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="pulpen" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Pulpen</label>
                    </div>

                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="pensil" id="pensil" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="pensil" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Pensil</label>
                    </div>

                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="tipex" id="tipex" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="tipex" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Correction Tape</label>
                    </div>

                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="penghapus" id="penghapus" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="penghapus" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Penghapus Pensil</label>
                    </div>

                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="spidol" id="spidol" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="spidol" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Spidol</label>
                    </div>

                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="penggaris" id="penggaris" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="penggaris" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Penggaris Min. 30cm</label>
                    </div>

                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="hvs" id="hvs" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="hvs" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">HVS A4</label>
                    </div>
                </div>
            </div>

            <!-- Keselamatan Kerja Section -->
            <div class="card-dark rounded-xl p-6 border border-gray-700">
                <h3 class="text-lg font-semibold text-primary-dark mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    Keselamatan Kerja
                </h3>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="apd" id="apd" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="apd" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">APD</label>
                    </div>

                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="apk" id="apk" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="apk" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">APK</label>
                    </div>

                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="p3k" id="p3k" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="p3k" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">P3K</label>
                    </div>

                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="rambu" id="rambu" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="rambu" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Rambu Keselamatan Kerja</label>
                    </div>
                </div>
            </div>

            <!-- Peralatan Praktik Section (Dynamic) -->
            @if (!empty($allPeralatan))
            <div class="card-dark rounded-xl p-6 border border-gray-700">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-[#1F3A73] flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Peralatan Praktik
                    </h3>
                    <a href="#proyek-kerja" onclick="openModal('../daftarperalatan.pdf')" class="text-sm text-[#1F3A73] hover:text-[#3F5FA8] font-medium flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        Lihat Panduan
                    </a>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                    <!-- Peralatan Ukur -->
                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="theodolite" id="theodolite" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="theodolite" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Theodolite</label>
                    </div>

                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="meteran" id="meteran" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="meteran" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Meteran</label>
                    </div>

                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="waterpass" id="waterpass" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="waterpass" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Waterpass</label>
                    </div>

                    <!-- Software -->
                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="autocad" id="autocad" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="autocad" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Autocad</label>
                    </div>

                    <!-- Peralatan Konstruksi -->
                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="perancah" id="perancah" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="perancah" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Perancah</label>
                    </div>

                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="bouwplank" id="bouwplank" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="bouwplank" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Bouwplank</label>
                    </div>

                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="patok" id="patok" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="patok" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Patok / Bench Mark</label>
                    </div>

                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="jidar" id="jidar" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="jidar" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Jidar</label>
                    </div>

                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="bandul" id="bandul" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="bandul" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Lot / Bandul</label>
                    </div>

                    <!-- Peralatan Tangan -->
                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="palu_karet" id="palu_karet" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="palu_karet" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Palu Karet</label>
                    </div>

                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="palu_besi" id="palu_besi" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="palu_besi" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Palu Besi</label>
                    </div>

                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="tang_jepit" id="tang_jepit" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="tang_jepit" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Tang Jepit</label>
                    </div>

                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="tang_potong" id="tang_potong" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="tang_potong" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Tang Potong</label>
                    </div>

                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="gergaji_kayu" id="gergaji_kayu" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="gergaji_kayu" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Gergaji Kayu</label>
                    </div>

                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="gergaji_besi" id="gergaji_besi" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="gergaji_besi" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Gergaji Besi</label>
                    </div>

                    <!-- Peralatan Listrik -->
                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="gerinda" id="gerinda" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="gerinda" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Mesin Gerinda</label>
                    </div>

                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="pembengkok" id="pembengkok" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="pembengkok" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Pembengkok Besi</label>
                    </div>

                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="mesin_bor" id="mesin_bor" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="mesin_bor" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Mesin Bor</label>
                    </div>

                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="mesin_serut" id="mesin_serut" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="mesin_serut" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Mesin Serut</label>
                    </div>

                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="mesin_gergaji" id="mesin_gergaji" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="mesin_gergaji" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Mesin Gergaji</label>
                    </div>

                    <!-- Peralatan Kayu -->
                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="pahat" id="pahat" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="pahat" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Pahat Kayu</label>
                    </div>

                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="obeng" id="obeng" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="obeng" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Obeng</label>
                    </div>

                    <!-- Peralatan Tanah -->
                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="cangkul" id="cangkul" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="cangkul" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Cangkul / Sekop</label>
                    </div>

                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="sendok_semen" id="sendok_semen" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="sendok_semen" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Sendok Semen</label>
                    </div>

                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="ember" id="ember" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="ember" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Ember</label>
                    </div>

                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="pengerik" id="pengerik" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="pengerik" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Alat Pengerik / Kape</label>
                    </div>

                    <!-- Peralatan Cat -->
                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="roll_cat" id="roll_cat" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="roll_cat" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Kuas Roll Cat</label>
                    </div>

                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="cat" id="cat" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="cat" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Cat</label>
                    </div>

                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="kuas_cat" id="kuas_cat" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="kuas_cat" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Kuas Cat</label>
                    </div>

                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="nampan" id="nampan" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="nampan" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Nampan Cat</label>
                    </div>

                    <!-- Lain-lain -->
                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="benang" id="benang" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="benang" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Benang</label>
                    </div>

                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="paku" id="paku" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="paku" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Paku</label>
                    </div>

                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="ampelas" id="ampelas" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="ampelas" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Ampelas</label>
                    </div>

                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="triplek" id="triplek" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="triplek" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Triplek</label>
                    </div>

                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="lakban" id="lakban" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="lakban" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Masking Tape / Lakban</label>
                    </div>

                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="dempul" id="dempul" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="dempul" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Dempul</label>
                    </div>

                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="papan_applicator" id="papan_applicator" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="papan_applicator" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Papan Applicator</label>
                    </div>

                    <div class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-800/50 transition-colors">
                        <input name="penggaris_siku" id="penggaris_siku" type="checkbox" value="Yes" class="h-4 w-4 text-[#1F3A73] focus:ring-[#1F3A73] border-gray-300 rounded">
                        <label for="penggaris_siku" class="ml-3 block text-sm font-medium text-gray-300 cursor-pointer">Penggaris Siku</label>
                    </div>
                </div>
            </div>
            @endif

            <!-- Submit Button -->
            <div class="flex justify-center">
                <button type="submit"
                        class="btn-primary-dark flex items-center space-x-3 px-8 py-3 font-semibold rounded-lg shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Submit Verifikasi</span>
                </button>
            </div>
        </form>
    </div>

    <!-- PDF Modal -->
    <div id="pdfModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="glass-dark rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
                <div class="flex items-center justify-between p-4 border-b border-gray-700">
                    <h3 class="text-lg font-semibold text-white">Preview Panduan Peralatan</h3>
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
    <script>
        // Modal functions
        function openModal(pdfUrl) {
            event.preventDefault();
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

        // Make checkbox containers clickable
        document.addEventListener('DOMContentLoaded', function() {
            // Find ALL divs that contain checkboxes
            const allDivs = document.querySelectorAll('div');

            allDivs.forEach(function(div) {
                const checkbox = div.querySelector('input[type="checkbox"]');
                const label = div.querySelector('label');

                if (checkbox && label) {
                    // Make the entire div look clickable
                    div.style.cursor = 'pointer';

                    // Add click event to the entire div
                    div.addEventListener('click', function(e) {
                        // Don't toggle if clicking on:
                        // 1. The checkbox itself
                        // 2. The label text
                        if (e.target.type === 'checkbox' || e.target.tagName === 'LABEL') {
                            return;
                        }

                        // Toggle the checkbox
                        checkbox.checked = !checkbox.checked;

                        // Trigger change event
                        const event = new Event('change', { bubbles: true });
                        checkbox.dispatchEvent(event);
                    });
                }
            });
        });
    </script>
@endsection