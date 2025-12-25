import './bootstrap';
import './login';


toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: "toast-top-right",
    timeOut: 3000,
    newestOnTop: true,
    preventDuplicates: true,
    escapeHtml: false,
    target: 'body', // append to body
    toastClass: 'toastr toast show', // enforce Bootstrap stacking
};
