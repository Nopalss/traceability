$(document).ready(function () {

    // ===========================
    // GENERATE JAM (00–23)
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
    function generateHourOptions2(selected = null) {
        let html = '';
        for (let i = 0; i < 24; i++) {
            const h = i.toString().padStart(2, '0');
            const isSelected = selected == i ? 'selected' : '';
            html += `<option value="${i}" ${isSelected}>${h}</option>`;
        }
        return html;
    }

    // ===========================
    // GENERATE MENIT
    // ===========================
    function generateMinuteOptions(selected = null) {
        let html = '';
        for (let i = 0; i < 60; i++) {
            const m = i.toString().padStart(2, '0');
            const isSelected = selected == i ? 'selected' : '';
            html += `<option value="${i}" ${isSelected}>${m}</option>`;
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
    // SPLIT MENIT → JAM & MENIT
    // ===========================
    function splitTime(totalMinutes) {
        totalMinutes = parseInt(totalMinutes || 0);
        return {
            hour: Math.floor(totalMinutes / 60),
            minute: totalMinutes % 60
        };
    }

    // ===========================
    // ADD SHIFT
    // ===========================
    $('#addShiftBtn').on('click', function () {

        Swal.fire({
            title: 'Tambah Shift',
            html: `
                <input id="shift_name" class="swal2-input" placeholder="Nama Shift">

                <div>Jam Mulai</div>
                <select id="start_hour">${generateHourOptions()}</select>

                <div>Jam Akhir</div>
                <select id="end_hour">${generateHourOptions()}</select>

                <hr>

                <b>Coffee Break</b><br>
                <select id="coffee_hour">${generateHourOptions2()}</select> :
                <select id="coffee_minute">${generateMinuteOptions()}</select>

                <input id="duration_time" class="swal2-input" placeholder="Durasi Coffee (menit)">

                <b>Istirahat Makan</b><br>
                <select id="makan_hour">${generateHourOptions2()}</select> :
                <select id="makan_minute">${generateMinuteOptions()}</select>

                <input id="duration_bm" class="swal2-input" placeholder="Durasi Makan (menit)">
            `,
            showCancelButton: true,
            confirmButtonText: 'Simpan',

            preConfirm: () => {

                const shift = $('#shift_name').val().trim();

                // ✅ tetap jam
                const start = $('#start_hour').val();
                const end = $('#end_hour').val();

                // ✅ jadi menit
                const time_coffe = (parseInt($('#coffee_hour').val()) * 60) + parseInt($('#coffee_minute').val());
                const break_makan = (parseInt($('#makan_hour').val()) * 60) + parseInt($('#makan_minute').val());

                const duration_time = $('#duration_time').val();
                const duration_bm = $('#duration_bm').val();

                const error = validateShift(shift);
                if (error) {
                    Swal.showValidationMessage(error);
                    return false;
                }

                return {
                    shift,
                    start,
                    end,
                    time_coffe,
                    duration_time,
                    break_makan,
                    duration_bm
                };
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

        // ✅ tetap jam
        const startHour = $(this).data('start');
        const endHour = $(this).data('end');

        // ✅ convert menit → jam+menit
        const coffee = splitTime($(this).data('time_coffe'));
        const makan = splitTime($(this).data('break_makan'));

        const durationTime = $(this).data('duration_time') || 0;
        const durationBm = $(this).data('duration_bm') || 0;

        Swal.fire({
            title: 'Edit Shift',
            html: `
                <input id="shift_name" class="swal2-input" value="${shiftName}">

                <div>Jam Mulai</div>
                <select id="start_hour">${generateHourOptions(startHour)}</select>

                <div>Jam Akhir</div>
                <select id="end_hour">${generateHourOptions(endHour)}</select>

                <hr>

                <b>Coffee Break</b><br>
                <select id="coffee_hour">${generateHourOptions2(coffee.hour)}</select> :
                <select id="coffee_minute">${generateMinuteOptions(coffee.minute)}</select>

                <input id="duration_time" class="swal2-input" value="${durationTime}" placeholder="Durasi Coffee">

                <b>Istirahat Makan</b><br>
                <select id="makan_hour">${generateHourOptions2(makan.hour)}</select> :
                <select id="makan_minute">${generateMinuteOptions(makan.minute)}</select>

                <input id="duration_bm" class="swal2-input" value="${durationBm}" placeholder="Durasi Makan">
            `,
            showCancelButton: true,
            confirmButtonText: 'Update',

            preConfirm: () => {

                const shift = $('#shift_name').val().trim();

                const start = $('#start_hour').val();
                const end = $('#end_hour').val();

                const time_coffe = (parseInt($('#coffee_hour').val()) * 60) + parseInt($('#coffee_minute').val());
                const break_makan = (parseInt($('#makan_hour').val()) * 60) + parseInt($('#makan_minute').val());

                const duration_time = $('#duration_time').val();
                const duration_bm = $('#duration_bm').val();

                const error = validateShift(shift);
                if (error) {
                    Swal.showValidationMessage(error);
                    return false;
                }

                return {
                    shift_id: id,
                    shift,
                    start,
                    end,
                    time_coffe,
                    duration_time,
                    break_makan,
                    duration_bm
                };
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