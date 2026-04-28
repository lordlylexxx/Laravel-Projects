{{-- SweetAlert2: confirm, then POST via fetch (JSON) so CSRF + cookies are sent and no redirect/flash issues. --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function () {
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('form[data-swal-confirm]').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                var title = form.getAttribute('data-swal-title') || 'Are you sure?';
                var text = form.getAttribute('data-swal-text') || '';
                var confirmText = form.getAttribute('data-swal-confirm-button') || 'Yes, continue';
                var cancelText = form.getAttribute('data-swal-cancel-button') || 'Cancel';
                var icon = form.getAttribute('data-swal-icon') || 'warning';
                var confirmColor = form.getAttribute('data-swal-confirm-color') || '#15803d';
                var cancelColor = form.getAttribute('data-swal-cancel-color') || '#64748b';
                Swal.fire({
                    title: title,
                    text: text,
                    icon: icon,
                    showCancelButton: true,
                    confirmButtonColor: confirmColor,
                    cancelButtonColor: cancelColor,
                    confirmButtonText: confirmText,
                    cancelButtonText: cancelText,
                    focusCancel: true,
                }).then(function (result) {
                    if (!result.isConfirmed) {
                        return;
                    }
                    var meta = document.querySelector('meta[name="csrf-token"]');
                    var csrf = meta ? meta.getAttribute('content') : '';
                    if (!csrf) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Missing security token',
                            text: 'Reload the page and try again.',
                        });
                        return;
                    }
                    var formData = new FormData(form);
                    formData.set('_token', csrf);

                    Swal.fire({
                        title: 'Working…',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: function () {
                            Swal.showLoading();
                        },
                    });

                    fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'X-Requested-With': 'XMLHttpRequest',
                            Accept: 'application/json',
                        },
                        body: formData,
                        credentials: 'same-origin',
                    })
                        .then(function (response) {
                            return response
                                .json()
                                .then(function (data) {
                                    return { response: response, data: data };
                                })
                                .catch(function () {
                                    return { response: response, data: {} };
                                });
                        })
                        .then(function (pair) {
                            var response = pair.response;
                            var data = pair.data;
                            Swal.close();
                            if (response.status === 419) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Page expired',
                                    text: 'Refresh the page and try again.',
                                });
                                return;
                            }
                            if (response.status === 422) {
                                var errText =
                                    (data && data.message) ||
                                    (data.errors
                                        ? Object.values(data.errors)
                                              .flat()
                                              .join(' ')
                                        : 'Request could not be completed.');
                                Swal.fire({ icon: 'error', title: 'Error', text: errText });
                                return;
                            }
                            if (!response.ok) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: (data && data.message) || 'Something went wrong.',
                                });
                                return;
                            }
                            var successMsg = (data && data.message) || 'Done.';
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: successMsg,
                                confirmButtonColor: '#15803d',
                            }).then(function () {
                                window.location.reload();
                            });
                        })
                        .catch(function () {
                            Swal.close();
                            Swal.fire({
                                icon: 'error',
                                title: 'Network error',
                                text: 'Check your connection and try again.',
                            });
                        });
                });
            });
        });
    });
})();
</script>
