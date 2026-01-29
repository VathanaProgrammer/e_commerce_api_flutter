<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ session('business.name', 'Business Name') }} | @yield('title', 'Admin Panel')</title>
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
    <!-- DataTables Responsive CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <!-- Toastr CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <!-- Alpine -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js" defer></script>


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    <style>
        /* Navbar Animation Styles */
        .navbar {
            animation: slideDown 0.4s ease-out forwards;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .navbar .btn {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .navbar .btn:hover {
            transform: scale(1.05);
        }

        .navbar .dropdown-menu {
            animation: dropdownFadeIn 0.2s ease-out;
            border: none;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            border-radius: 12px;
            overflow: hidden;
        }

        @keyframes dropdownFadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .navbar .dropdown-item {
            transition: all 0.2s ease;
            padding: 10px 20px;
        }

        .navbar .dropdown-item:hover {
            background: linear-gradient(90deg, rgba(102, 126, 234, 0.1) 0%, transparent 100%);
            padding-left: 25px;
        }

        /* Notification badge animation */
        .navbar .badge {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }

        /* User avatar hover effect */
        .navbar .rounded-circle {
            transition: all 0.3s ease;
        }

        .navbar .dropdown:hover .rounded-circle {
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        /* Date badge styling */
        .navbar .text-opacity-75 {
            transition: all 0.3s ease;
        }

        .navbar .text-opacity-75:hover {
            background: rgba(255,255,255,0.1) !important;
        }

        /* ============================================
           MODAL BACKDROP FIX
           ============================================ */
        
        /* Ensure modal backdrop is properly layered */
        .modal-backdrop {
            z-index: 1040 !important;
        }

        .modal {
            z-index: 1050 !important;
        }

        /* Ensure sidebar stays below modal */
        aside {
            z-index: 100;
        }

        /* Ensure navbar stays below modal */
        .navbar {
            z-index: 1030;
        }

        /* Fix for stuck backdrop - make sure it's not blocking when hidden */
        .modal-backdrop.fade:not(.show) {
            pointer-events: none !important;
            opacity: 0 !important;
        }

        /* Ensure body scroll is restored */
        body.modal-open-fix {
            overflow: auto !important;
            padding-right: 0 !important;
        }
    </style>
</head>

<body class="bg-light" x-data="{ mobileSidebarOpen: false }">

    <div class="d-flex flex-column w-100 min-vh-100">

        <nav class="navbar navbar-expand-lg px-4 py-3 shadow-sm sticky-top"
            style="background: linear-gradient(90deg, #1e293b, #0f172a); z-index: 1040;">
            <div class="container-fluid d-flex justify-content-between align-items-center">

                <!-- LEFT: Logo + Name -->
                <div class="d-flex align-items-center gap-2">
                    <!-- Mobile Sidebar Toggle -->
                    <button @click="mobileSidebarOpen = !mobileSidebarOpen" 
                            class="btn btn-link text-white p-0 me-3 d-lg-none">
                        <i class="bi bi-list fs-2"></i>
                    </button>

                    <div class="rounded-circle bg-white d-flex justify-content-center align-items-center overflow-hidden"
                        style="width:40px; height:40px;">
                        @if(session('business.logo'))
                            <img src="{{ session('business.logo') }}" alt="Logo" class="w-100 h-100" style="object-fit: cover;">
                        @else
                            <i class="bi bi-shop text-dark"></i>
                        @endif
                    </div>
                    <a href="{{ route('home') }}" class="text-white fw-bold fs-5 mb-0 text-decoration-none">
                        {{ session('business.name', 'Business Name') }}
                    </a>
                </div>

                <!-- RIGHT: Date + Notifications + User -->
                <div class="d-flex align-items-center gap-3">

                    <!-- Date (Hidden on small mobile) -->
                    <div class="text-white text-opacity-75 small px-3 py-1 rounded d-none d-md-block"
                        style="background: rgba(255,255,255,0.05);">
                        {{ now()->format('D, M d Y') }}
                    </div>

                    <!-- Notifications -->
                    <div class="dropdown" id="notification-dropdown">
                        <button class="btn btn-sm btn-transparent position-relative text-white"
                            data-bs-toggle="dropdown">
                            <i class="bi bi-bell fs-5"></i>
                            <span id="notification-badge"
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none">
                                0
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4" style="min-width:320px; max-height: 450px; overflow-y: auto;">
                            <li class="px-3 py-2 border-bottom">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold">Recent Orders</span>
                                    <span class="badge bg-light text-primary">New</span>
                                </div>
                            </li>
                            <div id="notification-items">
                                <li><span class="dropdown-item text-muted small py-3 text-center">Loading notifications...</span></li>
                            </div>
                            <li class="text-center border-top">
                                <a href="{{ route('sales.orders') }}" class="dropdown-item small text-primary fw-bold py-2">View All Orders</a>
                            </li>
                        </ul>
                    </div>

                    <!-- Audio for Alerts -->
                    <audio id="notification-audio" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>

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

                            <span class="small d-none d-md-inline">{{ auth()->user()->first_name . " " . auth()->user()->last_name ?? 'Guest' }}</span>
                            <i class="bi bi-chevron-down small"></i>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('users.profile', auth()->id()) }}"><i class="bi bi-person me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item open-business-settings" data-id="{{ session('business.id') }}" href="#"><i class="bi bi-gear me-2"></i>Business Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form class="logout-form" method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                                </form>
                            </li>

                        </ul>
                    </div>

                </div>
            </div>
        </nav>

        <div class="d-flex flex-grow-1 position-relative overflow-hidden">

            <!-- SIDEBAR WRAPPER -->
            <!-- Mobile Backdrop -->
            <div x-show="mobileSidebarOpen" 
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="mobileSidebarOpen = false"
                 class="fixed inset-0 bg-gray-900/80 z-40 d-lg-none"
                 style="backdrop-filter: blur(4px);">
            </div>

            <!-- Sidebar Container -->
            <div class="sidebar-wrapper fixed inset-y-0 left-0 z-50 w-64 transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto lg:flex lg:flex-col"
                 :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full'">
                @include('layouts.sidebar')
            </div>

            <!-- MAIN CONTENT -->
            <main class="flex-grow-1 bg-gray-200 page-content overflow-auto h-100 w-100">
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

            <!-- BUSINESS SETTINGS MODAL -->
            @include('business.settings_modal')

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
        <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
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
            // Sound Player Wrapper
            function playAudio(id) {
                const audio = document.getElementById(id);
                if (!audio) {
                    console.error("Audio element not found:", id);
                    return;
                }
                
                // Reset and play
                audio.pause();
                audio.currentTime = 0;
                
                const playPromise = audio.play();
                if (playPromise !== undefined) {
                    playPromise.catch(error => {
                        console.warn("Audio playback failed (usually due to browser autoplay policy):", error);
                    });
                }
            }

            // Proxy toastr methods to force sound playback
            // This guarantees the sound runs exactly when the toast is requested
            (function() {
                const originalSuccess = toastr.success;
                toastr.success = function(...args) {
                    playAudio('success-audio');
                    originalSuccess.apply(toastr, args);
                };

                const originalError = toastr.error;
                toastr.error = function(...args) {
                    playAudio('error-audio');
                    originalError.apply(toastr, args);
                };

                const originalWarning = toastr.warning;
                toastr.warning = function(...args) {
                    playAudio('error-audio');
                    originalWarning.apply(toastr, args);
                };
                
                const originalInfo = toastr.info;
                toastr.info = function(...args) {
                    playAudio('success-audio');
                    originalInfo.apply(toastr, args);
                };
            })();

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

                // ============================================
                // MODAL BACKDROP FIX
                // ============================================
                
                // Clean up modal backdrop when any modal is hidden
                $(document).on('hidden.bs.modal', '.modal', function () {
                    // Remove any orphaned backdrops
                    if ($('.modal:visible').length === 0) {
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open').css({
                            'overflow': '',
                            'padding-right': ''
                        });
                    }
                });

                // Fallback: Clean up on escape key
                $(document).on('keydown', function(e) {
                    if (e.key === 'Escape' && $('.modal:visible').length === 0) {
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open').css({
                            'overflow': '',
                            'padding-right': ''
                        });
                    }
                });

                // Clean up any existing orphaned backdrops on page load
                setTimeout(function() {
                    if ($('.modal.show').length === 0 && $('.modal-backdrop').length > 0) {
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open');
                    }
                }, 500);
            });

            // Global function to force close all modals and clean up
            window.forceCloseModals = function() {
                $('.modal').modal('hide');
                setTimeout(function() {
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open').css({
                        'overflow': '',
                        'padding-right': ''
                    });
                }, 300);
            };
        </script>

        <script src="{{ asset('js/app.js') }}"></script>
        <script src="{{ asset('js/notifications.js') }}"></script>
        @include('includes.js')
        @yield('scripts')
        @stack('scripts')
</body>

</html>
