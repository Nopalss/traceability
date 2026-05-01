<!DOCTYPE html>
<html>

<head>
    <title>Incoming Part Scan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- LIBRARY LOKAL -->
    <script src="html5-qrcode.min.js"></script>

    <style>
        body {
            font-family: Arial;
        }

        input,
        button {
            font-size: 18px;
            padding: 6px;
            width: 100%;
        }

        .box {
            border: 1px solid #ccc;
            padding: 10px;
            margin-top: 10px;
        }

        pre {
            background: #f5f5f5;
            padding: 10px;
        }
    </style>
</head>

<body>

    <h3>Scan QR Barang Masuk</h3>

    <!-- MODE HP (KAMERA) -->
    <div id="reader" style="width:300px;"></div>

    <hr>

    <!-- MODE SCANNER GUN -->
    <input type="text" id="qr_raw" placeholder="Scan QR di sini" autofocus>

    <div class="box">
        <b>RAW DATA</b>
        <pre id="raw_preview">Belum ada scan</pre>
    </div>

    <button onclick="parseQR()">Parse & Preview</button>

    <div class="box">
        <b>HASIL PARSING</b>
        <pre id="parsed_preview">-</pre>
    </div>

    <button onclick="simpan()">Simpan Barang</button>

    <script>
        let rawData = "";

        // ===== MODE HP =====
        if (window.Html5Qrcode) {
            const qr = new Html5Qrcode("reader");
            qr.start({
                    facingMode: "environment"
                }, {
                    fps: 10,
                    qrbox: 250
                },
                (text) => tampilkan(text)
            );
        }

        // ===== MODE SCANNER =====
        document.getElementById('qr_raw').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                tampilkan(this.value);
                this.value = '';
            }
        });

        function tampilkan(text) {
            rawData = text;
            document.getElementById('raw_preview').innerText = text;
        }

        function parseQR() {
            if (!rawData) {
                alert('Belum ada data');
                return;
            }

            fetch('parse.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'raw=' + encodeURIComponent(rawData)
                })
                .then(r => r.json())
                .then(d => {
                    document.getElementById('parsed_preview').innerText =
                        JSON.stringify(d, null, 2);
                });
        }

        function simpan() {
            if (!rawData) {
                alert('Belum ada data');
                return;
            }

            fetch('save.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'raw=' + encodeURIComponent(rawData)
                })
                .then(r => r.text())
                .then(msg => alert(msg));
        }
    </script>

</body>

</html>