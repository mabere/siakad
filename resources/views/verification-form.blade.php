<x-main-layout>
    @section('title', 'Verifikasi Dokumen')
    <div class="container mt-5">
        <h2 class="text-center mb-4">Verifikasi Dokumen</h2>

        @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Detail Dokumen Terverifikasi</h5>
                <ul class="list-group">
                    <li class="list-group-item">Dekan: {{ session('verifiedData')['dekan'] }}</li>
                    <li class="list-group-item">NIP: {{ session('verifiedData')['nip'] }}</li>
                    <li class="list-group-item">Tanggal: {{ session('verifiedData')['date'] }}</li>
                    <li class="list-group-item">No Referensi: {{ session('verifiedData')['reference_number'] }}</li>
                    <li class="list-group-item">Nama Mahasiswa: {{ session('verifiedData')['student_name'] }}</li>
                    <li class="list-group-item">NIM: {{ session('verifiedData')['nim'] }}</li>
                    <li class="list-group-item">Program Studi: {{ session('verifiedData')['program_study'] }}</li>
                    <li class="list-group-item">Peruntukan: {{ session('verifiedData')['purpose'] }}</li>
                    <li class="list-group-item">ID: {{ session('verifiedData')['id'] }}</li>
                </ul>
            </div>
        </div>
        @endif

        @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
        @endif

        <form id="qrVerificationForm" action="{{ route('verify.qr') }}" method="POST" class="mt-4">
            @csrf
            <div class="mb-3">
                <label for="qr_data" class="form-label">Masukkan Data QR Code</label>
                <textarea class="form-control" id="qr_data" name="qr_data" rows="10"
                    readonly>{{ old('qr_data') }}</textarea>
                @error('qr_data')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <!-- Tombol dihapus -->
        </form>

        <div class="mb-3">
            <label for="qrFile" class="form-label">Upload Gambar QR Code</label>
            <input type="file" id="qrFile" accept="image/*" class="form-control">
        </div>

        <script src="{{ asset('js/qr_packed.js') }}"></script>
        <!-- Modal untuk hasil verifikasi -->
        <div class="modal fade" id="verificationModal" tabindex="-1" aria-labelledby="verificationModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="verificationModalLabel">Hasil Verifikasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="modalBody">
                        <!-- Konten hasil verifikasi akan dimasukkan di sini -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            console.log('jQuery loaded');
    
            // Fungsi untuk proses verifikasi
            function verifyQrCode(qrData) {
                $('#modalBody').html('<div class="alert alert-info">Memverifikasi...</div>');
                $('#verificationModalLabel').text('Memproses');
                $('#verificationModal').modal('show');
    
                setTimeout(() => {
                    $.ajax({
                        url: $('#qrVerificationForm').attr('action'),
                        type: 'POST',
                        data: {
                            _token: $('input[name="_token"]').val(),
                            qr_data: qrData
                        },
                        success: function(response) {
                            let modalBody = $('#modalBody');
                            let modalTitle = $('#verificationModalLabel');
                            if (response.status === 'success') {
                                modalTitle.text('Dokumen Valid');
                                modalBody.html(`
                                    <div class="alert alert-success">Dokumen Valid</div>
                                    <h5>Detail Dokumen:</h5>
                                    <ul class="list-group">
                                        <li class="list-group-item">Dekan: ${response.data.dekan}</li>
                                        <li class="list-group-item">NIP: ${response.data.nip}</li>
                                        <li class="list-group-item">Tanggal: ${response.data.date}</li>
                                        <li class="list-group-item">No Referensi: ${response.data.reference_number}</li>
                                        <li class="list-group-item">Nama Mahasiswa: ${response.data.student_name}</li>
                                        <li class="list-group-item">NIM: ${response.data.nim}</li>
                                        <li class="list-group-item">Program Studi: ${response.data.program_study}</li>
                                        <li class="list-group-item">Peruntukan: ${response.data.purpose}</li>
                                        <li class="list-group-item">ID: ${response.data.id}</li>
                                    </ul>
                                `);
                            } else {
                                modalTitle.text('Dokumen Tidak Valid');
                                modalBody.html(`<div class="alert alert-danger">${response.message}</div>`);
                            }
                        },
                        error: function(xhr) {
                            console.error('AJAX Error:', xhr.responseText);
                            let errorMessage = 'Terjadi kesalahan tidak diketahui';
                            try {
                                const errorData = JSON.parse(xhr.responseText);
                                errorMessage = errorData.message || errorMessage;
                            } catch (e) {
                                errorMessage = xhr.responseText || errorMessage;
                            }
                            $('#modalBody').html(`<div class="alert alert-danger">Terjadi kesalahan: ${errorMessage}</div>`);
                            $('#verificationModalLabel').text('Error');
                        }
                    });
                }, 500); // Delay untuk preload
            }
    
            // Handle upload gambar QR
            $('#qrFile').on('change', function(e) {
                const file = e.target.files[0];
                const reader = new FileReader();
                reader.onload = function() {
                    qrcode.callback = function(result) {
                        if (result) {
                            $('#qr_data').val(result);
                            verifyQrCode(result); // Langsung verifikasi
                        } else {
                            alert('Gagal membaca QR code');
                        }
                    };
                    qrcode.decode(reader.result);
                };
                reader.readAsDataURL(file);
            });
    
            // Optional: Handle manual submit jika textarea diisi manual
            $('#qrVerificationForm').on('submit', function(e) {
                e.preventDefault();
                const qrData = $('#qr_data').val();
                if (qrData) {
                    verifyQrCode(qrData);
                }
            });
        });
    </script>
</x-main-layout>