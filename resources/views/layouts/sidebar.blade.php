<!-- Sidebar -->
<aside class="w-full h-full bg-white shadow-xl rounded-r-2xl py-6 sidebar-animate overflow-y-auto border-r border-slate-100 font-sans">
    <nav class="px-3 space-y-2">
        {!! \App\Helpers\Menu::render(auth()->user()->role ?? null) !!}
    </nav>
</aside>
