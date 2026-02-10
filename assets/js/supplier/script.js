$(document).ready(function () {

    // ================================
    // HELPER VALIDATION FUNCTION (FINAL)
    // ================================
    function validateLineName(value) {
        const val = value.trim();

        // 1. wajib diisi
        if (!val) {
            return 'Nama line tidak boleh kosong!';
        }

        // 2. hanya alfanumerik & kapital
        if (!/^[A-Z0-9]+$/.test(val)) {
            return 'Hanya boleh huruf KAPITAL (A-Z) dan angka (0-9)!';
        }

        // 3. tidak boleh semua nol (0 / 00 / 000)
        if (/^0+$/.test(val)) {
            return 'Nama line tidak boleh hanya berisi angka 0!';
        }

        // 4. tidak boleh BERAKHIR dengan nol semua (A000, LINE00)
        //    harus ada huruf atau angka NON-ZERO di AKHIR
        if (/0+$/.test(val) && !/[1-9A-Z]$/.test(val)) {
            return 'Nama line tidak boleh diakhiri dengan nol saja!';
        }

        return null;
    }

    // ---------------------------------
    // ADD LINE
    // ---------------------------------
    $('#addLineBtn').on('click', function () {
        Swal.fire({
            title: 'Tambahkan Data Line Baru',
            input: 'text',
            inputLabel: 'Nama Line',
            inputPlaceholder: 'Contoh: LINE01, A001',
            showCancelButton: true,
            confirmButtonText: 'Tambahkan',
            cancelButtonText: 'Batal',
            inputAttributes: {
                autocapitalize: 'characters'
            },
            inputValidator: (value) => {
                return validateLineName(value);
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const lineName = result.value.trim();

                Swal.fire({
                    title: 'Menyimpan...',
                    text: 'Mohon tunggu sebentar.',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                fetch(`${HOST_URL}controllers/line/create.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ line_name: lineName })
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
    // EDIT LINE
    // ---------------------------------
    $('#kt_datatable').on('click', '.editLineBtn', function () {
        const lineId = $(this).data('id');
        const currentLineName = $(this).data('name');

        Swal.fire({
            title: 'Edit Data Line',
            input: 'text',
            inputLabel: 'Nama Line',
            inputValue: currentLineName,
            inputPlaceholder: 'Contoh: LINE01, A001',
            showCancelButton: true,
            confirmButtonText: 'Simpan Perubahan',
            cancelButtonText: 'Batal',
            inputAttributes: {
                autocapitalize: 'characters'
            },
            inputValidator: (value) => {
                return validateLineName(value);
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const newLineName = result.value.trim();

                if (newLineName === currentLineName) {
                    Swal.fire('Tidak ada perubahan', '', 'info');
                    return;
                }

                Swal.fire({
                    title: 'Menyimpan...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                fetch(`${HOST_URL}controllers/line/edit.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        line_id: lineId,
                        line_name: newLineName
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
