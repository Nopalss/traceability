$(document).ready(function () {

    // ================================
    // VALIDATION SUPPLIER
    // ================================
    function validateSupplierName(value) {
        const val = value.trim();

        if (!val) {
            return 'Nama supplier tidak boleh kosong!';
        }

        if (val.length < 3) {
            return 'Nama supplier minimal 3 karakter!';
        }

        return null;
    }

    // ---------------------------------
    // ADD SUPPLIER
    // ---------------------------------
    $('#addSupplierBtn').on('click', function () {

        Swal.fire({
            title: 'Tambah Supplier Baru',
            input: 'text',
            inputLabel: 'Nama Supplier',
            inputPlaceholder: 'Contoh: PT. ABC',
            showCancelButton: true,
            confirmButtonText: 'Tambahkan',
            cancelButtonText: 'Batal',
            inputValidator: (value) => {
                return validateSupplierName(value);
            }

        }).then((result) => {

            if (result.isConfirmed) {

                const supplierName = result.value.trim();

                Swal.fire({
                    title: 'Menyimpan...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                fetch(`${HOST_URL}controllers/supplier/create.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        name_supplier: supplierName
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
    // EDIT SUPPLIER
    // ---------------------------------
    $('#kt_datatable').on('click', '.editSupplierBtn', function () {

        const supplierId = $(this).data('id');
        const currentName = $(this).data('name');

        Swal.fire({
            title: 'Edit Supplier',
            input: 'text',
            inputLabel: 'Nama Supplier',
            inputValue: currentName,
            inputPlaceholder: 'Contoh: PT. ABC',
            showCancelButton: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
            inputValidator: (value) => {

                const val = value.trim();

                if (!val) return 'Nama supplier tidak boleh kosong!';
                if (val.length < 3) return 'Nama supplier minimal 3 karakter!';

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

                fetch(`${HOST_URL}controllers/supplier/edit.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        id_supplier: supplierId,
                        name_supplier: newName
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