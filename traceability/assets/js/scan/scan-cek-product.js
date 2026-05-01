(() => {
    // ===============================
    // STATE & CONFIG
    // ===============================
    let isProcessing = false;
    let lastRaw = null;
    let lastScanTime = 0;
    const SCAN_COOLDOWN = 2000;

    // ===============================
    // ELEMENT & API
    // ===============================
    const container = document.getElementById('scan-container');
    if (!container) return;

    const apiScan = container.dataset.apiScan;
    if (!apiScan) {
        console.error('API scan product tidak ditemukan');
        return;
    }

    function formatTanggal(dateString, format = 'indo', withTime = false) {
        if (!dateString) return '-';

        // amankan format MySQL -> ISO
        const date = new Date(dateString.replace(' ', 'T'));
        if (isNaN(date)) return '-';

        // ===============================
        // FORMAT DATABASE (YYYY-MM-DD)
        // ===============================
        if (format === 'ymd') {
            const y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const d = String(date.getDate()).padStart(2, '0');
            return `${y}-${m}-${d}`;
        }

        // ===============================
        // FORMAT INDONESIA
        // ===============================
        const options = {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        };

        let result = date.toLocaleDateString('id-ID', options);

        if (withTime) {
            const time = date.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit'
            });
            result += ` ${time}`;
        }

        return result;
    }
    const btnExecute = document.getElementById('btn-execute');
    const inputRaw = document.getElementById('qr_raw');
    const alertBox = document.getElementById('scan-alert');

    // ===============================
    // FORM PRODUCT
    // ===============================
    const productField = {
        ref_product: document.getElementById('ref_product'),
        product_code: document.getElementById('product_code'),
        product_name: document.getElementById('product_name'),
        production_at: document.getElementById('production_at'),
        status: document.getElementById('status'),
        tujuan: document.getElementById('tujuan'),
        delivery_date: document.getElementById('delivery_date')
    };

    const partTableBody = document.getElementById('part-used-body');

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
    // CLEAR UI
    // ===============================
    function clearUI() {
        Object.values(productField).forEach(el => el && (el.value = ''));

        if (partTableBody) {
            partTableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-muted">
                        Belum ada data
                    </td>
                </tr>
            `;
        }
    }

    // ===============================
    // RENDER PART (TRACEABILITY)
    // ===============================
    function renderTraceability(data) {
        if (!Array.isArray(data) || data.length === 0) {
            clearUI();
            return;
        }

        // tampilkan PRODUCT dari production pertama
        const first = data[0].production;

        productField.ref_product.value = first.ref_product;
        productField.product_code.value = first.product_code;
        productField.product_name.value = first.product_name;
        productField.production_at.value = formatTanggal(first.production_at);
        productField.status.value = first.status;

        productField.tujuan.value = "PT XYZ";
        productField.delivery_date.value = formatTanggal("2026-01-30");

        // render PART
        let rows = '';

        data.forEach(item => {
            const prod = item.production;
            const parts = item.parts;

            if (!parts || parts.length === 0) {
                rows += `
                    <tr>
                        <td colspan="6" class="text-muted text-center">
                            Tidak ada part untuk ref ${prod.ref_number}
                        </td>
                    </tr>
                `;
                return;
            }

            parts.forEach(p => {
                rows += `
                    <tr>
                        <td>${prod.ref_number}</td>
                        <td>${p.part_code}</td>
                        <td>${p.part_name}</td>
                        <td>${p.supplier || '-'}</td>
                        <td> <a href="" class="font-weight-bolder">Detail</a>
                        </td>
                    </tr>
                `;
            });
        });
        partTableBody.innerHTML = rows;
    }

    // ===============================
    // HANDLE EXECUTE
    // ===============================
    btnExecute.addEventListener('click', () => {
        const raw = inputRaw.value.trim();
        const now = Date.now();

        if (!raw) {
            showAlert('error', 'QR Code belum diisi');
            return;
        }

        if (isProcessing || (raw === lastRaw && now - lastScanTime < SCAN_COOLDOWN)) {
            return;
        }

        isProcessing = true;
        lastRaw = raw;
        lastScanTime = now;
        btnExecute.disabled = true;

        fetch(apiScan, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ qr_raw: raw })
        })
            .then(res => res.json())
            .then(res => {
                if (!res.success) {
                    showAlert('error', res.message || 'Product tidak valid');
                    clearUI();
                    return;
                }

                renderTraceability(res.data);
                showAlert('success', 'Traceability product berhasil');
            })
            .catch(err => {
                console.error(err);
                showAlert('error', 'Gagal koneksi ke server');
                clearUI();
            })
            .finally(() => {
                setTimeout(() => {
                    isProcessing = false;
                    btnExecute.disabled = false;
                }, SCAN_COOLDOWN);
            });
    });

})();
