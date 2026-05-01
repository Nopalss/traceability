(() => {

    const container = document.getElementById('scan-container');
    if (!container) return;

    const apiScan = container.dataset.apiScan;
    if (!apiScan) {
        console.error('API Scan tidak ditemukan');
        return;
    }

    const btnExecute = document.getElementById('btn-execute');
    const inputRaw = document.getElementById('qr_raw');
    const supplier = document.getElementById('supplier');
    const alertBox = document.getElementById('scan-alert');

    const field = {
        part_code: document.getElementById('part_code'),
        lot_no: document.getElementById('lot_no'),
        qty: document.getElementById('qty'),
        ref_no: document.getElementById('ref_no'),
        remarks: document.getElementById('remarks')
    };

    /* ===============================
       AUTO FOCUS (SANTAI + AMAN)
    =============================== */


    // fokus saat halaman load
    // window.addEventListener('load', () => {
    //     inputRaw.focus();
    // });

    // jika user mengetik apapun di halaman dan textarea kosong
    document.addEventListener('keydown', function (e) {

        if (document.activeElement !== inputRaw) {

            // kalau scanner mulai mengetik
            if (e.key.length === 1) {
                inputRaw.focus();
            }

        }

    });

    /* ===============================
       DETECT SCAN (ENTER / NEWLINE)
    =============================== */

    inputRaw.addEventListener('input', () => {

        const value = inputRaw.value;

        // scanner biasanya kirim newline di akhir
        if (value.includes('\n') || value.includes('\r')) {

            const cleaned = value.replace(/[\r\n]/g, '').trim();
            inputRaw.value = cleaned;

            btnExecute.click();
        }

    });


    /* ===============================
       ALERT
    =============================== */

    function showAlert(type, message) {

        if (!alertBox) return;

        alertBox.classList.remove('d-none', 'alert-success', 'alert-danger');
        alertBox.classList.add(type === 'success' ? 'alert-success' : 'alert-danger');
        alertBox.textContent = message;

        setTimeout(() => {
            alertBox.classList.add('d-none');
        }, 4000);
    }


    /* ===============================
       FORM
    =============================== */

    function fillForm(data = {}) {

        field.part_code.value = data.part_code || '';
        field.lot_no.value = data.lot_no || '';
        field.qty.value = data.qty || '';
        field.ref_no.value = data.ref_no || '';
        field.remarks.value = data.remarks || '';

    }


    /* ===============================
       CLEAR RAW
    =============================== */

    function clearRaw() {

        inputRaw.value = '';
        inputRaw.focus();// balik fokus setelah scan selesai

    }


    /* ===============================
       EXECUTE
    =============================== */

    btnExecute.addEventListener('click', () => {

        const raw = inputRaw.value.trim();

        if (!raw) {
            showAlert('error', 'QR Code belum diisi');
            return;
        }

        if (!supplier.value) {
            showAlert('error', 'Pilih supplier terlebih dahulu');
            supplier.focus();
            return;
        }

        btnExecute.disabled = true;

        fetch(apiScan, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                qr_raw: raw,
                supplier: supplier.value
            })
        })
            .then(res => {
                if (!res.ok) throw new Error("HTTP error");
                return res.json();
            })
            .then(res => {

                btnExecute.disabled = false;

                if (!res.success) {
                    showAlert('error', res.message || 'QR tidak valid');
                    clearRaw();
                    return;
                }

                if (res.data) fillForm(res.data);

                showAlert('success', res.message || 'Incoming berhasil');

                clearRaw();

            })
            .catch(err => {

                console.error(err);
                btnExecute.disabled = false;
                showAlert('error', 'Server tidak merespon');
                clearRaw();

            });

    });

})();