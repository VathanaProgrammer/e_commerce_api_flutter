<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin Panel')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    {{-- DataTables --}}
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    {{-- Toastr --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">

    <!-- JSZip for Excel export (optional) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <!-- PDFMake for PDF export (optional) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <!-- Buttons for HTML5 export and print -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    {{-- App CSS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="bg-light">

    <div class="d-flex">
        {{-- Main Content --}}
        <div class="flex-grow-1 text-white">

            {{-- Top Header --}}
            <!-- Top Header -->
            <nav class="navbar navbar-expand-lg navbar-light sed p-3 overflow-hidden">
                <div class="container-fluid d-flex justify-content-between align-items-center">

                    <!-- Business Name on the top-left -->
                    <div class="d-flex align-items-center me-3">
                        <a href="{{ route('home') }}" class="text-white text-decoration-none fw-bold"
                            style="outline: none;">
                            {{ session('business.name', 'Business Name') }}
                        </a>
                    </div>

                    <!-- Right-side items: Date, Notifications, User -->
                    <ul class="navbar-nav ms-auto d-flex align-items-center">

                        <!-- Date -->
                        <li class="nav-item me-3 thr px-3 rounded py-1 d-flex align-items-center" style="height: 45px;">
                            <span>{{ now()->format('D, M d Y') }}</span>
                        </li>

                        <!-- Notifications -->
                        <li class="nav-item dropdown me-3 thr text-white rounded px-2 py-1 d-flex align-items-center"
                            style="height: 45px;">
                            <a class="nav-link dropdown-toggle text-dark text-white" href="#" role="button"
                                data-bs-toggle="dropdown">
                                <i class="bi bi-bell me-1"></i> Notifications
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end thr" style="min-height: 45px;">
                                <li><span class="dropdown-item text-white">No new notifications</span></li>
                            </ul>
                        </li>

                        <!-- User -->
                        <li class="nav-item thr text-white px-3 py-1 rounded d-flex align-items-center"
                            style="height: 45px;">
                            <span class="d-flex align-items-center">
                                {{ auth()->user()->first_name ?? 'Guest' }}
                                <i class="bi bi-person-circle fs-3 ms-2"></i>
                            </span>
                        </li>

                    </ul>
                </div>
            </nav>
            <div class="d-flex">

                <!-- Sidebar -->
                @include('layouts.sidebar')

                <!-- Main Content -->
                <div class="flex-grow-1 d-flex flex-column">
                    <main class="h-full">
                        @yield('content')
                    </main>
                </div>

            </div>
        </div>

    </div>

    {{-- Scripts --}}
    <!-- jQuery & Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <!-- Buttons core + Bootstrap -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>

    <!-- Buttons extras -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <!-- DataTables Responsive CSS -->
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">

    <!-- DataTables Responsive JS -->
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>


    <script>
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: "toast-top-right",
            timeOut: 3000
        };

        @if (session()->has('success'))
            @if (session('success') === true)
                toastr.success("{{ session('message') ?? 'Operation successful' }}");
            @else
                toastr.error("{{ session('message') ?? 'Operation failed' }}");
            @endif
        @endif

        @if (isset($errors) && $errors->any())
            <
            script >
                @foreach ($errors->all() as $error)
                    toastr.error("{{ $error }}");
                @endforeach
    </script>
    @endif

    </script>
    {{-- Global JS --}}
    <script src="{{ asset('js/app.js') }}"></script>
    @yield('scripts')
</body>

</html>
