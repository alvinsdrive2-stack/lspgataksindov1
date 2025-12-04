<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Verifikasi TUK Sewaktu - LSP LPK Gataksindo</title>
        <style>
            .container {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                gap: 30px; /* Adds space between labels */
            }
            .check .label-text {
                display: inline-block;
                width: 220px;
            }
            .checkbox {
                vertical-align: middle;
            }

            .forms {
                padding-bottom: 20px;
                display: flex;
                flex-wrap: wrap;
            }

            Modal styles .modalpdf {
                display: none;
                position: fixed;
                z-index: 1000;
                width: auto;
                height: auto;
                background-color: rgba(0, 0, 0, 0.5);
            }

            .modal-content {
                padding: 20px;
                width: 900px;
                height: auto;
                cursor: move;
                position: absolute;
            }

            .close {
                color: #aaa;
                float: right;
                font-size: 28px;
                font-weight: bold;
            }

            .close:hover,
            .close:focus {
                color: #454444;
                text-decoration: none;
                cursor: pointer;
            }
            .holder {
                display: none;
                position: fixed;
                left: 25%;
                top: 25%;
                -ms-transform: translate(-50%, -50%);
                -moz-transform: translate(-50%, -50%);
                -webkit-transform: translate(-50%, -50%);
                transform: translate(-50%, -50%);
                min-height: max-content;
                border: 1px solid white;
            }
            .contentpdf,
            .holder,
            .popup {
                padding: 0;
                margin: 0;
            }
            .holder,
            .modal-content {
                border-radius: 15px;
            }

            .modal-content {
                min-width: 100%;
            }
        </style>
        @vite('resources/css/app.css')
    </head>
    <body>
        <header class="bg-white dark:bg-gray-800 shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between">
                <a href="/sewaktu" class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Verifikasi TUK - Verifikator TUK
                </a>
                <a href="{{ route('logout') }}" class="py-2 px-1 bg-red-600 rounded text-center font-semibold text-gray-800 dark:text-gray-200 leading-tight">Logout</a>
            </div>
        </header>
        <div class="bg-gray-900 min-h-screen flex flex-col justify-center items-center">
            @if (session('success'))
                <div class="max-w-[1200px] text-gray-900 mt-10 mx-auto bg-green-400 p-3 rounded-md">
                    <p>{!! session('success') !!}</p>
                </div>
            @endif
            <div class="bg-gray-800 rounded-xl text-white m-10">
                <form action="{{ route('verify') }}" method="POST" class="flex flex-col text-center justify-center items-center p-8 gap-10">
                    @csrf
                    <input type="hidden" name="id" value="{{ $id }}">
                    <div>
                        <h1 class="text-2xl font-bold tracking-normal">STANDAR PERSYARATAN JABATAN KERJA</h1>
                    </div>
                    <div class="flex justify-center items-center gap-10 flex-wrap check">
                        <div class="form-control">
                            <label for="gedung" class="label cursor-pointer">
                                <span class="text-white label-text mr-3">Lokasi Gedung Dengan Akses<br>Masuk & Keluar</span>
                                <input name="gedung" id="gedung" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                            </label>
                        </div>
                        <div class="form-control">
                            <label for="bangunan" class="label cursor-pointer">
                                <span class="text-white label-text mr-3">Kondisi Bangunan Baik Dengan<br>Penerangan Yang Cukup</span>
                                <input name="bangunan" id="bangunan" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                            </label>
                        </div>
                        <div class="form-control">
                            <label for="ruangan" class="label cursor-pointer">
                                <span class="text-white label-text mr-3">Ruangan Uji Tulis Sesuai<br>Kapasitas Asesi</span>
                                <input name="ruangan" id="ruangan" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                            </label>
                        </div>
                        <div class="form-control">
                            <label for="internet" class="label cursor-pointer">
                                <span class="text-white label-text mr-3">Jaringan Internet Min. 10 Mbps</span>
                                <input name="internet" id="internet" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                            </label>
                        </div>
                        <div class="form-control">
                            <label for="proyektor" class="label cursor-pointer">
                                <span class="text-white label-text mr-3">Monitor / Proyektor</span>
                                <input name="proyektor" id="proyektor" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                            </label>
                        </div>
                        <div class="form-control">
                            <label for="pc" class="label cursor-pointer">
                                <span class="text-white label-text mr-3">Laptop / Komputer Min. 1</span>
                                <input name="pc" id="pc" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                            </label>
                        </div>
                        <div class="form-control">
                            <label for="mejaasesor" class="label cursor-pointer">
                                <span class="text-white label-text mr-3">Meja Dan Kursi Asesor</span>
                                <input name="mejaasesor" id="mejaasesor" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                            </label>
                        </div>
                        <div class="form-control">
                            <label for="mejaasesi" class="label cursor-pointer">
                                <span class="text-white label-text mr-3">Meja Dan Kursi Asesi</span>
                                <input name="mejaasesi" id="mejaasesi" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                            </label>
                        </div>
                        <div class="form-control">
                            <label for="komunikasi" class="label cursor-pointer">
                                <span class="text-white label-text mr-3">Alat Komunikasi (HT/HP)</span>
                                <input name="komunikasi" id="komunikasi" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                            </label>
                        </div>
                        <div class="form-control">
                            <label for="dokumentasi" class="label cursor-pointer">
                                <span class="text-white label-text mr-3">Alat Dokumentasi (HP/Kamera)</span>
                                <input name="dokumentasi" id="dokumentasi" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                            </label>
                        </div>
                        <div class="form-control">
                            <label for="pulpen" class="label cursor-pointer">
                                <span class="text-white label-text mr-3">Pulpen</span>
                                <input name="pulpen" id="pulpen" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                            </label>
                        </div>
                        <div class="form-control">
                            <label for="pensil" class="label cursor-pointer">
                                <span class="text-white label-text mr-3">Pensil</span>
                                <input name="pensil" id="pensil" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                            </label>
                        </div>
                        <div class="form-control">
                            <label for="tipex" class="label cursor-pointer">
                                <span class="text-white label-text mr-3">Correction Tape</span>
                                <input name="tipex" id="tipex" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                            </label>
                        </div>
                        <div class="form-control">
                            <label for="penghapus" class="label cursor-pointer">
                                <span class="text-white label-text mr-3">Penghapus Pensil</span>
                                <input name="penghapus" id="penghapus" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                            </label>
                        </div>
                        <div class="form-control">
                            <label for="spidol" class="label cursor-pointer">
                                <span class="text-white label-text mr-3">Spidol</span>
                                <input name="spidol" id="spidol" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                            </label>
                        </div>
                        <div class="form-control">
                            <label for="penggaris" class="label cursor-pointer">
                                <span class="text-white label-text mr-3">Penggaris Min. 30cm</span>
                                <input name="penggaris" id="penggaris" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                            </label>
                        </div>
                        <div class="form-control">
                            <label for="hvs" class="label cursor-pointer">
                                <span class="text-white label-text mr-3">HVS A4</span>
                                <input name="hvs" id="hvs" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                            </label>
                        </div>
                        <div class="form-control">
                            <label for="apd" class="label cursor-pointer">
                                <span class="text-white label-text mr-3">APD</span>
                                <input name="apd" id="apd" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                            </label>
                        </div>
                        <div class="form-control">
                            <label for="apk" class="label cursor-pointer">
                                <span class="text-white label-text mr-3">APK</span>
                                <input name="apk" id="apk" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                            </label>
                        </div>
                        <div class="form-control">
                            <label for="p3k" class="label cursor-pointer">
                                <span class="text-white label-text mr-3">P3K</span>
                                <input name="p3k" id="p3k" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                            </label>
                        </div>
                        <div class="form-control">
                            <label for="rambu" class="label cursor-pointer">
                                <span class="text-white label-text mr-3">Rambu Keselamatan Kerja</span>
                                <input name="rambu" id="rambu" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                            </label>
                        </div>
                    </div>
                    <label for="pendingin" class="form-control w-full max-w-xs">
                        <div class="label">
                            <span class="label-text text-white">Spesifikasi Pendingin Ruangan</span>
                        </div>
                        <select id="pendingin" name="pendingin" class="select select-bordered">
                            <option disabled selected>Pick one</option>
                            <option value="1/2pk">1/2 PK</option>
                            <option value="3/4pk">3/4 PK</option>
                            <option value="1pk">1 PK</option>
                            <option value="1,5pk">1.5 PK</option>
                            <option value="kipas">Kipas Angin / Kipas Blower</option>
                        </select>
                    </label>
                    @if (!empty($allPeralatan))
                        <h1 class="text-2xl font-bold tracking-normal">PERALATAN PRAKTIK</h1>
                        <a href="#proyek-kerja" onclick="openModal('../daftarperalatan.pdf')">Lihat Panduan</a>
                        <div class="flex justify-center items-center gap-10 flex-wrap check">
                            <div class="form-control">
                                <label for="theodolite" class="label cursor-pointer">
                                    <span class="text-white label-text mr-3">Theodolite</span>
                                    <input name="theodolite" id="theodolite" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                                </label>
                            </div>
                            <div class="form-control">
                                <label for="meteran" class="label cursor-pointer">
                                    <span class="text-white label-text mr-3">Meteran</span>
                                    <input name="meteran" id="meteran" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                                </label>
                            </div>
                            <div class="form-control">
                                <label for="waterpass" class="label cursor-pointer">
                                    <span class="text-white label-text mr-3">Waterpass</span>
                                    <input name="waterpass" id="waterpass" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                                </label>
                            </div>
                            <div class="form-control">
                                <label for="autocad" class="label cursor-pointer">
                                    <span class="text-white label-text mr-3">Autocad</span>
                                    <input name="autocad" id="autocad" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                                </label>
                            </div>
                            <div class="form-control">
                                <label for="perancah" class="label cursor-pointer">
                                    <span class="text-white label-text mr-3">Perancah</span>
                                    <input name="perancah" id="perancah" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                                </label>
                            </div>
                            <div class="form-control">
                                <label for="bouwplank" class="label cursor-pointer">
                                    <span class="text-white label-text mr-3">Bouwplank</span>
                                    <input name="bouwplank" id="bouwplank" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                                </label>
                            </div>
                            <div class="form-control">
                                <label for="patok" class="label cursor-pointer">
                                    <span class="text-white label-text mr-3">Patok / Bench Mark</span>
                                    <input name="patok" id="patok" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                                </label>
                            </div>
                            <div class="form-control">
                                <label for="jidar" class="label cursor-pointer">
                                    <span class="text-white label-text mr-3">Jidar</span>
                                    <input name="jidar" id="jidar" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                                </label>
                            </div>
                            <div class="form-control">
                                <label for="bandul" class="label cursor-pointer">
                                    <span class="text-white label-text mr-3">Lot / Bandul</span>
                                    <input name="bandul" id="bandul" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                                </label>
                            </div>
                            <div class="form-control">
                                <label for="palu_karet" class="label cursor-pointer">
                                    <span class="text-white label-text mr-3">Palu Karet</span>
                                    <input name="palu_karet" id="palu_karet" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                                </label>
                            </div>
                            <div class="form-control">
                                <label for="palu_besi" class="label cursor-pointer">
                                    <span class="text-white label-text mr-3">Palu Besi</span>
                                    <input name="palu_besi" id="palu_besi" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                                </label>
                            </div>
                            <div class="form-control">
                                <label for="tang_jepit" class="label cursor-pointer">
                                    <span class="text-white label-text mr-3">Tang Jepit</span>
                                    <input name="tang_jepit" id="tang_jepit" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                                </label>
                            </div>
                            <div class="form-control">
                                <label for="tang_potong" class="label cursor-pointer">
                                    <span class="text-white label-text mr-3">Tang Potong</span>
                                    <input name="tang_potong" id="tang_potong" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                                </label>
                            </div>
                            <div class="form-control">
                                <label for="gergaji_kayu" class="label cursor-pointer">
                                    <span class="text-white label-text mr-3">Gergaji Kayu</span>
                                    <input name="gergaji_kayu" id="gergaji_kayu" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                                </label>
                            </div>
                            <div class="form-control">
                                <label for="gergaji_besi" class="label cursor-pointer">
                                    <span class="text-white label-text mr-3">Gergaji Besi</span>
                                    <input name="gergaji_besi" id="gergaji_besi" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                                </label>
                            </div>
                            <div class="form-control">
                                <label for="gerinda" class="label cursor-pointer">
                                    <span class="text-white label-text mr-3">Mesin Gerinda</span>
                                    <input name="gerinda" id="gerinda" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                                </label>
                            </div>
                            <div class="form-control">
                                <label for="pembengkok" class="label cursor-pointer">
                                    <span class="text-white label-text mr-3">Pembengkok Besi</span>
                                    <input name="pembengkok" id="pembengkok" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                                </label>
                            </div>
                            <div class="form-control">
                                <label for="pahat" class="label cursor-pointer">
                                    <span class="text-white label-text mr-3">Pahat Kayu</span>
                                    <input name="pahat" id="pahat" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                                </label>
                            </div>
                            <div class="form-control">
                                <label for="obeng" class="label cursor-pointer">
                                    <span class="text-white label-text mr-3">Obeng</span>
                                    <input name="obeng" id="obeng" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                                </label>
                            </div>
                            <div class="form-control">
                                <label for="cangkul" class="label cursor-pointer">
                                    <span class="text-white label-text mr-3">Cangkul / Sekop</span>
                                    <input name="cangkul" id="cangkul" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                                </label>
                            </div>
                            <div class="form-control">
                                <label for="sendok_semen" class="label cursor-pointer">
                                    <span class="text-white label-text mr-3">Sendok Semen</span>
                                    <input name="sendok_semen" id="sendok_semen" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                                </label>
                            </div>
                            <div class="form-control">
                                <label for="ember" class="label cursor-pointer">
                                    <span class="text-white label-text mr-3">Ember</span>
                                    <input name="ember" id="ember" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                                </label>
                            </div>
                            <div class="form-control">
                                <label for="pengerik" class="label cursor-pointer">
                                    <span class="text-white label-text mr-3">Alat Pengerik / Kape</span>
                                    <input name="pengerik" id="pengerik" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                                </label>
                            </div>
                            <div class="form-control">
                                <label for="roll_cat" class="label cursor-pointer">
                                    <span class="text-white label-text mr-3">Kuas Roll Cat</span>
                                    <input name="roll_cat" id="roll_cat" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                                </label>
                            </div>
                            <div class="form-control">
                                <label for="cat" class="label cursor-pointer">
                                    <span class="text-white label-text mr-3">Cat</span>
                                    <input name="cat" id="cat" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                                </label>
                            </div>
                            <div class="form-control">
                                <label for="kuas_cat" class="label cursor-pointer">
                                    <span class="text-white label-text mr-3">Kuas Cat</span>
                                    <input name="kuas_cat" id="kuas_cat" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                                </label>
                            </div>
                            <div class="form-control">
                                <label for="nampan" class="label cursor-pointer">
                                    <span class="text-white label-text mr-3">Nampan Cat</span>
                                    <input name="nampan" id="nampan" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                                </label>
                            </div>
                            <div class="form-control">
                                <label for="benang" class="label cursor-pointer">
                                    <span class="text-white label-text mr-3">Benang</span>
                                    <input name="benang" id="benang" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                                </label>
                            </div>
                            <div class="form-control">
                                <label for="paku" class="label cursor-pointer">
                                    <span class="text-white label-text mr-3">Paku</span>
                                    <input name="paku" id="paku" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                                </label>
                            </div>
                            <div class="form-control">
                                <label for="ampelas" class="label cursor-pointer">
                                    <span class="text-white label-text mr-3">Ampelas</span>
                                    <input name="ampelas" id="ampelas" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                                </label>
                            </div>
                            <div class="form-control">
                                <label for="triplek" class="label cursor-pointer">
                                    <span class="text-white label-text mr-3">Triplek</span>
                                    <input name="triplek" id="triplek" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                                </label>
                            </div>
                            <div class="form-control">
                                <label for="lakban" class="label cursor-pointer">
                                    <span class="text-white label-text mr-3">Masking Tape / Lakban</span>
                                    <input name="lakban" id="lakban" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                                </label>
                            </div>
                            <div class="form-control">
                                <label for="dempul" class="label cursor-pointer">
                                    <span class="text-white label-text mr-3">Dempul</span>
                                    <input name="dempul" id="dempul" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                                </label>
                            </div>
                            <div class="form-control">
                                <label for="papan_applicator" class="label cursor-pointer">
                                    <span class="text-white label-text mr-3">Papan Applicator</span>
                                    <input name="papan_applicator" id="papan_applicator" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                                </label>
                            </div>
                            <div class="form-control">
                                <label for="mesin_bor" class="label cursor-pointer">
                                    <span class="text-white label-text mr-3">Mesin Bor</span>
                                    <input name="mesin_bor" id="mesin_bor" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                                </label>
                            </div>
                            <div class="form-control">
                                <label for="mesin_serut" class="label cursor-pointer">
                                    <span class="text-white label-text mr-3">Mesin Serut</span>
                                    <input name="mesin_serut" id="mesin_serut" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                                </label>
                            </div>
                            <div class="form-control">
                                <label for="mesin_gergaji" class="label cursor-pointer">
                                    <span class="text-white label-text mr-3">Mesin Gergaji</span>
                                    <input name="mesin_gergaji" id="mesin_gergaji" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                                </label>
                            </div>
                            <div class="form-control">
                                <label for="penggaris_siku" class="label cursor-pointer">
                                    <span class="text-white label-text mr-3">Penggaris Siku</span>
                                    <input name="penggaris_siku" id="penggaris_siku" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                                </label>
                            </div>
                            <div class="form-control">
                                <label for="triplek" class="label cursor-pointer">
                                    <span class="text-white label-text mr-3">Triplek</span>
                                    <input name="triplek" id="triplek" type="checkbox" value="Yes" class="checkbox checkbox-primary" />
                                </label>
                            </div>
                        </div>
                    @endif
                    <button type="submit" class="flex-none h-10 mb-2 rounded-md bg-indigo-500 px-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500">Submit</button>
                </form>
            </div>
            <form class="holder" id="holder" method="POST" enctype="multipart/form-data">
                <div class="popup forms">
                    <div id="pdfModal" class="contentpdf modalpdf">
                        <div class="modal-content bg-gray-900" id="draggable">
                            <span class="close" onclick="closeModal()">&times;</span>
                            <iframe id="pdfViewer" src="" width="800px" height="480px"></iframe>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        

        <script>
            function openModal(pdfUrl) {
                var pdfViewer = document.getElementById('pdfViewer');
                var holder = document.getElementById('holder');
                pdfViewer.src = pdfUrl;
                holder.style.display = 'block';
            }

            function closeModal() {
                var holder = document.getElementById('holder');
                var pdfViewer = document.getElementById('pdfViewer');
                pdfViewer.src = '';
                holder.style.display = 'none';
            }

            const modal = document.getElementById("draggable");
            let isDragging = false;
            let offsetX = 0;
            let offsetY = 0;

            // Mouse down event to start dragging
            modal.addEventListener("mousedown", function (e) {
                isDragging = true;
                offsetX = e.clientX - modal.offsetLeft;
                offsetY = e.clientY - modal.offsetTop;
                modal.style.position = "absolute";
            });

            // Mouse move event to drag the modal
            document.addEventListener("mousemove", function (e) {
                if (isDragging) {
                    modal.style.left = `${e.clientX - offsetX}px`;
                    modal.style.top = `${e.clientY - offsetY}px`;
                }
            });

            // Mouse up event to stop dragging
            document.addEventListener("mouseup", function () {
                isDragging = false;
            });

            let skemaCount = 2;

            document.getElementById('addSkemaBtn').addEventListener('click', function() {
                skemaCount++;
                const container = document.getElementById('skemaContainer');

                const newField = `
                    <label for="skema${skemaCount}" class="form-control w-full max-w-xs">
                        <div class="label">
                            <span class="label-text text-white">Skema (${skemaCount})</span>
                        </div>
                        <input list="jabker" type="text" id="skema${skemaCount}" name="skema[]" placeholder="Type here" class="input text-black dark:text-white input-bordered w-full max-w-xs" />
                    </label>`;

                container.insertAdjacentHTML('beforeend', newField);
            });
        </script>
    </body>
</html>