<script type="text/javascript">
    console.log('Global JS loaded');

    // Load libraries
    const loadScripts = () => {
        const scripts = [
            "https://code.jquery.com/jquery-3.7.0.min.js",
            "https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js",
            "https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js",
            "https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js",
            "https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"
        ];

        scripts.forEach(src => {
            if (!document.querySelector(`script[src="${src}"]`)) {
                const tag = document.createElement('script');
                tag.src = src;
                document.head.appendChild(tag);
            }
        });

        // Setup AJAX CSRF
        $(document).ready(function() {
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });

            toastr.options = {
                closeButton: true,
                progressBar: true,
                positionClass: "toast-top-right",
                timeOut: 3000
            };
        });
    };

    loadScripts();

    // Page-specific JS
    @yield('page-js')
</script>
