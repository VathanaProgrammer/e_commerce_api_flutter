<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin Panel')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- MDB UI Kit -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <!-- Toastr CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">

    <!-- Alpine -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js" defer></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="bg-light">

    <div class="d-flex flex-column w-100">

        <nav class="navbar navbar-expand-lg px-4 py-3 shadow-sm"
            style="background: linear-gradient(90deg, #1e293b, #0f172a);">
            <div class="container-fluid d-flex justify-content-between align-items-center">

                <!-- LEFT: Logo + Name -->
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-white d-flex justify-content-center align-items-center"
                        style="width:40px; height:40px;">
                        <i class="bi bi-shop text-dark"></i>
                    </div>
                    <a href="{{ route('home') }}" class="text-white fw-bold fs-5 mb-0">
                        {{ session('business.name', 'Business Name') }}
                    </a>
                </div>

                <!-- RIGHT: Date + Notifications + User -->
                <div class="d-flex align-items-center gap-3">

                    <!-- Date -->
                    <div class="text-white text-opacity-75 small px-3 py-1 rounded"
                        style="background: rgba(255,255,255,0.05);">
                        {{ now()->format('D, M d Y') }}
                    </div>

                    <!-- Notifications -->
                    <div class="dropdown">
                        <button class="btn btn-sm btn-transparent position-relative text-white"
                            data-bs-toggle="dropdown">
                            <i class="bi bi-bell fs-5"></i>
                            <span
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                3
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" style="min-width:200px;">
                            <li><span class="dropdown-item text-dark">No new notifications</span></li>
                        </ul>
                    </div>

                    <!-- User Profile -->
                    <div class="dropdown">
                        <button class="btn btn-sm btn-transparent d-flex align-items-center gap-2 text-white"
                            data-bs-toggle="dropdown">

                            <div class="rounded-circle bg-white d-flex justify-content-center align-items-center"
                                style="width:30px; height:30px; overflow:hidden;">

                                @if (auth()->user() && auth()->user()->profile_image_url)
                                    <img src="{{ auth()->user()->profile_image_url }}" alt="User Avatar"
                                        class="w-100 h-100 object-cover">
                                @else
                                    <i class="bi bi-person-fill text-dark"></i>
                                @endif

                            </div>

                            <span class="small">{{ auth()->user()->first_name . " " . auth()->user()->last_name ?? 'Guest' }}</span>
                            <i class="bi bi-chevron-down small"></i>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('users.profile', auth()->id()) }}">Profile</a></li>
                            <li>
                                <form class="logout-form" method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Logout</button>
                                </form>
                            </li>

                        </ul>
                    </div>

                </div>
            </div>
        </nav>

        <div class="d-flex">

            <!-- SIDEBAR -->
            @include('layouts.sidebar')

            <!-- MAIN CONTENT -->
            <main class="flex-grow-1 bg-gray-200">
                @yield('content')
            </main>
            <!-- GLOBAL MODAL -->
            <div class="modal fade" id="globalConfirmModal" tabindex="-1" aria-labelledby="globalConfirmModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="globalConfirmModalLabel">Confirm Action</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="globalConfirmModalBody">
                            Are you sure you want to proceed?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-danger btn-sm" id="globalConfirmModalConfirmBtn">Yes,
                                Proceed</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- AUDIO -->
        <audio id="success-audio" src="{{ asset('sounds/success.mp3') }}" preload="auto"></audio>
        <audio id="error-audio" src="{{ asset('sounds/error.mp3') }}" preload="auto"></audio>

        <!-- SCRIPTS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

        <script>
            window.toastr = toastr;
            let soundEnabled = false;
            $(document).one('click keydown scroll', () => {
                soundEnabled = true;
            });

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
