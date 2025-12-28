<!-- Sidebar -->
<aside class="bg-white text-gray-900 flex flex-col w-64 min-h-screen shadow mt-4">
    <!-- Sidebar Menu -->
    <nav class="flex-1 px-2">

        <!-- Home -->
        <a href="{{ route('home') }}"
            class="flex items-center px-3 py-2 rounded hover:bg-gray-200 {{ request()->routeIs('home') ? 'bg-gray-200 font-semibold' : '' }}">
            <i class="bi bi-house me-2"></i> Home
        </a>

        <!-- Products Dropdown -->
        <!-- Products Dropdown -->
        @php $productActive = request()->routeIs('products.*') || request()->routeIs('categories.*'); @endphp
        <div x-data="{ open: {{ $productActive ? 'true' : 'false' }} }" class="space-y-1">
            <button @click="open = !open"
                class="flex justify-between items-center w-full px-3 py-2 rounded hover:bg-gray-200 {{ $productActive ? 'bg-gray-200 font-semibold' : '' }}">
                <span class="flex items-center">
                    <i class="bi bi-box-seam me-2"></i> Products
                </span>
                <i :class="open ? 'bi bi-chevron-up' : 'bi bi-chevron-down'"></i>
            </button>

            <div x-show="open" class="space-y-1 mt-2 pl-5">
                <!-- Product List -->
                <a href="{{ route('products.index') }}"
                    class="flex items-center px-3 py-2 rounded hover:bg-gray-300 {{ request()->routeIs('products.index') ? 'bg-gray-300 font-semibold' : '' }}">
                    <i class="bi bi-list-ul me-2"></i> Product Lists
                </a>

                <!-- Add Product -->
                <a href="{{ route('products.create') }}"
                    class="flex items-center px-3 py-2 rounded hover:bg-gray-300 {{ request()->routeIs('products.create') ? 'bg-gray-300 font-semibold' : '' }}">
                    <i class="bi bi-plus-square me-2"></i> Add Product
                </a>

                <a href="{{ route('attributes.index') }}"
                    class="flex items-center px-3 py-2 rounded hover:bg-gray-300 {{ request()->routeIs('attributes.index') ? 'bg-gray-300 font-semibold' : '' }}">
                    <i class="bi bi-plus-square me-2"></i> List Attribute
                </a>

                <!-- Categories inside Products -->
                <a href="{{ route('categories.index') }}"
                    class="flex items-center px-3 py-2 rounded hover:bg-gray-300 {{ request()->routeIs('categories.index') ? 'bg-gray-300 font-semibold' : '' }}">
                    <i class="bi bi-tags me-2"></i> Categories
                </a>
            </div>
        </div>

        @if (1 == 1)
            @php $userActive = request()->routeIs('users.*') || request()->routeIs('roles.*'); @endphp
            <!-- User Management Dropdown -->
            <div x-data="{ open: {{ $userActive ? 'true' : 'false' }} }" class="space-y-1">
                <button @click="open = !open"
                    class="flex justify-between items-center w-full px-3 py-2 rounded hover:bg-gray-200 {{ $userActive ? 'bg-gray-200 font-semibold' : '' }}">
                    <span class="flex items-center">
                        <i class="bi bi-people me-2"></i> User Management
                    </span>
                    <i :class="open ? 'bi bi-chevron-up' : 'bi bi-chevron-down'"></i>
                </button>
                <div x-show="open" class="space-y-1 pl-5">
                    <a href="{{ route('users.index') }}"
                        class="flex items-center px-3 py-2 rounded hover:bg-gray-300 {{ request()->routeIs('users.index') ? 'bg-gray-300 font-semibold' : '' }}">
                        <i class="bi bi-person me-2"></i> Users
                    </a>
                    <a href="{{ route('roles.index') }}"
                        class="flex items-center px-3 py-2 rounded hover:bg-gray-300 {{ request()->routeIs('roles.index') ? 'bg-gray-300 font-semibold' : '' }}">
                        <i class="bi bi-shield-lock me-2"></i> Roles
                    </a>
                </div>
            </div>
        @endif

    </nav>
</aside>
