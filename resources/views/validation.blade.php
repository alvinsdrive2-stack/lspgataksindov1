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
                    Validasi Verifikasi TUK - Validator
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
                                        <th class="text-center" rowspan="2">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($all_verifications as $verification)
                                        <tr>
                                            <td>{{ $verification['no_surat'] }}</td>
                                            <td>{{ $verification['link'] }}</td>
                                            <td>{{ $verification['created_at'] }}</td>
                                            <td><a href="{{ Storage::disk('public')->url('tuk-paperless/' . \Carbon\Carbon::parse($verification['created_at'])->format('Y-m-d') . '/' . strtoupper($verification['tuk']) . '/' . $verification['link']) }}" target="_blank">
                                                    <div class="py-2 px-1 bg-green-600 rounded text-center">Lihat File
                                                    </div>
                                                </a>
                                            </td>
                                            <td><a href="/approve-validation/{{ $verification['id'] }}">
                                                    <div class="py-2 px-1 bg-green-600 rounded text-center">Layak
                                                    </div>
                                                </a>
                                            </td>
                                            <td><a href="/reject-validation/{{ $verification['id'] }}">
                                                    <div class="py-2 px-1 bg-red-600 rounded text-center">Tidak Layak
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
</body>
</html>