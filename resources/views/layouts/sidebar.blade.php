<!-- Sidebar -->
<aside class="w-full h-full bg-slate-900 text-slate-300 shadow-2xl rounded-r-2xl py-6 sidebar-animate overflow-y-auto border-r border-slate-800">

    <nav class="px-3 space-y-2">

        <!-- Home -->
        <a href="{{ route('home') }}"
            class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 ease-in-out
           {{ request()->routeIs('home')
               ? 'bg-blue-600/10 text-blue-400 font-medium'
               : 'hover:bg-slate-800/50 hover:text-white' }}">
            <i class="bi bi-grid-1x2 text-xl transition-transform group-hover:scale-110 {{ request()->routeIs('home') ? 'text-blue-500' : 'text-slate-500 group-hover:text-blue-400' }}"></i>
            <span class="tracking-wide text-sm">Dashboard</span>
            @if (request()->routeIs('home'))
                <span class="ml-auto w-1 h-1 rounded-full bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.6)]"></span>
            @endif
        </a>

        <!-- Section Label -->
        <div class="px-4 mt-6 mb-2 text-[10px] font-bold text-slate-500 uppercase tracking-widest leading-none">Management</div>

        <!-- Products -->
        @role('admin|staff')
        @php
            $productActive =
                request()->routeIs('products.*') ||
                request()->routeIs('categories.*') ||
                request()->routeIs('attributes.*');
        @endphp

        <div x-data="{ open: {{ $productActive ? 'true' : 'false' }} }">
            <button @click="open = !open"
                class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-300 group
                {{ $productActive ? 'bg-blue-600/10 text-blue-400 font-medium' : 'hover:bg-slate-800/50 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <i class="bi bi-box-seam text-xl transition-transform group-hover:scale-110 {{ $productActive ? 'text-blue-500' : 'text-slate-500 group-hover:text-blue-400' }}"></i>
                    <span class="tracking-wide text-sm">Inventory</span>
                </div>
                <i class="bi bi-chevron-down text-[10px] transition-transform duration-300"
                   :class="open ? 'rotate-180 text-blue-400' : 'text-slate-600'"></i>
            </button>

            <div x-show="open" x-collapse
                 class="mt-1 space-y-1 pl-11 pr-2 border-l-2 border-slate-800 ml-6">
                
                <a href="{{ route('products.index') }}"
                   class="block px-3 py-2 text-sm rounded-lg transition-colors
                   {{ request()->routeIs('products.index') ? 'text-blue-400 bg-blue-600/5 font-medium' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                   Products
                </a>
                <a href="{{ route('products.create') }}"
                   class="block px-3 py-2 text-sm rounded-lg transition-colors
                   {{ request()->routeIs('products.create') ? 'text-blue-400 bg-blue-600/5 font-medium' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                   Add Product
                </a>
                <a href="{{ route('attributes.index') }}"
                   class="block px-3 py-2 text-sm rounded-lg transition-colors
                   {{ request()->routeIs('attributes.index') ? 'text-blue-400 bg-blue-600/5 font-medium' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                   Attributes
                </a>
                <a href="{{ route('categories.index') }}"
                   class="block px-3 py-2 text-sm rounded-lg transition-colors
                   {{ request()->routeIs('categories.index') ? 'text-blue-400 bg-blue-600/5 font-medium' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                   Categories
                </a>
            </div>
        </div>
        @endrole

        <!-- Sales Orders -->
        @role('admin|staff')
        @php
            $salesActive = request()->routeIs('sales.orders.*') || request()->routeIs('sales.orders');
        @endphp

        <div x-data="{ open: {{ $salesActive ? 'true' : 'false' }} }">
            <button @click="open = !open"
                class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-300 group
                {{ $salesActive ? 'bg-emerald-600/10 text-emerald-400 font-medium' : 'hover:bg-slate-800/50 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <i class="bi bi-cart3 text-xl transition-transform group-hover:scale-110 {{ $salesActive ? 'text-emerald-500' : 'text-slate-500 group-hover:text-emerald-400' }}"></i>
                    <span class="tracking-wide text-sm">Sales & Orders</span>
                </div>
                <i class="bi bi-chevron-down text-[10px] transition-transform duration-300"
                   :class="open ? 'rotate-180 text-emerald-400' : 'text-slate-600'"></i>
            </button>

            <div x-show="open" x-collapse
                 class="mt-1 space-y-1 pl-11 pr-2 border-l-2 border-slate-800 ml-6">
                <a href="{{ route('sales.orders') }}"
                   class="block px-3 py-2 text-sm rounded-lg transition-colors
                   {{ request()->routeIs('sales.orders') ? 'text-emerald-400 bg-emerald-600/5 font-medium' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                   All Orders
                </a>
            </div>
        </div>
        @endrole


        <!-- User Management -->
        @role('admin')
        @php
            $userActive = request()->routeIs('users.*') || request()->routeIs('roles.*');
        @endphp

        <div x-data="{ open: {{ $userActive ? 'true' : 'false' }} }">
            <button @click="open = !open"
                class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-300 group
                {{ $userActive ? 'bg-purple-600/10 text-purple-400 font-medium' : 'hover:bg-slate-800/50 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <i class="bi bi-people text-xl transition-transform group-hover:scale-110 {{ $userActive ? 'text-purple-500' : 'text-slate-500 group-hover:text-purple-400' }}"></i>
                    <span class="tracking-wide text-sm">Users</span>
                </div>
                <i class="bi bi-chevron-down text-[10px] transition-transform duration-300"
                   :class="open ? 'rotate-180 text-purple-400' : 'text-slate-600'"></i>
            </button>

            <div x-show="open" x-collapse
                 class="mt-1 space-y-1 pl-11 pr-2 border-l-2 border-slate-800 ml-6">
                <a href="{{ route('users.index') }}"
                   class="block px-3 py-2 text-sm rounded-lg transition-colors
                   {{ request()->routeIs('users.index') ? 'text-purple-400 bg-purple-600/5 font-medium' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                   User List
                </a>
                <a href="{{ route('roles.index') }}"
                   class="block px-3 py-2 text-sm rounded-lg transition-colors
                   {{ request()->routeIs('roles.index') ? 'text-purple-400 bg-purple-600/5 font-medium' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                   Roles & Permissions
                </a>
            </div>
        </div>
        @endrole

        @role('admin')
        <!-- Section Label -->
        <div class="px-4 mt-6 mb-2 text-[10px] font-bold text-slate-500 uppercase tracking-widest leading-none">Configuration</div>
        
        <a href="{{ route('business.settings') }}"
            class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 ease-in-out
           {{ request()->routeIs('business.settings')
               ? 'bg-amber-600/10 text-amber-400 font-medium'
               : 'hover:bg-slate-800/50 hover:text-white' }}">
            <i class="bi bi-gear text-xl transition-transform group-hover:rotate-90 {{ request()->routeIs('business.settings') ? 'text-amber-500' : 'text-slate-500 group-hover:text-amber-400' }}"></i>
            <span class="tracking-wide text-sm">Business Settings</span>
        </a>
        @endrole

    </nav>
</aside>
