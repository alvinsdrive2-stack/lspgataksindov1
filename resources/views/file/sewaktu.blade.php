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
                    Penugasan Verifikator TUK - Admin LSP
                </a>
                <a href="logout" class="py-2 px-1 bg-red-600 rounded text-center font-semibold text-gray-800 dark:text-gray-200 leading-tight">Logout</a>
            </div>
        </header>
        <div class="bg-gray-900 min-h-screen flex flex-col justify-center items-center">
            @if (session('success'))
                <div class="max-w-[1200px] text-gray-900 mt-10 mx-auto bg-green-400 p-3 rounded-md">
                    <p>{!! session('success') !!}</p>
                </div>
            @endif
            <div class="bg-gray-800 rounded-xl text-white m-10">
                <form action="{{ route('createFileSewaktu') }}" method="POST" class="flex flex-col text-center justify-center items-center p-8">
                    @csrf
                    <h1 class="text-2xl font-bold tracking-normal capitalize mb-8">data verifikasi tuk</h1>
                    <div class="container">
                        <label for="nomor" class="form-control w-full max-w-xs">
                            <div class="label">
                                <span class="label-text text-white">Nomor Surat</span>
                            </div>
                            <input type="number" id="nomor" name="nomor" placeholder="Type here" class="input text-black dark:text-white input-bordered w-full max-w-xs" />
                        </label>
                        <label for="tuk" class="form-control w-full max-w-xs">
                            <div class="label">
                                <span class="label-text text-white">Nama TUK</span>
                            </div>
                            <input type="text" id="tuk" name="tuk" placeholder="Type here" class="input text-black dark:text-white input-bordered w-full max-w-xs" />
                        </label>
                        <label for="jenisTUK" class="form-control w-full max-w-xs">
                            <div class="label">
                                <span class="label-text text-white">Jenis TUK</span>
                            </div>
                            <select id="jenisTUK" name="jenisTUK" class="select select-bordered">
                                <option disabled selected>Pick one</option>
                                <option value="Sewaktu">TUK Sewaktu</option>
                                <option value="Mandiri">TUK Mandiri</option>
                            </select>
                        </label>
                        <label for="tanggal_asesmen" class="form-control w-full max-w-xs">
                            <div class="label">
                                <span class="label-text text-white">Tanggal Asesmen</span>
                            </div>
                            <input type="date" id="tanggal_asesmen" name="tanggal_asesmen" placeholder="Type here" class="input text-black dark:text-white input-bordered w-full max-w-xs" />
                        </label>
                        <label for="tanggal_verifikasi" class="form-control w-full max-w-xs">
                            <div class="label">
                                <span class="label-text text-white">Tanggal Verifikasi</span>
                            </div>
                            <input type="date" id="tanggal_verifikasi" name="tanggal_verifikasi" placeholder="Type here" class="input text-black dark:text-white input-bordered w-full max-w-xs" />
                        </label>
                        <label for="metodeVerif" class="form-control w-full max-w-xs">
                            <div class="label">
                                <span class="label-text text-white">Metode Verifikasi</span>
                            </div>
                            <select id="metodeVerif" name="metodeVerif" class="select select-bordered">
                                <option disabled selected>Pick one</option>
                                <option value="Luring">Luring</option>
                                <option value="Daring">Daring</option>
                            </select>
                        </label>
                        <label for="alamat" class="form-control w-full max-w-xs">
                            <div class="label">
                                <span class="label-text text-white">Alamat</span>
                            </div>
                            <input type="text" id="alamat" name="alamat" placeholder="Type here" class="input text-black dark:text-white input-bordered w-full max-w-xs" />
                        </label>
                        <label for="peserta" class="form-control w-full max-w-xs">
                            <div class="label">
                                <span class="label-text text-white">Jumlah Peserta</span>
                            </div>
                            <input type="number" id="peserta" name="peserta" placeholder="Type here" class="input text-black dark:text-white input-bordered w-full max-w-xs" />
                        </label>
                        <label for="met1" class="form-control w-full max-w-xs">
                            <div class="label">
                                <span class="label-text text-white">Validator</span>
                            </div>
                            <input list="asesor" type="text" id="met1" name="met1" placeholder="Type here" class="input text-black dark:text-white input-bordered w-full max-w-xs" />
                            <datalist id="asesor">
                                @foreach ($allAsesor as $asesor)
                                    <option value="{{ $asesor->Noreg }}">{{ $asesor->Nama }}</option>
                                @endforeach
                            </datalist>
                        </label>
                        <label for="ketua" class="form-control w-full max-w-xs">
                            <div class="label">
                                <span class="label-text text-white">Ketua TUK</span>
                            </div>
                            <input list="ketua_tuk" type="text" id="ketua" name="ketua_tuk" placeholder="Ketik nama ketua TUK..." class="input text-black dark:text-white input-bordered w-full max-w-xs" />
                            <datalist id="ketua_tuk">
                                @if($ketuaTukList->count() > 0)
                                    @foreach ($ketuaTukList as $ketua)
                                        <option value="{{ $ketua->name }}">{{ $ketua->nama_tuk }}</option>
                                    @endforeach
                                @endif
                            </datalist>
                        </label>
                        <label for="asesor" class="form-control w-full max-w-xs">
                            <div class="label">
                                <span class="label-text text-white">Verifikator</span>
                            </div>
                            <input list="asesor" type="text" id="asesor" name="asesor" placeholder="Type here" class="input text-black dark:text-white input-bordered w-full max-w-xs" />
                        </label>
                        <label for="admin" class="form-control w-full max-w-xs">
                            <div class="label">
                                <span class="label-text text-white">Admin TUK</span>
                            </div>
                            <input type="text" id="admin" name="admin" placeholder="Type here" class="input text-black dark:text-white input-bordered w-full max-w-xs" />
                        </label>
                        <label for="skema1" class="form-control w-full max-w-xs">
                            <div class="label">
                                <span class="label-text text-white">Skema (1)</span>
                            </div>
                            <input list="jabker" type="text" id="skema1" name="skema[]" placeholder="Type here" class="input text-black dark:text-white input-bordered w-full max-w-xs" />
                            <datalist id="jabker">
                                @foreach ($allJabker as $jabker)
                                    <option value="{{ $jabker->jabatan_kerja }}">{{ $jabker->id_jabatan_kerja }}</option>
                                @endforeach
                            </datalist>
                        </label>
                        <label for="jenjang1" class="form-control w-full max-w-xs">
                            <div class="label">
                                <span class="label-text text-white">Jenjang (1)</span>
                            </div>
                            <input type="number" id="jenjang1" name="jenjang[]" placeholder="Type here" class="input text-black dark:text-white input-bordered w-full max-w-xs" />
                        </label>
                        <label for="metode1" class="form-control w-full max-w-xs">
                            <div class="label">
                                <span class="label-text text-white">Metode Asesmen (1)</span>
                            </div>
                            <select id="metode1" name="metode[]" class="select select-bordered">
                                <option disabled selected>Pick one</option>
                                <option value="Observasi">Observasi</option>
                                <option value="Portofolio">Portofolio</option>
                                <option value="Observasi & Portofolio">Observasi & Portofolio</option>
                            </select>
                        </label>
                        <div class="container" id="skemaContainer">
                            
                        </div>
                        <button type="button" id="addSkemaBtn" class="mt-2 bg-green-500 hover:bg-green-400 text-white py-2 px-4 rounded">Add Skema</button>
                    </div>
                    <button type="submit" class="flex-none h-10 mb-2 mt-10 rounded-md bg-indigo-500 px-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500">Submit</button>
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

            let skemaCount = 1;

            document.getElementById('addSkemaBtn').addEventListener('click', function() {
                skemaCount++;
                const container = document.getElementById('skemaContainer');

                const newField = `
                    <label for="skema${skemaCount}" class="form-control w-full max-w-xs">
                        <div class="label">
                            <span class="label-text text-white">Skema (${skemaCount})</span>
                        </div>
                        <input list="jabker" type="text" id="skema${skemaCount}" name="skema[]" placeholder="Type here" class="input text-black dark:text-white input-bordered w-full max-w-xs" />
                    </label>
                    <label for="jenjang${skemaCount}" class="form-control w-full max-w-xs">
                        <div class="label">
                            <span class="label-text text-white">Jenjang (${skemaCount})</span>
                        </div>
                        <input type="text" id="jenjang${skemaCount}" name="jenjang[]" placeholder="Type here" class="input text-black dark:text-white input-bordered w-full max-w-xs" />
                    </label>
                    <label for="metode${skemaCount}" class="form-control w-full max-w-xs">
                        <div class="label">
                            <span class="label-text text-white">Metode Asesmen (${skemaCount})</span>
                        </div>
                        <select id="metode${skemaCount}" name="metode[]" class="select select-bordered">
                            <option disabled selected>Pick one</option>
                            <option value="Observasi">Observasi</option>
                            <option value="Portofolio">Portofolio</option>
                            <option value="Observasi & Portofolio">Observasi & Portofolio</option>
                        </select>
                    </label>`;

                container.insertAdjacentHTML('beforeend', newField);
            });

            // Enhanced autocomplete for ketua TUK
            const ketuaInput = document.getElementById('ketua');
            const ketuaDatalist = document.getElementById('ketua_tuk');

            // Set active ketua based on selection
            ketuaInput.addEventListener('input', function() {
                const selectedValue = this.value;
                const options = ketuaDatalist.querySelectorAll('option');

                options.forEach(option => {
                    if (option.value === selectedValue) {
                        this.setAttribute('data-tuk-name', option.textContent);
                    }
                });
            });

            // Auto-complete on paste/type
            ketuaInput.addEventListener('change', function() {
                if (this.value && !this.getAttribute('data-tuk-name')) {
                    // Try to find exact match
                    const options = ketuaDatalist.querySelectorAll('option');
                    for (let option of options) {
                        if (option.value.toLowerCase() === this.value.toLowerCase()) {
                            this.setAttribute('data-tuk-name', option.textContent);
                            break;
                        }
                    }
                }
            });
        </script>
    </body>
</html>