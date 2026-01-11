<!-- Sidebar -->
<aside class="w-64 min-h-screen bg-white shadow-xl rounded-r-2xl py-4">

    <nav class="">

        <!-- Home -->
        <a href="{{ route('home') }}"
            class="relative flex items-center gap-3 px-4 py-3 rounded-xl transition
           {{ request()->routeIs('home')
               ? 'bg-slate-100 text-slate-900 font-semibold'
               : 'text-slate-600 hover:bg-slate-50' }}">
            @if (request()->routeIs('home'))
                <span class="absolute left-0 top-2 bottom-2 w-1 rounded-r bg-slate-900"></span>
            @endif
            <i class="bi bi-house text-lg"></i>
            Home
        </a>

        <!-- Products -->
        @php
            $productActive =
                request()->routeIs('products.*') ||
                request()->routeIs('categories.*') ||
                request()->routeIs('attributes.*');
        @endphp

        <div x-data="{ open: {{ $productActive ? 'true' : 'false' }} }" class="space-y-1">

            <button @click="open = !open"
                class="relative w-full flex items-center justify-between px-4 py-3 rounded-xl transition
                {{ $productActive ? 'bg-slate-100 text-slate-900 font-semibold' : 'text-slate-600 hover:bg-slate-50' }}">
                @if ($productActive)
                    <span class="absolute left-0 top-2 bottom-2 w-1 rounded-r bg-slate-900"></span>
                @endif

                <span class="flex items-center gap-3">
                    <i class="bi bi-box-seam text-lg"></i>
                    Products
                </span>

                <i class="bi bi-chevron-down text-xs transition-transform duration-200"
                    :class="open && 'rotate-180'"></i>
            </button>

            <div x-show="open" x-collapse.duration.250ms class="ml-4 pl-4 border-l border-slate-200 space-y-1">

                <a href="{{ route('products.index') }}"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg transition
                   {{ request()->routeIs('products.index')
                       ? 'bg-slate-100 font-semibold text-slate-900'
                       : 'text-slate-600 hover:bg-slate-50' }}">
                    <i class="bi bi-list-ul"></i>
                    Product Lists
                </a>

                <a href="{{ route('products.create') }}"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg transition
                   {{ request()->routeIs('products.create')
                       ? 'bg-slate-100 font-semibold text-slate-900'
                       : 'text-slate-600 hover:bg-slate-50' }}">
                    <i class="bi bi-plus-square"></i>
                    Add Product
                </a>

                <a href="{{ route('attributes.index') }}"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg transition
                   {{ request()->routeIs('attributes.index')
                       ? 'bg-slate-100 font-semibold text-slate-900'
                       : 'text-slate-600 hover:bg-slate-50' }}">
                    <i class="bi bi-sliders"></i>
                    Attributes
                </a>

                <a href="{{ route('categories.index') }}"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg transition
                   {{ request()->routeIs('categories.index')
                       ? 'bg-slate-100 font-semibold text-slate-900'
                       : 'text-slate-600 hover:bg-slate-50' }}">
                    <i class="bi bi-tags"></i>
                    Categories
                </a>
            </div>
        </div>
        <!-- Sales Orders -->
        @php
            $salesActive = request()->routeIs('sales.orders.*');
        @endphp

        <div x-data="{ open: {{ $salesActive ? 'true' : 'false' }} }" class="space-y-1">
            <button @click="open = !open"
                class="relative w-full flex items-center justify-between px-4 py-3 rounded-xl transition
        {{ $salesActive ? 'bg-slate-100 text-slate-900 font-semibold' : 'text-slate-600 hover:bg-slate-50' }}">
                @if ($salesActive)
                    <span class="absolute left-0 top-2 bottom-2 w-1 rounded-r bg-slate-900"></span>
                @endif

                <span class="flex items-center gap-3">
                    <i class="bi bi-receipt text-lg"></i>
                    Sell
                </span>

                <i class="bi bi-chevron-down text-xs transition-transform duration-200"
                    :class="open && 'rotate-180'"></i>
            </button>

            <div x-show="open" x-collapse.duration.250ms class="ml-4 pl-4 border-l border-slate-200 space-y-1">
                <a href="{{ route('sales.orders') }}"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg transition
           {{ request()->routeIs('sales.orders')
               ? 'bg-slate-100 font-semibold text-slate-900'
               : 'text-slate-600 hover:bg-slate-50' }}">
                    <i class="bi bi-list-ul"></i>
                    Sale Order List
                </a>
            </div>
        </div>


        <!-- User Management -->
        @php
            $userActive = request()->routeIs('users.*') || request()->routeIs('roles.*');
        @endphp

        <div x-data="{ open: {{ $userActive ? 'true' : 'false' }} }" class="space-y-1">

            <button @click="open = !open"
                class="relative w-full flex items-center justify-between px-4 py-3 rounded-xl transition
                {{ $userActive ? 'bg-slate-100 text-slate-900 font-semibold' : 'text-slate-600 hover:bg-slate-50' }}">
                @if ($userActive)
                    <span class="absolute left-0 top-2 bottom-2 w-1 rounded-r bg-slate-900"></span>
                @endif

                <span class="flex items-center gap-3">
                    <i class="bi bi-people text-lg"></i>
                    User Management
                </span>

                <i class="bi bi-chevron-down text-xs transition-transform duration-200"
                    :class="open && 'rotate-180'"></i>
            </button>

            <div x-show="open" x-collapse.duration.250ms class="ml-4 pl-4 border-l border-slate-200 space-y-1">

                <a href="{{ route('users.index') }}"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg transition
                   {{ request()->routeIs('users.index')
                       ? 'bg-slate-100 font-semibold text-slate-900'
                       : 'text-slate-600 hover:bg-slate-50' }}">
                    <i class="bi bi-person"></i>
                    Users List
                </a>

                <a href="{{ route('roles.index') }}"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg transition
                   {{ request()->routeIs('roles.index')
                       ? 'bg-slate-100 font-semibold text-slate-900'
                       : 'text-slate-600 hover:bg-slate-50' }}">
                    <i class="bi bi-shield-lock"></i>
                    Roles
                </a>
            </div>
        </div>

    </nav>
</aside>
