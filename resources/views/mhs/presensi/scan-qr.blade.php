<x-main-layout>
    @section('title', 'Scan QR Code untuk Absen')
    <div class="container py-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="card-title mb-0">Absen dengan QR Code</h4>
            </div>
            <div class="card-body">
                <div id="qr-reader" style="width: 100%; height: 300px; border: 2px solid #000;"></div>
                <div id="result" class="mt-3"></div>
                <button id="scan-btn" class="btn btn-primary mt-3">Mulai Scan</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="{{ asset('js/html5-qrcode.min.js') }}"></script>
    <script>
        console.log('Memuat Html5Qrcode...');
                if (typeof Html5Qrcode === 'undefined') {
                    console.error('Html5Qrcode tidak dimuat!');
                    document.getElementById('result').innerHTML = 'Error: Html5Qrcode library tidak dimuat. Periksa koneksi internet.';
                } else {
                    console.log('Html5Qrcode dimuat dengan sukses.');
                }
    
                let html5QrCode;
    
                document.getElementById('scan-btn').addEventListener('click', () => {
                    if (!html5QrCode) {
                        html5QrCode = new Html5Qrcode("qr-reader");
                        html5QrCode.start(
                            { facingMode: "environment" },
                            { fps: 10, qrbox: 250 },
                            (qrCodeMessage) => {
                                console.log('QR Code Detected:', qrCodeMessage);
                                document.getElementById('result').innerHTML = `Memproses: ${qrCodeMessage}`;
                                verifyAttendance(qrCodeMessage);
                            },
                            (error) => {
                                console.log('Error scanning:', error);
                                document.getElementById('result').innerHTML = 'Error scanning: ' + error;
                            }
                        ).catch(err => {
                            console.error('Gagal memulai QR scanner:', err);
                            document.getElementById('result').innerHTML = 'Gagal memulai: ' + err;
                        });
                        document.getElementById('scan-btn').textContent = 'Hentikan Scan';
                    } else {
                        html5QrCode.stop().then(() => {
                            html5QrCode = null;
                            document.getElementById('result').innerHTML = '';
                            document.getElementById('scan-btn').textContent = 'Mulai Scan';
                        }).catch(err => {
                            console.error('Gagal menghentikan QR scanner:', err);
                        });
                    }
                });
    
                function verifyAttendance(qrData) {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                    if (!csrfToken) {
                        console.error('CSRF Token tidak ditemukan!');
                        document.getElementById('result').innerHTML = 'Error: CSRF Token tidak ditemukan.';
                        return;
                    }
                    console.log('Sending QR Data to Server:', qrData);
                    console.log('CSRF Token:', csrfToken);
                    fetch('/attendance/verify-qr', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ qr_data: qrData }),
                        credentials: 'same-origin' // Pastikan sesi dipertahankan
                    })
                    .then(response => {
                        console.log('Response Status:', response.status);
                        if (!response.ok) {
                            throw new Error('Network response was not ok ' + response.statusText);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Response Data:', data);
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: data.success,
                                showConfirmButton: false,
                                timer: 2000
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: data.error,
                                showConfirmButton: false,
                                timer: 2000
                            });
                        }
                        html5QrCode.stop().then(() => {
                            html5QrCode = null;
                            document.getElementById('scan-btn').textContent = 'Mulai Scan';
                        });
                    })
                    .catch(error => {
                        console.error('Fetch Error:', error);
                        document.getElementById('result').innerHTML = 'Error: ' + error.message;
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi kesalahan saat memverifikasi: ' + error.message,
                            showConfirmButton: false,
                            timer: 2000
                        });
                    });
                }
    
                $(document).ready(function() {
                    $('[data-toggle="tooltip"]').tooltip();
                });
    </script>
    @endpush
</x-main-layout>