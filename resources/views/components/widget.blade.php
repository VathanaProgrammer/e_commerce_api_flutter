<div style="color: #333;" class="mb-4 flex flex-col flex-grow widget-animate">
    <!-- Widget Header -->
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="font-semibold mb-0 animate-fade-in-left" style="color: #333; font-size: 1.5rem;">
            {{ $title }}
        </h2>
        <div class="widget-header-line flex-grow-1 mx-3" style="height: 2px; background: linear-gradient(90deg, #667eea, transparent); border-radius: 2px;"></div>
    </div>

    <!-- Content / Activity -->
    <div class="card-body text-gray-800 p-4 shadow-sm bg-white overflow-auto rounded-lg border-0" 
         style="border-radius: 12px; transition: all 0.3s ease;">
        {{ $slot }}
    </div>
</div>
