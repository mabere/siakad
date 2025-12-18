<x-main-layout>
    @section('title', 'Scan QR Code untuk Presensi')

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="container py-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="card-title mb-0">Presensi dengan QR Code</h4>
            </div>
            <div class="card-body">
                <div id="qr-reader" style="width: 100%; height: 300px; border: 2px solid #000;"></div>
                <div id="result" class="mt-3"></div>
                <button id="scan-btn" class="btn btn-primary mt-3">Mulai Scan</button>
                <div id="loading" class="mt-3" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Memproses...</span>
                    </div>
                    <span>Memproses...</span>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="{{ asset('js/html5-qrcode.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        console.log('Memuat Html5Qrcode...');
        if (typeof Html5Qrcode === 'undefined') {
            console.error('Html5Qrcode tidak dimuat!');
            document.getElementById('result').innerHTML = 'Error: Html5Qrcode library tidak dimuat.';
        } else {
            console.log('Html5Qrcode dimuat dengan sukses.');
        }

        let html5QrCode = null;
        let isScanning = false;
        let isProcessing = false;
        let processedQRs = new Set();

        // Debounce untuk tombol scan
        let scanTimeout = null;
        document.getElementById('scan-btn').addEventListener('click', (event) => {
            event.preventDefault();
            if (scanTimeout) return;
            scanTimeout = setTimeout(() => {
                scanTimeout = null;
            }, 1000);

            if (!isScanning) {
                // Mulai scan
                isScanning = true;
                isProcessing = false;
                const scanBtn = document.getElementById('scan-btn');
                scanBtn.disabled = true;
                html5QrCode = new Html5Qrcode("qr-reader");
                html5QrCode.start(
                    { facingMode: "environment" },
                    { fps: 5, qrbox: { width: 250, height: 250 } },
                    (qrCodeMessage) => {
                        if (isProcessing) {
                            console.log('Ignoring QR scan: already processing');
                            return;
                        }
                        if (processedQRs.has(qrCodeMessage)) {
                            console.log('Ignoring QR scan: already processed in session');
                            document.getElementById('result').innerHTML = 'QR code ini sudah diproses.';
                            stopScanner();
                            return;
                        }
                        console.log('QR Code Detected:', qrCodeMessage);
                        document.getElementById('result').innerHTML = 'Memindai QR code...';
                        isProcessing = true;
                        // Hentikan scanner sebelum fetch
                        html5QrCode.stop().then(() => {
                            console.log('Scanner stopped after detection');
                            isScanning = false;
                            verifyAttendance(qrCodeMessage);
                        }).catch(err => {
                            console.error('Gagal menghentikan QR scanner:', err);
                            document.getElementById('result').innerHTML = 'Error menghentikan scanner: ' + err;
                            isProcessing = false;
                            isScanning = false;
                            scanBtn.disabled = false;
                            scanBtn.textContent = 'Mulai Scan';
                        });
                    },
                    (error) => {
                        console.log('Error scanning:', error);
                        document.getElementById('result').innerHTML = 'Error saat memindai: ' + error;
                    }
                ).catch(err => {
                    console.error('Gagal memulai QR scanner:', err);
                    document.getElementById('result').innerHTML = 'Gagal memulai scanner: ' + err;
                    isScanning = false;
                    scanBtn.disabled = false;
                    scanBtn.textContent = 'Mulai Scan';
                });
                scanBtn.textContent = 'Hentikan Scan';
            } else {
                // Hentikan scan
                stopScanner();
            }
        });

        function stopScanner() {
            const scanBtn = document.getElementById('scan-btn');
            if (html5QrCode && isScanning) {
                html5QrCode.stop().then(() => {
                    console.log('Scanner stopped');
                    html5QrCode = null;
                    isScanning = false;
                    isProcessing = false;
                    document.getElementById('result').innerHTML = '';
                    scanBtn.textContent = 'Mulai Scan';
                    scanBtn.disabled = false;
                }).catch(err => {
                    console.error('Gagal menghentikan QR scanner:', err);
                    document.getElementById('result').innerHTML = 'Error menghentikan scanner: ' + err;
                    scanBtn.disabled = false;
                });
            } else {
                console.log('No active scanner to stop');
                isScanning = false;
                isProcessing = false;
                document.getElementById('result').innerHTML = '';
                scanBtn.textContent = 'Mulai Scan';
                scanBtn.disabled = false;
            }
        }

        function verifyAttendance(qrData) {
            if (!qrData) {
                Swal.fire({
                    icon: 'error',
                    title: 'Data QR kosong!',
                    showConfirmButton: false,
                    timer: 2000
                });
                isProcessing = false;
                document.getElementById('scan-btn').disabled = false;
                document.getElementById('scan-btn').textContent = 'Mulai Scan';
                return;
            }

            document.getElementById('loading').style.display = 'block';
            document.getElementById('result').innerHTML = '';

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            if (!csrfToken) {
                console.error('CSRF Token tidak ditemukan!');
                document.getElementById('result').innerHTML = 'Error: CSRF Token tidak ditemukan.';
                document.getElementById('loading').style.display = 'none';
                isProcessing = false;
                document.getElementById('scan-btn').disabled = false;
                document.getElementById('scan-btn').textContent = 'Mulai Scan';
                return;
            }

            console.log('Sending QR Data to Server:', qrData);
            fetch('{{ route("student.attendance.verify.qr") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ qr_data: qrData }),
                credentials: 'same-origin'
            })
            .then(async response => {
                console.log('Response Status:', response.status, 'Status Text:', response.statusText);
                let data;
                try {
                    data = await response.json();
                } catch (e) {
                    console.error('Failed to parse JSON:', e);
                    throw new Error('Invalid response format from server');
                }
                return { status: response.status, data };
            })
            .then(({ status, data }) => {
                console.log('Response Data:', data);
                document.getElementById('loading').style.display = 'none';
                isProcessing = false;
                if (status >= 200 && status < 300) {
                    processedQRs.add(qrData);
                    Swal.fire({
                        icon: 'success',
                        title: data.success || 'Kehadiran berhasil dicatat',
                        showConfirmButton: false,
                        timer: 2000
                    });
                    document.getElementById('result').innerHTML = 'Presensi berhasil!';
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: data.error || 'Terjadi kesalahan',
                        showConfirmButton: false,
                        timer: 2000
                    });
                    document.getElementById('result').innerHTML = '';
                }
                document.getElementById('scan-btn').disabled = false;
                document.getElementById('scan-btn').textContent = 'Mulai Scan';
            })
            .catch(error => {
                console.error('Fetch Error:', error);
                document.getElementById('loading').style.display = 'none';
                document.getElementById('result').innerHTML = 'Error: ' + error.message;
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi kesalahan: ' + error.message,
                    showConfirmButton: false,
                    timer: 2000
                });
                isProcessing = false;
                document.getElementById('scan-btn').disabled = false;
                document.getElementById('scan-btn').textContent = 'Mulai Scan';
            });
        }

        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
    @endpush
</x-main-layout>