@vite(['resources/js/app.js'])

<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if(session('success'))
            toastr.success(@json(session('success')));
        @endif

        @if(session('error'))
            toastr.error(@json(session('error')));
        @endif

        @if(session('warning'))
            toastr.warning(@json(session('warning')));
        @endif

        @if(session('info'))
            toastr.info(@json(session('info')));
        @endif
    });

    document.addEventListener('submit', function (e) {
        const form = e.target.closest('.js-confirm-form');

        if (!form) return;

        if (form.dataset.confirmed === '1') {
            return;
        }

        e.preventDefault();

        Swal.fire({
            title: form.dataset.title || 'تأكيد الإجراء',
            text: form.dataset.text || 'هل أنت متأكد من تنفيذ هذا الإجراء؟',
            icon: form.dataset.icon || 'warning',
            showCancelButton: true,
            confirmButtonText: form.dataset.confirmText || 'نعم',
            cancelButtonText: form.dataset.cancelText || 'إلغاء',
            reverseButtons: true,
            confirmButtonColor: form.dataset.confirmColor || '#073f22',
            cancelButtonColor: form.dataset.cancelColor || '#6b7280',
            customClass: {
                popup: 'nk-swal-popup',
                title: 'nk-swal-title',
                htmlContainer: 'nk-swal-text',
                confirmButton: 'nk-swal-confirm',
                cancelButton: 'nk-swal-cancel',
            }
        }).then((result) => {
            if (result.isConfirmed) {
                form.dataset.confirmed = '1';
                form.submit();
            }
        });
    });
</script>