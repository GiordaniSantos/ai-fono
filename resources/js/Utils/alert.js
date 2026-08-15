import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

export const Toast = Swal.mixin({
    toast: true,
    position: 'bottom-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    },
});

export const confirmAction = async ({
    title = 'Tem certeza?',
    text = 'Esta ação não poderá ser desfeita!',
    confirmButtonText = 'Sim, confirmar',
    cancelButtonText = 'Cancelar',
} = {}) => {
    return await Swal.fire({
        title,
        text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#2563eb',
        cancelButtonColor: '#6b7280',
        confirmButtonText,
        cancelButtonText,
        reverseButtons: true,
        customClass: {
            popup: 'rounded-2xl',
        },
    });
};