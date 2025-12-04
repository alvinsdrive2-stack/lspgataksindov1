<!DOCTYPE html>
<html class="dark" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <title>Pendaftaran TUK - LSP LPK Gataksindo</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <!-- Scripts -->
    @vite('resources/css/app.css')
</head>
<body>
    <div class="bg-white min-h-screen">
        <div class="flex justify-center items-center pt-12">
            <img src="/images/banner.png" alt="banner" width="1024px" class="rounded-xl">
        </div>
        <div class="mx-auto max-w-5xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
            <form action="{{ route('registerTUK') }}" method="POST" class="mx-auto max-w-5xl" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 gap-x-8 gap-y-8 py-10 md:grid-cols-3">
                    <div class="px-4 sm:px-0">
                        <h2 class="text-base/7 font-semibold text-gray-900">Panduan Pengisian Pengajuan TUK</h2>
                        <button type="button" onclick="my_modal_2.showModal()" class="mt-1 rounded-md bg-violet-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:cursor-pointer">Lihat Panduan</button>
                        <dialog id="my_modal_2" class="modal">
                            <div class="modal-box">
                                <h3 class="text-lg font-bold">Hello!</h3>
                                <p class="py-4">Press ESC key or click outside to close</p>
                            </div>
                            <form method="dialog" class="modal-backdrop">
                                <button type="button">close</button>
                            </form>
                        </dialog>
                    </div>
                    <div class="bg-white shadow-xs outline outline-gray-900/5 sm:rounded-xl md:col-span-2">
                        <div class="px-4 py-6 sm:p-8">
                            <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                <div class="col-span-full">
                                    <label for="wilayah" class="block text-sm/6 font-medium text-gray-900">Wilayah Pengajuan TUK</label>
                                    <div class="mt-2 grid grid-cols-1">
                                        <select id="wilayah" name="wilayah" autocomplete="wilayah-name" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white py-1.5 pr-8 pl-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                            <option value="">-- Pilih Wilayah --</option>
                                            <option>Wilayah 1</option>
                                            <option>Wilayah 2</option>
                                            <option>Wilayah 3</option>
                                            <option>Wilayah 4</option>
                                            <option>Wilayah 5</option>
                                            <option>Wilayah 6</option>
                                            <option>Wilayah 7</option>
                                        </select>
                                        <svg viewBox="0 0 16 16" fill="currentColor" data-slot="icon" aria-hidden="true" class="pointer-events-none col-start-1 row-start-1 mr-2 size-5 self-center justify-self-end text-gray-500 sm:size-4">
                                            <path d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" fill-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="col-span-full">
                                    <label for="pengusung" class="block text-sm/6 font-medium text-gray-900">Pengusung TUK</label>
                                    <div class="mt-2 grid grid-cols-1">
                                        <select id="pengusung" name="pengusung" autocomplete="pengusung-name" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white py-1.5 pr-8 pl-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                            <option value="">-- Pilih Pengusung --</option>
                                            <option value="bpd">BPD</option>
                                            <option value="mitra">Mitra</option>
                                            <option value="pemerintah">Pemerintah</option>
                                            <option value="pusat">Pusat</option>
                                        </select>
                                        <svg viewBox="0 0 16 16" fill="currentColor" data-slot="icon" aria-hidden="true" class="pointer-events-none col-start-1 row-start-1 mr-2 size-5 self-center justify-self-end text-gray-500 sm:size-4">
                                            <path d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" fill-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="bpd-section" class="grid grid-cols-1 gap-x-8 gap-y-8 py-10 md:grid-cols-3">
                    <div class="px-4 sm:px-0">
                        <h2 class="text-base/7 font-semibold text-gray-900">Pengusung TUK dari BPD</h2>
                        <p class="mt-1 text-sm/6 text-gray-600">Use a permanent address where you can receive mail.</p>
                    </div>
                    <div class="bg-white shadow-xs outline outline-gray-900/5 sm:rounded-xl md:col-span-2">
                        <div class="px-4 py-6 sm:p-8">
                            <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                <div class="col-span-full">
                                    <label for="provinsi" class="block text-sm/6 font-medium text-gray-900">Provinsi (BPD)</label>
                                    <div class="mt-2">
                                    <input id="provinsi" type="text" name="provinsi" autocomplete="provinsi" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="mitra-section" class="grid grid-cols-1 gap-x-8 gap-y-8 py-10 md:grid-cols-3">
                    <div class="px-4 sm:px-0">
                        <h2 class="text-base/7 font-semibold text-gray-900">Pengusung TUK dari Mitra</h2>
                        <p class="mt-1 text-sm/6 text-gray-600">Use a permanent address where you can receive mail.</p>
                    </div>
                    <div class="bg-white shadow-xs outline outline-gray-900/5 sm:rounded-xl md:col-span-2">
                        <div class="px-4 py-6 sm:p-8">
                            <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                <div class="col-span-full">
                                    <label for="nama_instansi" class="block text-sm/6 font-medium text-gray-900">Nama Instansi (MITRA)</label>
                                    <div class="mt-2">
                                    <input id="nama_instansi" type="text" name="nama_instansi" autocomplete="nama_instansi" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="col-span-full">
                                    <label for="email_instansi" class="block text-sm/6 font-medium text-gray-900">Email Instansi (MITRA)</label>
                                    <div class="mt-2">
                                    <input id="email_instansi" type="email_instansi" name="email_instansi" autocomplete="email_instansi" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="col-span-full">
                                    <label for="referensi" class="block text-sm/6 font-medium text-gray-900">Dari mana Anda mengetahui informasi ini? <br/>(sertakan nama dan no hp yang bersangkutan)</label>
                                    <div class="mt-2">
                                    <input id="referensi" type="text" name="referensi" autocomplete="referensi" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="pemerintah-section" class="grid grid-cols-1 gap-x-8 gap-y-8 py-10 md:grid-cols-3">
                    <div class="px-4 sm:px-0">
                        <h2 class="text-base/7 font-semibold text-gray-900">Pengusung TUK dari Pemerintah</h2>
                        <p class="mt-1 text-sm/6 text-gray-600">Use a permanent address where you can receive mail.</p>
                    </div>
                    <div class="bg-white shadow-xs outline outline-gray-900/5 sm:rounded-xl md:col-span-2">
                        <div class="px-4 py-6 sm:p-8">
                            <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                <div class="col-span-full">
                                    <label for="nama_instansi" class="block text-sm/6 font-medium text-gray-900">Nama Instansi (Pemerintah)</label>
                                    <div class="mt-2 grid grid-cols-1">
                                        <select id="nama_instansi" name="nama_instansi" autocomplete="nama_instansi" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white py-1.5 pr-8 pl-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                            <option value="">-- Pilih Instansi --</option>
                                            <option>BJKW 1</option>
                                            <option>BJKW 2</option>
                                            <option>BJKW 3</option>
                                            <option>BJKW 4</option>
                                            <option>BJKW 5</option>
                                            <option>BJKW 6</option>
                                            <option>BJKW 7</option>
                                            <option>Lainnya</option>
                                        </select>
                                        <svg viewBox="0 0 16 16" fill="currentColor" data-slot="icon" aria-hidden="true" class="pointer-events-none col-start-1 row-start-1 mr-2 size-5 self-center justify-self-end text-gray-500 sm:size-4">
                                            <path d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" fill-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-x-8 gap-y-8 py-10 md:grid-cols-3 data-section">
                    <div class="px-4 sm:px-0">
                        <h2 class="text-base/7 font-semibold text-gray-900">Data TUK</h2>
                        <p class="mt-1 text-sm/6 text-gray-600">Use a permanent address where you can receive mail.</p>
                    </div>
                    <div class="bg-white shadow-xs outline outline-gray-900/5 sm:rounded-xl md:col-span-2">
                        <div class="px-4 py-6 sm:p-8">
                            <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                <div class="col-span-full">
                                    <label for="tuk" class="block text-sm/6 font-medium text-gray-900">Nama TUK (nama TUK menggunakan huruf kapital)</label>
                                    <div class="mt-2">
                                    <input id="tuk" type="text" name="tuk" autocomplete="tuk" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="col-span-full">
                                    <label for="alamat" class="block text-sm/6 font-medium text-gray-900">Alamat TUK (Harus memuat Kec, Kab/Kota, Provinsi)</label>
                                    <div class="mt-2">
                                    <input id="alamat" type="text" name="alamat" autocomplete="alamat" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="col-span-full">
                                    <label for="email" class="block text-sm/6 font-medium text-gray-900">Email</label>
                                    <div class="mt-2">
                                    <input id="email" type="email" name="email" autocomplete="email" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="col-span-full">
                                    <label for="pengaju" class="block text-sm/6 font-medium text-gray-900">Nama Pengaju/Ketua TUK <br/>(Nama pengaju akan dijadikan sebagai penanggung jawab TUK)</label>
                                    <div class="mt-2">
                                    <input id="pengaju" type="text" name="pengaju" autocomplete="pengaju" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="col-span-full">
                                    <label for="telepon_pengaju" class="block text-sm/6 font-medium text-gray-900">Nomor Telepon Pengaju <br/>(Pastikan nomor aktif dan dapat menggunakan WhatsApp)</label>
                                    <div class="mt-2">
                                    <input id="telepon_pengaju" type="number" name="telepon_pengaju" autocomplete="telepon_pengaju" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="col-span-full">
                                    <label for="admin" class="block text-sm/6 font-medium text-gray-900">Nama Admin TUK <br/>(Nama pengaju akan dijadikan sebagai penanggung jawab TUK)</label>
                                    <div class="mt-2">
                                    <input id="admin" type="text" name="admin" autocomplete="admin" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="col-span-full">
                                    <label for="telepon_admin" class="block text-sm/6 font-medium text-gray-900">Nomor Telepon Admin TUK <br/>(Pastikan nomor aktif dan dapat menggunakan WhatsApp)</label>
                                    <div class="mt-2">
                                    <input id="telepon_admin" type="number" name="telepon_admin" autocomplete="telepon_admin" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="col-span-full">
                                    <label for="tanggal_uji" class="block text-sm/6 font-medium text-gray-900">Tanggal Rencana Uji</label>
                                    <div class="mt-2">
                                    <input id="tanggal_uji" type="date" name="tanggal_uji" autocomplete="tanggal_uji" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="col-span-full">
                                    <label for="asesi" class="block text-sm/6 font-medium text-gray-900">Rencana Jumlah Asesi</label>
                                    <div class="mt-2">
                                    <input id="asesi" type="number" name="asesi" autocomplete="asesi" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-x-8 gap-y-8 py-10 md:grid-cols-3 data-section">
                    <div class="px-4 sm:px-0">
                        <h2 class="text-base/7 font-semibold text-gray-900">Jenis TUK</h2>
                        <p class="mt-1 text-sm/6 text-gray-600">Use a permanent address where you can receive mail.</p>
                    </div>
                    <div class="bg-white shadow-xs outline outline-gray-900/5 sm:rounded-xl md:col-span-2">
                        <div class="px-4 py-6 sm:p-8">
                            <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                <div class="col-span-full">
                                    <label for="jenis_tuk" class="block text-sm/6 font-medium text-gray-900">Jenis TUK</label>
                                    <div class="mt-2 grid grid-cols-1">
                                        <select id="jenis_tuk" name="jenis_tuk" autocomplete="jenis_tuk" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white py-1.5 pr-8 pl-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                            <option value="">-- Pilih Jenis TUK --</option>
                                            <option value="mandiri">TUK Mandiri</option>
                                            <option value="sewaktu">TUK Sewaktu</option>
                                            <option value="ulang">TUK Sewaktu (Verif. Ulang)</option>
                                        </select>
                                        <svg viewBox="0 0 16 16" fill="currentColor" data-slot="icon" aria-hidden="true" class="pointer-events-none col-start-1 row-start-1 mr-2 size-5 self-center justify-self-end text-gray-500 sm:size-4">
                                            <path d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" fill-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="mandiri-section" class="grid grid-cols-1 gap-x-8 gap-y-8 py-10 md:grid-cols-3">
                    <div class="px-4 sm:px-0">
                        <h2 class="text-base/7 font-semibold text-gray-900">Dokumen TUK Mandiri</h2>
                        <p class="mt-1 text-sm/6 text-gray-600">Use a permanent address where you can receive mail.</p>
                    </div>
                    <div class="bg-white shadow-xs outline outline-gray-900/5 sm:rounded-xl md:col-span-2">
                        <div class="px-4 py-6 sm:p-8">
                            <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                <div class="col-span-full">
                                    <label for="dokumen_pengajuan" class="block text-sm/6 font-medium text-gray-900">Upload Dokumen Pengajuan TUK Mandiri</label>
                                    <div class="mt-2">
                                    <input id="dokumen_pengajuan" type="file" name="dokumen_pengajuan" autocomplete="dokumen_pengajuan" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="col-span-full">
                                    <label for="dokumentasi" class="block text-sm/6 font-medium text-gray-900">Upload  dokumentasi foto kondisi TUK Mandiri dan peralatan yang tersedia</label>
                                    <div class="mt-2">
                                    <input id="dokumentasi" type="file" name="dokumentasi" autocomplete="dokumentasi" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="sewaktu-section" class="grid grid-cols-1 gap-x-8 gap-y-8 py-10 md:grid-cols-3">
                    <div class="px-4 sm:px-0">
                        <h2 class="text-base/7 font-semibold text-gray-900">Dokumen TUK Sewaktu</h2>
                        <p class="mt-1 text-sm/6 text-gray-600">Use a permanent address where you can receive mail.</p>
                    </div>
                    <div class="bg-white shadow-xs outline outline-gray-900/5 sm:rounded-xl md:col-span-2">
                        <div class="px-4 py-6 sm:p-8">
                            <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                <div class="col-span-full">
                                    <label for="dokumen_pengajuan" class="block text-sm/6 font-medium text-gray-900">Upload Dokumen Pengajuan TUK Sewaktu</label>
                                    <div class="mt-2">
                                    <input id="dokumen_pengajuan" type="file" name="dokumen_pengajuan" autocomplete="dokumen_pengajuan" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="col-span-full">
                                    <label for="perjanjian" class="block text-sm/6 font-medium text-gray-900">Upload Dokumen Perjanjian TUK Sewaktu</label>
                                    <div class="mt-2">
                                    <input id="perjanjian" type="file" name="perjanjian" autocomplete="perjanjian" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="col-span-full">
                                    <label for="dokumentasi" class="block text-sm/6 font-medium text-gray-900">Upload dokumentasi foto kondisi TUK Sewaktu dan peralatan yang tersedia</label>
                                    <div class="mt-2">
                                    <input id="dokumentasi" type="file" name="dokumentasi" autocomplete="dokumentasi" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="ulang-section" class="grid grid-cols-1 gap-x-8 gap-y-8 py-10 md:grid-cols-3">
                    <div class="px-4 sm:px-0">
                        <h2 class="text-base/7 font-semibold text-gray-900">Dokumen TUK Sewaktu (Verif Ulang)</h2>
                        <p class="mt-1 text-sm/6 text-gray-600">Use a permanent address where you can receive mail.</p>
                    </div>
                    <div class="bg-white shadow-xs outline outline-gray-900/5 sm:rounded-xl md:col-span-2">
                        <div class="px-4 py-6 sm:p-8">
                            <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                <div class="col-span-full">
                                    <label for="dokumen_pengajuan" class="block text-sm/6 font-medium text-gray-900">Upload Dokumen Pengajuan TUK Sewaktu (verif ulang)</label>
                                    <div class="mt-2">
                                    <input id="dokumen_pengajuan" type="file" name="dokumen_pengajuan" autocomplete="dokumen_pengajuan" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="col-span-full">
                                    <label for="dokumentasi" class="block text-sm/6 font-medium text-gray-900">Upload dokumentasi foto kondisi TUK Sewaktu dan peralatan yang tersedia (verif ulang)</label>
                                    <div class="mt-2">
                                    <input id="dokumentasi" type="file" name="dokumentasi" autocomplete="dokumentasi" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-end gap-x-6 border border-gray-900/10 px-4 py-4 sm:px-8">
                            <button type="button" class="text-sm font-semibold text-gray-900">Close</button>
                            <button type="submit" class="rounded-md bg-violet-600 px-3 py-2 text-sm font-semibold text-white shadow-xs">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const pengusungSelect = document.getElementById("pengusung");
        const bpdSection = document.getElementById("bpd-section");
        const mitraSection = document.getElementById("mitra-section");
        const pemerintahSection = document.getElementById("pemerintah-section");
        const dataSection = document.getElementsByClassName("data-section");

        const tukSelect = document.getElementById("jenis_tuk")
        const mandiriSection = document.getElementById("mandiri-section");
        const sewaktuSection = document.getElementById("sewaktu-section");
        const ulangSection = document.getElementById("ulang-section");

        function toggleTUK(value) {
            mandiriSection.classList.add("hidden")
            sewaktuSection.classList.add("hidden")
            ulangSection.classList.add("hidden")

            if (value === "mandiri") {
                mandiriSection.classList.remove("hidden");
            } else if (value === "sewaktu") {
                sewaktuSection.classList.remove("hidden");
            } else if (value === "ulang") {
                ulangSection.classList.remove("hidden");
            }
        }

        function toggleSections(value) {
            // Hide all sections first
            bpdSection.classList.add("hidden");
            mitraSection.classList.add("hidden");
            pemerintahSection.classList.add("hidden");
            for (let section of dataSection) {
                section.classList.add("hidden");
            }

            if (value) {
                // Show the general data-section if any pengusung selected
                for (let section of dataSection) {
                    section.classList.remove("hidden");
                }
            }

            // Show only the selected pengusung section
            if (value === "bpd") {
                bpdSection.classList.remove("hidden");
            } else if (value === "mitra") {
                mitraSection.classList.remove("hidden");
            } else if (value === "pemerintah") {
                pemerintahSection.classList.remove("hidden");
            }
        }

        // Run once on page load (useful if editing existing data)
        toggleSections(pengusungSelect.value);
        toggleTUK(tukSelect.value)

        // Run every time user changes the dropdown
        pengusungSelect.addEventListener("change", function () {
            toggleSections(this.value);
        });
        tukSelect.addEventListener("change", function () {
            toggleTUK(this.value)
        })
    });
    </script>
</body>
</html>