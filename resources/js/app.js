import 'bootstrap-icons/font/bootstrap-icons.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';

import DataTable from 'datatables.net-bs5';
import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';

import Swal from 'sweetalert2';
window.Swal = Swal;

// make it global (important)
window.DataTable = DataTable;

import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Wait a tiny bit to make sure DOM is fully ready
window.addEventListener('load', () => {
    const successMeta = document.querySelector('meta[name="flash-success"]');
    const successMessage = successMeta?.content?.trim();
    if (successMessage) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: successMessage,
            showConfirmButton: false,
            timer: 3000
        });
    }

    const errorMeta = document.querySelector('meta[name="flash-error"]');
    const errorMessage = errorMeta?.content?.trim();
    if (errorMessage) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',  // <--- red toast
            title: errorMessage,
            showConfirmButton: false,
            timer: 3000
        });
    }
});

// Delete User
window.confirmDelete = function(userId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(`delete-form-${userId}`).submit();
        }
    });
};

// Delete Category
function confirmDelete(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This category will be permanently deleted!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}

// Delete Dosage Form
window.confirmDeleteDosageForm = function(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This dosage form will be permanently deleted!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-dosageForm-' + id).submit();
        }
    });
}

// Delete medicine
window.confirmDeleteMedicine = function (id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This medicine will be permanently deleted!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-medicine-' + id).submit();
        }
    });
}

// Delete medicine batch number
window.confirmDeleteMedicineBatch = function (id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This medicine batch will be permanently deleted!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-medicine-batch-' + id).submit();
        }
    });
}




