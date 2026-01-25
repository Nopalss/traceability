(() => {
    let isProcessing = false;
    let alertTimer = null;

    let lastRaw = null;
    let lastScanTime = 0;
    const SCAN_COOLDOWN = 2000; // 2 detik

    // ===============================
    // ELEMENT & CONFIG
    // ===============================
    const container = document.getElementById('scan-container');
    if (!container) return;

    const API_URL = container.dataset.apiUrl;

    const inputScanner = document.getElementById('qr_raw');
    const alertBox = document.getElementById('scan-alert');

    const field = {
        part_code: document.getElementById('part_code'),
        lot_no: document.getElementById('lot_no'),
        qty: document.getElementById('qty'),
        ref_no: document.getElementById('ref_no'),
        remarks: document.getElementById('remarks')
    };

    // ===============================
    // ALERT HANDLER (5 DETIK)
    // ===============================
    function showAlert(type, message) {
        if (!alertBox) return;

        alertBox.classList.remove('d-none', 'alert-success', 'alert-danger');
        alertBox.classList.add(type === 'success' ? 'alert-success' : 'alert-danger');
        alertBox.textContent = message;

        if (alertTimer) clearTimeout(alertTimer);
        alertTimer = setTimeout(() => {
            alertBox.classList.add('d-none');
        }, 5000);
    }

    // ===============================
    // LOCAL STORAGE (LOG HARIAN)
    // ===============================
    const todayKey = 'scan_log_' + new Date().toISOString().slice(0, 10);

    function getScanLogs() {
        return JSON.parse(localStorage.getItem(todayKey) || '[]');
    }

    function saveScanLog({ part_code, ref_no, status }) {
        const logs = getScanLogs();
        const now = Date.now();

        // cegah spam 1 detik
        if (
            logs[0] &&
            logs[0].part_code === (part_code || '-') &&
            logs[0].ref_no === (ref_no || '-') &&
            logs[0].status === status &&
            (now - (logs[0].timestamp || 0)) < 1000
        ) {
            return;
        }

        logs.unshift({
            part_code: part_code || '-',
            ref_no: ref_no || '-',
            status,
            time: new Date().toLocaleTimeString(),
            timestamp: now
        });

        localStorage.setItem(todayKey, JSON.stringify(logs));
        renderScanTable();
    }

    function renderScanTable() {
        const tbody = document.getElementById('scan-log-body');
        if (!tbody) return;

        const logs = getScanLogs();

        if (logs.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center text-muted">
                        Belum ada scan
                    </td>
                </tr>`;
            return;
        }

        tbody.innerHTML = logs.map(log => `
            <tr>
                <td>${log.part_code}</td>
                <td>${log.ref_no}</td>
                <td>
                    <span class="badge ${log.status === 'SUCCESS' ? 'badge-success' : 'badge-danger'}">
                        ${log.status}
                    </span>
                </td>
                <td>${log.time}</td>
            </tr>
        `).join('');
    }

    // ===============================
    // HANDLE SCAN (STABIL)
    // ===============================
    function handleScan(raw) {
        const now = Date.now();
        if (!raw) return;

        if (isProcessing || (now - lastScanTime < SCAN_COOLDOWN)) return;
        if (raw === lastRaw && (now - lastScanTime < SCAN_COOLDOWN)) return;

        isProcessing = true;
        lastRaw = raw;
        lastScanTime = now;

        fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ qr_raw: raw })
        })
            .then(res => res.json())
            .then(res => {
                if (!res.success) {
                    showAlert('error', res.message || 'Scan gagal');

                    const partMatch = raw.match(/Z1([^Z]+)/);
                    const refMatch = raw.match(/Z5([^Z]+)/);

                    saveScanLog({
                        part_code: partMatch ? partMatch[1] : '-',
                        ref_no: refMatch ? refMatch[1] : '-',
                        status: 'ERROR'
                    });
                    return;
                }

                field.part_code.value = res.data.part_code;
                field.lot_no.value = res.data.lot_no;
                field.qty.value = res.data.qty;
                field.ref_no.value = res.data.ref_no;
                field.remarks.value = res.data.remarks;

                showAlert('success', res.message || 'Incoming part berhasil');

                saveScanLog({
                    part_code: res.data.part_code,
                    ref_no: res.data.ref_no,
                    status: 'SUCCESS'
                });
            })
            .catch(() => {
                showAlert('error', 'Gagal koneksi ke server');
                saveScanLog({ part_code: '-', ref_no: '-', status: 'ERROR' });
            })
            .finally(() => {
                setTimeout(() => {
                    isProcessing = false;
                }, SCAN_COOLDOWN);
            });
    }

    // ===============================
    // MODE SCANNER GUN
    // ===============================
    if (inputScanner) {
        inputScanner.addEventListener('keypress', e => {
            if (e.key === 'Enter') {
                e.preventDefault();
                handleScan(inputScanner.value.trim());
                inputScanner.value = '';
            }
        });
    }

    // ===============================
    // MODE KAMERA
    // ===============================
    if (window.Html5Qrcode) {
        const qr = new Html5Qrcode("reader");
        qr.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: 250 },
            decodedText => handleScan(decodedText)
        );
    }

    // ===============================
    // INIT
    // ===============================
    document.addEventListener('DOMContentLoaded', renderScanTable);
})();
