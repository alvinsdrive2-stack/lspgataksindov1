<!DOCTYPE html>
<html class="dark" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <title>File Verifikasi - LSP LPK Gataksindo</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite('resources/css/app.css')
</head>
<body class="font-sans antialiased dark">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <header class="bg-white dark:bg-gray-800 shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between">
                <a href="/archive" class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Verifikasi TUK - Verifikator TUK
                </a>
                <a href="logout" class="py-2 px-1 bg-red-600 rounded text-center font-semibold text-gray-800 dark:text-gray-200 leading-tight">Logout</a>
            </div>
        </header>
        <main>
            <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
            <link href="https://cdn.datatables.net/v/dt/dt-1.13.4/datatables.min.css" rel="stylesheet" />
            <script src="https://cdn.datatables.net/v/dt/dt-1.13.4/datatables.min.js"></script>
            <script>
                $(document).ready(function() {
                    $('#listTable').DataTable({
                        order: [2, 'asc']
                    });

                    // Add event listener to the select element
                    $('#status').on('change', function() {
                        var selectedTuk = $(this).val(); // Get the selected TUK value

                        // Use DataTables' built-in search API to filter the table
                        $('#listTable').DataTable().column(1).search(selectedTuk).draw();
                    });
                });
            </script>

            @if (session('success'))
            <div class="max-w-[1200px] text-gray-900 mt-10 mx-auto bg-green-400 p-3 rounded-md">
                <p>{!! session('success') !!}</p>
            </div>
            @endif
            <div class="py-12 mx-auto lg:max-w-screen-xl">
                <div class="sm:px-6 lg:px-8">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg overflow-x-auto">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <table id="listTable">
                                <thead>
                                    <tr>
                                        <th>No Surat</th>
                                        <th>Nama File</th>
                                        <th>Tanggal Dibuat</th>
                                        <th>Link</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($all_verifications as $data)
                                        <tr>
                                            <td>{{ $data['no_surat'] }}</td>
                                            <td>{{ $data['link'] }}</td>
                                            <td>{{ $data['created_at'] }}</td>
                                            <td><a href="{{ Storage::disk('public')->url('tuk-paperless/' . \Carbon\Carbon::parse($data['created_at'])->format('Y-m-d') . '/' . strtoupper($data['tuk']) . '/' . $data['link']) }}" target="_blank">
                                                    <div class="py-2 px-1 bg-green-600 rounded text-center">Lihat File
                                                    </div>
                                                </a>
                                            </td>
                                            <td><a href="/verification/{{ $data['id'] }}">
                                                    <div class="py-2 px-1 bg-green-600 rounded text-center">Verifikasi
                                                    </div>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
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
    </script>
</body>
</html>