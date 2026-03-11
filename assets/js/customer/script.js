$(document).ready(function () {

    // ================================
    // VALIDATION Customer
    // ================================
    function validateCustomerName(value) {
        const val = value.trim();

        if (!val) {
            return 'Nama Customer tidak boleh kosong!';
        }

        if (val.length < 3) {
            return 'Nama Customer minimal 3 karakter!';
        }

        return null;
    }

    // ---------------------------------
    // ADD Customer
    // ---------------------------------
    $('#addCustomerBtn').on('click', function () {

        Swal.fire({
            title: 'Tambah Customer Baru',
            input: 'text',
            inputLabel: 'Nama Customer',
            inputPlaceholder: 'Contoh: PT. ABC',
            showCancelButton: true,
            confirmButtonText: 'Tambahkan',
            cancelButtonText: 'Batal',
            inputValidator: (value) => {
                return validateCustomerName(value);
            }

        }).then((result) => {

            if (result.isConfirmed) {

                const CustomerName = result.value.trim();

                Swal.fire({
                    title: 'Menyimpan...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                fetch(`${HOST_URL}controllers/Customer/create.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        name_Customer: CustomerName
                    })
                })
                    .then(res => res.json())
                    .then(data => {

                        if (data.success) {
                            Swal.fire('Berhasil!', data.message, 'success')
                                .then(() => location.reload());
                        } else {
                            Swal.fire('Gagal!', data.message, 'error');
                        }

                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire('Oops...', err.message, 'error');
                    });
            }

        });
    });
    // ---------------------------------
    // EDIT Customer
    // ---------------------------------
    $('#kt_datatable').on('click', '.editCustomerBtn', function () {

        const CustomerId = $(this).data('id');
        const currentName = $(this).data('name');

        Swal.fire({
            title: 'Edit Customer',
            input: 'text',
            inputLabel: 'Nama Customer',
            inputValue: currentName,
            inputPlaceholder: 'Contoh: PT. ABC',
            showCancelButton: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
            inputValidator: (value) => {

                const val = value.trim();

                if (!val) return 'Nama Customer tidak boleh kosong!';
                if (val.length < 3) return 'Nama Customer minimal 3 karakter!';

                return null;
            }

        }).then((result) => {

            if (result.isConfirmed) {

                const newName = result.value.trim();

                if (newName === currentName) {
                    Swal.fire('Tidak ada perubahan', '', 'info');
                    return;
                }

                Swal.fire({
                    title: 'Menyimpan...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                fetch(`${HOST_URL}controllers/Customer/edit.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        id_Customer: CustomerId,
                        name_Customer: newName
                    })
                })
                    .then(res => res.json())
                    .then(data => {

                        if (data.success) {
                            Swal.fire('Berhasil!', data.message, 'success')
                                .then(() => location.reload());
                        } else {
                            Swal.fire('Gagal!', data.message, 'error');
                        }

                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire('Oops...', err.message, 'error');
                    });
            }

        });
    });

});