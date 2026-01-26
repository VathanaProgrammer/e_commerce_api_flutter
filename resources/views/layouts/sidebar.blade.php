<!-- Sidebar -->
<aside class="w-64 min-h-screen bg-white shadow-xl rounded-r-2xl py-4 sidebar-animate" 
       style="background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);">

    <nav class="px-2">

        <!-- Home -->
        <a href="{{ route('home') }}"
            class="sidebar-item relative flex items-center gap-3 px-4 py-3 rounded-xl mb-1
           {{ request()->routeIs('home')
               ? 'bg-gradient-to-r from-slate-800 to-slate-700 text-white font-semibold shadow-md'
               : 'text-slate-600 hover:bg-slate-100' }}"
            style="transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);">
            @if (request()->routeIs('home'))
                <span class="absolute left-0 top-2 bottom-2 w-1 rounded-r bg-indigo-500" style="animation: fadeInLeft 0.3s ease;"></span>
            @endif
            <i class="bi bi-house text-lg {{ request()->routeIs('home') ? 'icon-bounce' : '' }}"></i>
            <span>Home</span>
        </a>

        <!-- Products -->
        @role('admin|staff')
        @php
            $productActive =
                request()->routeIs('products.*') ||
                request()->routeIs('categories.*') ||
                request()->routeIs('attributes.*');
        @endphp

        <div x-data="{ open: {{ $productActive ? 'true' : 'false' }} }" class="space-y-1 mb-1">

            <button @click="open = !open"
                class="sidebar-item relative w-full flex items-center justify-between px-4 py-3 rounded-xl
                {{ $productActive ? 'bg-gradient-to-r from-slate-800 to-slate-700 text-white font-semibold shadow-md' : 'text-slate-600 hover:bg-slate-100' }}"
                style="transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);">
                @if ($productActive)
                    <span class="absolute left-0 top-2 bottom-2 w-1 rounded-r bg-indigo-500"></span>
                @endif

                <span class="flex items-center gap-3">
                    <i class="bi bi-box-seam text-lg"></i>
                    <span>Products</span>
                </span>

                <i class="bi bi-chevron-down text-xs"
                    :class="open && 'rotate-180'"
                    style="transition: transform 0.3s ease;"></i>
            </button>

            <div x-show="open" x-collapse.duration.300ms 
                 class="ml-4 pl-4 border-l-2 border-slate-200 space-y-1"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0">

                <a href="{{ route('products.index') }}"
                    class="sidebar-subitem flex items-center gap-3 px-4 py-2.5 rounded-lg
                   {{ request()->routeIs('products.index')
                       ? 'bg-indigo-50 font-semibold text-indigo-700 border-l-2 border-indigo-500'
                       : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                    style="transition: all 0.25s ease;">
                    <i class="bi bi-list-ul text-sm"></i>
                    <span>Product Lists</span>
                </a>

                <a href="{{ route('products.create') }}"
                    class="sidebar-subitem flex items-center gap-3 px-4 py-2.5 rounded-lg
                   {{ request()->routeIs('products.create')
                       ? 'bg-indigo-50 font-semibold text-indigo-700 border-l-2 border-indigo-500'
                       : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                    style="transition: all 0.25s ease;">
                    <i class="bi bi-plus-square text-sm"></i>
                    <span>Add Product</span>
                </a>

                <a href="{{ route('attributes.index') }}"
                    class="sidebar-subitem flex items-center gap-3 px-4 py-2.5 rounded-lg
                   {{ request()->routeIs('attributes.index')
                       ? 'bg-indigo-50 font-semibold text-indigo-700 border-l-2 border-indigo-500'
                       : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                    style="transition: all 0.25s ease;">
                    <i class="bi bi-sliders text-sm"></i>
                    <span>Attributes</span>
                </a>

                <a href="{{ route('categories.index') }}"
                    class="sidebar-subitem flex items-center gap-3 px-4 py-2.5 rounded-lg
                   {{ request()->routeIs('categories.index')
                       ? 'bg-indigo-50 font-semibold text-indigo-700 border-l-2 border-indigo-500'
                       : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                    style="transition: all 0.25s ease;">
                    <i class="bi bi-tags text-sm"></i>
                    <span>Categories</span>
                </a>
            </div>
        </div>
        @endrole

        <!-- Sales Orders -->
        @role('admin|staff')
        @php
            $salesActive = request()->routeIs('sales.orders.*') || request()->routeIs('sales.orders');
        @endphp

        <div x-data="{ open: {{ $salesActive ? 'true' : 'false' }} }" class="space-y-1 mb-1">
            <button @click="open = !open"
                class="sidebar-item relative w-full flex items-center justify-between px-4 py-3 rounded-xl
                {{ $salesActive ? 'bg-gradient-to-r from-slate-800 to-slate-700 text-white font-semibold shadow-md' : 'text-slate-600 hover:bg-slate-100' }}"
                style="transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);">
                @if ($salesActive)
                    <span class="absolute left-0 top-2 bottom-2 w-1 rounded-r bg-indigo-500"></span>
                @endif

                <span class="flex items-center gap-3">
                    <i class="bi bi-receipt text-lg"></i>
                    <span>Sales</span>
                </span>

                <i class="bi bi-chevron-down text-xs"
                    :class="open && 'rotate-180'"
                    style="transition: transform 0.3s ease;"></i>
            </button>

            <div x-show="open" x-collapse.duration.300ms 
                 class="ml-4 pl-4 border-l-2 border-slate-200 space-y-1"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0">
                <a href="{{ route('sales.orders') }}"
                    class="sidebar-subitem flex items-center gap-3 px-4 py-2.5 rounded-lg
                   {{ request()->routeIs('sales.orders')
                       ? 'bg-indigo-50 font-semibold text-indigo-700 border-l-2 border-indigo-500'
                       : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                    style="transition: all 0.25s ease;">
                    <i class="bi bi-list-ul text-sm"></i>
                    <span>Sale Order List</span>
                </a>
            </div>
        </div>
        @endrole


        <!-- User Management -->
        @role('admin')
        @php
            $userActive = request()->routeIs('users.*') || request()->routeIs('roles.*');
        @endphp

        <div x-data="{ open: {{ $userActive ? 'true' : 'false' }} }" class="space-y-1 mb-1">

            <button @click="open = !open"
                class="sidebar-item relative w-full flex items-center justify-between px-4 py-3 rounded-xl
                {{ $userActive ? 'bg-gradient-to-r from-slate-800 to-slate-700 text-white font-semibold shadow-md' : 'text-slate-600 hover:bg-slate-100' }}"
                style="transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);">
                @if ($userActive)
                    <span class="absolute left-0 top-2 bottom-2 w-1 rounded-r bg-indigo-500"></span>
                @endif

                <span class="flex items-center gap-3">
                    <i class="bi bi-people text-lg"></i>
                    <span>User Management</span>
                </span>

                <i class="bi bi-chevron-down text-xs"
                    :class="open && 'rotate-180'"
                    style="transition: transform 0.3s ease;"></i>
            </button>

            <div x-show="open" x-collapse.duration.300ms 
                 class="ml-4 pl-4 border-l-2 border-slate-200 space-y-1"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0">

                <a href="{{ route('users.index') }}"
                    class="sidebar-subitem flex items-center gap-3 px-4 py-2.5 rounded-lg
                   {{ request()->routeIs('users.index')
                       ? 'bg-indigo-50 font-semibold text-indigo-700 border-l-2 border-indigo-500'
                       : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                    style="transition: all 0.25s ease;">
                    <i class="bi bi-person text-sm"></i>
                    <span>Users List</span>
                </a>

                <a href="{{ route('roles.index') }}"
                    class="sidebar-subitem flex items-center gap-3 px-4 py-2.5 rounded-lg
                   {{ request()->routeIs('roles.index')
                       ? 'bg-indigo-50 font-semibold text-indigo-700 border-l-2 border-indigo-500'
                       : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                    style="transition: all 0.25s ease;">
                    <i class="bi bi-shield-lock text-sm"></i>
                    <span>Roles</span>
                </a>
            </div>
        </div>
        @endrole

        <!-- Divider -->
        <div class="my-4 mx-4 border-t border-slate-200"></div>

        @role('admin')
        <div class="px-2">
            <p class="px-4 text-xs text-slate-400 uppercase tracking-wider mb-2">Settings</p>
            
            <a href="{{ route('business.settings') }}" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl mb-1
               {{ request()->routeIs('business.settings')
                   ? 'bg-gradient-to-r from-slate-800 to-slate-700 text-white font-semibold shadow-md'
                   : 'text-slate-600 hover:bg-slate-100' }}"
               style="transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);">
                <i class="bi bi-gear text-lg"></i>
                <span>Business Settings</span>
            </a>
        </div>
        @endrole

    </nav>
</aside>
