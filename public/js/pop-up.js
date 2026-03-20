document.addEventListener('DOMContentLoaded', function () {
        var toastEl = document.getElementById('popupToast');
        var toast = new bootstrap.Toast(toastEl, { delay: 3000 });
        toast.show();
    });