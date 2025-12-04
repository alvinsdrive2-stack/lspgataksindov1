<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Verifikasi TUK Mandiri - LSP LPK Gataksindo</title>
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
        </style>
        @vite('resources/css/app.css')
    </head>
    <body class="bg-gray-900 min-h-screen flex flex-col justify-center items-center">
        <div class="bg-gray-800 rounded-xl text-white m-10">
            <form action="{{ route('createFileMandiri') }}" method="POST" class="flex flex-col text-center justify-center items-center p-8 gap-10">
                @csrf
                <h1 class="text-2xl font-bold tracking-normal">DATA KLASIFIKASI KUALIFIKASI</h1>
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
                    <label for="tanggal1" class="form-control w-full max-w-xs">
                        <div class="label">
                            <span class="label-text text-white">Tanggal Asesmen</span>
                        </div>
                        <input type="date" id="tanggal1" name="tanggal1" placeholder="Type here" class="input text-black dark:text-white input-bordered w-full max-w-xs" />
                    </label>
                    <label for="alamat" class="form-control w-full max-w-xs">
                        <div class="label">
                            <span class="label-text text-white">Alamat</span>
                        </div>
                        <input type="text" id="alamat" name="alamat" placeholder="Type here" class="input text-black dark:text-white input-bordered w-full max-w-xs" />
                    </label>
                    <div class="container" id="subklasContainer">
                        <label for="peserta" class="form-control w-full max-w-xs">
                            <div class="label">
                                <span class="label-text text-white">Jumlah Peserta</span>
                            </div>
                            <input type="number" id="peserta" name="peserta" placeholder="Type here" class="input text-black dark:text-white input-bordered w-full max-w-xs" />
                        </label>
                        <label for="ketua" class="form-control w-full max-w-xs">
                            <div class="label">
                                <span class="label-text text-white">Ketua TUK</span>
                            </div>
                            <input type="text" id="ketua" name="ketua" placeholder="Type here" class="input text-black dark:text-white input-bordered w-full max-w-xs" />
                        </label>
                        <label for="asesor" class="form-control w-full max-w-xs">
                            <div class="label">
                                <span class="label-text text-white">Asesor Kompetensi</span>
                            </div>
                            <input type="text" id="asesor" name="asesor" placeholder="Type here" class="input text-black dark:text-white input-bordered w-full max-w-xs" />
                        </label>
                        
                        <label for="subklas1" class="form-control w-full max-w-xs">
                            <div class="label">
                                <span class="label-text text-white">Subklasifikasi (1)</span>
                            </div>
                            <input list="jabker" type="text" id="subklas1" name="subklas[]" placeholder="Type here" class="input text-black dark:text-white input-bordered w-full max-w-xs" />
                            <datalist id="jabker">
                                @foreach ($allSubklas as $subklas)
                                    <option value="{{ $subklas->deskripsi_subklasifikasi }}">{{ $subklas->kode_subklasifikasi }}</option>
                                @endforeach
                            </datalist>
                        </label>
                        <label for="jenjang1" class="form-control w-full max-w-xs">
                            <div class="label">
                                <span class="label-text text-white">Jenjang (1)</span>
                            </div>
                            <input type="number" id="jenjang1" name="jenjang[]" placeholder="Type here" class="input text-black dark:text-white input-bordered w-full max-w-xs" />
                        </label>
                        <label for="subklas2" class="form-control w-full max-w-xs">
                            <div class="label">
                                <span class="label-text text-white">Subklasifikasi (2)</span>
                            </div>
                            <input list="jabker" type="text" id="subklas2" name="subklas[]" placeholder="Type here" class="input text-black dark:text-white input-bordered w-full max-w-xs" />
                        </label>
                        <label for="jenjang2" class="form-control w-full max-w-xs">
                            <div class="label">
                                <span class="label-text text-white">Jenjang (2)</span>
                            </div>
                            <input type="number" id="jenjang2" name="jenjang[]" placeholder="Type here" class="input text-black dark:text-white input-bordered w-full max-w-xs" />
                        </label>
                        <label for="subklas3" class="form-control w-full max-w-xs">
                            <div class="label">
                                <span class="label-text text-white">Subklasifikasi (3)</span>
                            </div>
                            <input list="jabker" type="text" id="subklas3" name="subklas[]" placeholder="Type here" class="input text-black dark:text-white input-bordered w-full max-w-xs" />
                        </label>
                        <label for="jenjang3" class="form-control w-full max-w-xs">
                            <div class="label">
                                <span class="label-text text-white">Jenjang (3)</span>
                            </div>
                            <input type="number" id="jenjang3" name="jenjang[]" placeholder="Type here" class="input text-black dark:text-white input-bordered w-full max-w-xs" />
                        </label>
                        <label for="subklas4" class="form-control w-full max-w-xs">
                            <div class="label">
                                <span class="label-text text-white">Subklasifikasi (4)</span>
                            </div>
                            <input list="jabker" type="text" id="subklas4" name="subklas[]" placeholder="Type here" class="input text-black dark:text-white input-bordered w-full max-w-xs" />
                        </label>
                        <label for="jenjang4" class="form-control w-full max-w-xs">
                            <div class="label">
                                <span class="label-text text-white">Jenjang (4)</span>
                            </div>
                            <input type="number" id="jenjang4" name="jenjang[]" placeholder="Type here" class="input text-black dark:text-white input-bordered w-full max-w-xs" />
                        </label>
                    </div>
                    <button type="button" id="addSubklasBtn" class="mt-2 bg-green-500 hover:bg-green-400 text-white py-2 px-4 rounded">Add Subklasifikasi</button>
                </div>
                <button type="submit" class="flex-none h-10 mb-2 mt-10 rounded-md bg-indigo-500 px-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500">Submit</button>
            </form>
        </div>
        <script>
            let subklasCount = 4;

            document.getElementById('addSubklasBtn').addEventListener('click', function() {
                subklasCount++;
                const container = document.getElementById('subklasContainer');

                const newField = `
                    <label for="subklas${subklasCount}" class="form-control w-full max-w-xs">
                        <div class="label">
                            <span class="label-text text-white">Subklasifikasi (${subklasCount})</span>
                        </div>
                        <input list="jabker" type="text" id="subklas${subklasCount}" name="subklas[]" placeholder="Type here" class="input text-black dark:text-white input-bordered w-full max-w-xs" />
                    </label>
                    <label for="jenjang${subklasCount}" class="form-control w-full max-w-xs">
                        <div class="label">
                            <span class="label-text text-white">Jenjang (${subklasCount})</span>
                        </div>
                        <input type="number" id="jenjang${subklasCount}" name="jenjang[]" placeholder="Type here" class="input text-black dark:text-white input-bordered w-full max-w-xs" />
                    </label>`;

                container.insertAdjacentHTML('beforeend', newField);
            });
        </script>
    </body>
</html>