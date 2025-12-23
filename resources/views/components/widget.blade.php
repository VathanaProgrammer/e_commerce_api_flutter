<div class="mb-4 p-4 bg-gray-300 flex flex-col flex-grow">
    <h2 class="font-semibold text-gray-800 mb-2">{{ $title }}</h2>

    <!-- Content / Activity -->
    <div class="card-body text-gray-800 p-3 shadow-md bg-white overflow-auto">
        {{ $slot }}
    </div>
</div>
