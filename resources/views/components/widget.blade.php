<div style="color: #333;" class="mb-4  flex flex-col flex-grow">
    <h2 class="font-semibold mb-2" style="color: #333;">{{ $title }}</h2>

    <!-- Content / Activity -->
    <div class="card-body text-gray-800 p-3 shadow-md bg-white overflow-auto">
        {{ $slot }}
    </div>
</div>
