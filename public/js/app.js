$(document).ready(function () {
    // Target links that are parent of a submenu
    $('.nav > li > a[href="#"]').on('click', function (e) {
        e.preventDefault();

        var $submenu = $(this).next('ul'); // the submenu <ul>

        // Toggle visibility
        $submenu.slideToggle(200); // smooth toggle
    });
});
