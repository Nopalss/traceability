(() => {
    // ===============================
    // ELEMENT & API
    // ===============================
    const container = document.getElementById('scan-container');
    if (!container) return;

    const apiScan = container.dataset.apiScan;
    const apiAdd = container.dataset.apiAdd;

    if (!apiScan) {
        console.error('API SCAN tidak ditemukan');
        return;
    }

    const btnExecute = document.getElementById('btn-execute');
    const btnAdd = document.getElementById('btn-add');

    const inputRaw = document.getElementById('qr_raw');
    const alertBox = document.getElementById('scan-alert');
    const rawSaved = document.getElementById('qr_raw_saved');

    const field = {
        part_code: document.getElementById('part_code'),
        lot_no: document.getElementById('lot_no'),
        qty: document.getElementById('qty'),
        ref_no: document.getElementById('ref_no'),
        remarks: document.getElementById('remarks')
    };

    // ===============================
    // ALERT
    // ===============================
    function showAlert(type, message) {
        if (!alertBox) return;

        alertBox.classList.remove('d-none', 'alert-success', 'alert-danger');
        alertBox.classList.add(type === 'success' ? 'alert-success' : 'alert-danger');
        alertBox.textContent = message;

        setTimeout(() => {
            alertBox.classList.add('d-none');
        }, 5000);
    }

    // ===============================
    // FORM HANDLER
    // ===============================
    function fillForm(data = {}) {
        field.part_code.value = data.part_code || '';
        field.lot_no.value = data.lot_no || '';
        field.qty.value = data.qty || '';
        field.ref_no.value = data.ref_no || '';
        field.remarks.value = data.remarks || '';
    }

    function clearForm() {
        Object.values(field).forEach(el => el.value = '');
        rawSaved.value = '';
        btnAdd.disabled = true;
    }

    // ===============================
    // EXECUTE (SCAN & PARSE SAJA)
    // ===============================
    btnExecute.addEventListener('click', () => {
        const raw = inputRaw.value.trim();

        if (!raw) {
            showAlert('error', 'QR Code belum diisi');
            return;
        }

        btnExecute.disabled = true;

        fetch(apiScan, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ qr_raw: raw })
        })
            .then(res => res.json())
            .then(res => {
                if (!res.success) {
                    showAlert('error', res.message || 'QR tidak valid');

                    clearForm();          // ✅ PENTING
                    btnExecute.disabled = false;
                    return;
                }

                // ✅ SUCCESS BARU ISI FORM
                fillForm(res.data);
                rawSaved.value = raw;
                btnAdd.disabled = false;

                showAlert('success', 'QR berhasil diparsing');
                btnExecute.disabled = false;
            })
            .catch(err => {
                console.error(err);
                showAlert('error', 'Gagal koneksi ke server');
                clearForm();
                btnExecute.disabled = false;
            });
    });


    // ===============================
    // ADD (SIMPAN DATA)
    // ===============================
    btnAdd.addEventListener('click', () => {
        if (!apiAdd) {
            showAlert('error', 'API Add tidak tersedia');
            return;
        }

        const raw = rawSaved.value;
        if (!raw) {
            showAlert('error', 'Data QR belum diproses');
            return;
        }

        btnAdd.disabled = true;

        fetch(apiAdd, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ qr_raw: raw })
        })
            .then(res => res.json())
            .then(res => {
                if (!res.success) {
                    showAlert('error', res.message || 'Gagal simpan data');
                    btnAdd.disabled = false;
                    return;
                }

                showAlert('success', 'Incoming part berhasil disimpan');
                clearForm();
                inputRaw.value = '';
            })
            .catch(err => {
                console.error(err);
                showAlert('error', 'Gagal koneksi ke server');
                btnAdd.disabled = false;
            });
    });

})();
