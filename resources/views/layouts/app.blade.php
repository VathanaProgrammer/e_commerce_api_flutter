<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin Panel')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <!-- DataTables Buttons CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

    <!-- Toastr CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @section('styles')
</head>

<body class="bg-light">

    <div class="d-flex">
        <div class="flex-grow-1 text-white">
            <nav class="navbar navbar-expand-lg navbar-light sed p-3 overflow-hidden">
                <div class="container-fluid d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center me-3">
                        <a href="{{ route('home') }}" class="text-white text-decoration-none fw-bold"
                            style="outline: none;">
                            {{ session('business.name', 'Business Name') }}
                        </a>
                    </div>
                    <ul class="navbar-nav ms-auto d-flex align-items-center">
                        <li class="nav-item me-3 thr px-3 rounded py-1 d-flex align-items-center" style="height: 45px;">
                            <span>{{ now()->format('D, M d Y') }}</span>
                        </li>
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
                @include('layouts.sidebar')
                <div class="flex-grow-1 d-flex flex-column">
                    <main class="h-full">
                        @yield('content')
                    </main>
                </div>
            </div>
        </div>

        <!-- Global Confirmation Modal -->
        <div class="modal fade" id="globalConfirmModal" tabindex="-1" aria-labelledby="globalConfirmModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="globalConfirmModalLabel">Confirm Action</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="globalConfirmModalBody">
                        Are you sure you want to proceed?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger btn-sm" id="globalConfirmModalConfirmBtn">Yes,
                            Proceed</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Audio elements -->
    <audio id="success-audio" src="{{ asset('sounds/success.mp3') }}" preload="auto"></audio>
    <audio id="error-audio" src="{{ asset('sounds/error.mp3') }}" preload="auto"></audio>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>
    <!-- jQuery first (needed by DataTables) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <!-- DataTables Buttons JS and dependencies -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        window.toastr = toastr;

        // Track first user interaction (required by autoplay policy)
        let soundEnabled = false;
        $(document).one('click keydown scroll', () => {
            soundEnabled = true;
        });

        // Toastr options
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: "toast-top-right",
            timeOut: 3000,
            newestOnTop: true,
            preventDuplicates: true,
            escapeHtml: false,
        };
        toastr.options.onShown = function(toast) {
            let $toast = $(toast);
            if ($toast.hasClass('toast-success')) playAudio('success-audio');
            else if ($toast.hasClass('toast-error')) playAudio('error-audio');
        };

        function playAudio(id) {
            const audio = document.getElementById(id);
            if (!audio) return;
            audio.pause();
            audio.currentTime = 0;
            audio.play().catch(() => {});
        }

        // Laravel flash messages
        $(document).ready(function() {
            @if (session()->has('success'))
                toastr.success(@json(session('success')));
            @endif
            @if (session()->has('error'))
                toastr.error(@json(session('error')));
            @endif
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    toastr.error(@json($error));
                @endforeach
            @endif
        });
    </script>

    <script src="{{ asset('js/app.js') }}"></script>
    @include('includes.js')
    @yield('scripts')
</body>

</html>
