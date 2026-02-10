$(document).ready(function () {

    // ===========================
    // GENERATE JAM OPTION 00–23
    // ===========================
    function generateHourOptions(selected = null) {
        let html = '';

        for (let i = 0; i < 24; i++) {
            const h = i.toString().padStart(2, '0');
            const isSelected = selected == i ? 'selected' : '';
            html += `<option value="${i}" ${isSelected}>${h}:00</option>`;
        }

        return html;
    }

    // ===========================
    // VALIDATION
    // ===========================
    function validateShift(shift) {

        if (!shift) return 'Nama shift wajib diisi!';

        return null;
    }

    // ===========================
    // ADD SHIFT
    // ===========================
    $('#addShiftBtn').on('click', function () {

        Swal.fire({
            title: 'Tambah Shift',
            html: `
                <input id="shift_name" class="swal2-input" placeholder="Nama Shift">

                <div class="d-flex align-items-center">
                    <p style="float:left;margin-left:35px">Jam Mulai</p>
                    <select id="start_hour" class="swal2-select">
                    ${generateHourOptions()}
                    </select>
                </div>
                <div class="d-flex align-items-center">
                    <p style="float:left;margin-left:35px">Jam Akhir</p>
                    <select id="end_hour" class="swal2-select">
                    ${generateHourOptions()}
                    </select>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Simpan',
            focusConfirm: false,

            preConfirm: () => {

                const shift = $('#shift_name').val().trim();
                const start = $('#start_hour').val();
                const end = $('#end_hour').val();

                const error = validateShift(shift);

                if (error) {
                    Swal.showValidationMessage(error);
                    return false;
                }

                return { shift, start, end };
            }

        }).then(result => {

            if (result.isConfirmed) {

                Swal.fire({
                    title: 'Menyimpan...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                fetch(`${HOST_URL}controllers/shift/create.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(result.value)
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
                    .catch(err => Swal.fire('Error', err.message, 'error'));
            }

        });

    });

    // ===========================
    // EDIT SHIFT
    // ===========================
    $('#kt_datatable').on('click', '.editShiftBtn', function () {

        const id = $(this).data('id');
        const shiftName = $(this).data('shift');
        const startHour = $(this).data('start');
        const endHour = $(this).data('end');

        Swal.fire({
            title: 'Edit Shift',
            html: `
                <input id="shift_name" class="swal2-input" value="${shiftName}">

                <div class="d-flex align-items-center">
                    <label style="float:left;margin-left:35px">Jam Mulai</label>
                    <select id="start_hour" class="swal2-select">
                    ${generateHourOptions(startHour)}
                    </select>
                </div>
                
                <div class="d-flex align-items-center">
                    <label style="float:left;margin-left:35px">Jam Akhir</label>
                    <select id="end_hour" class="swal2-select">
                        ${generateHourOptions(endHour)}
                    </select>
                </div>

            `,
            showCancelButton: true,
            confirmButtonText: 'Update',
            focusConfirm: false,

            preConfirm: () => {

                const shift = $('#shift_name').val().trim();
                const start = $('#start_hour').val();
                const end = $('#end_hour').val();

                const error = validateShift(shift);

                if (error) {
                    Swal.showValidationMessage(error);
                    return false;
                }

                return { shift_id: id, shift, start, end };
            }

        }).then(result => {

            if (result.isConfirmed) {

                Swal.fire({
                    title: 'Menyimpan...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                fetch(`${HOST_URL}controllers/shift/edit.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(result.value)
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
                    .catch(err => Swal.fire('Error', err.message, 'error'));
            }

        });

    });

});
