import Swal from 'sweetalert2';

const toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 2200,
    timerProgressBar: true,
});

export function success(message = 'Berhasil') {
    toast.fire({ icon: 'success', title: message });
}

export function error(message = 'Terjadi kesalahan') {
    toast.fire({ icon: 'error', title: message });
}

export function info(message = 'Info') {
    toast.fire({ icon: 'info', title: message });
}

export default { success, error, info };

