<!-- Content -->
<x-main-layout>
    @section('title', 'Pengaturan EDOM')
    <!-- Alert Messages -->
    <x-custom.sweet-alert />

    <!-- Content Body -->
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">@yield('title')</h3>
                </div>
            </div>
        </div>

        <div class="nk-block">
            <!-- Status EDOM -->
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="card-title">Status EDOM</h5>
                            <p class="text-soft">Aktifkan atau non-aktifkan sistem EDOM untuk semester ini</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <form action="{{ route('admin.edom.settings.toggle') }}" method="POST">
                                @csrf
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="edomActive" name="is_active"
                                        value="1" {{ $settings['edom_active'] ? 'checked' : '' }}
                                        onchange="this.form.submit()">
                                    <label class="custom-control-label" for="edomActive">
                                        {{ $settings['edom_active'] ? 'Aktif' : 'Nonaktif' }}
                                    </label>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Pengaturan -->
            <div class="card card-bordered card-preview mt-4">
                <div class="card-inner">
                    <form action="{{ route('admin.edom.settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card">
                            <div class="card-inner">
                                <div class="row g-4">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-label">Minimal Responden</label>
                                            <input type="number" class="form-control" name="min_respondents"
                                                value="{{ old('min_respondents', $settings['min_respondents'] ?? '') }}"
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-label">Batas Waktu Pengisian (Tanggal)</label>
                                            <input type="date" class="form-control" name="submission_deadline"
                                                value="{{ old('submission_deadline', $settings['submission_deadline'] ?? '') }}"
                                                required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer border-top">
                                <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Informasi Tambahan -->
            <div class="card card-bordered card-preview mt-4">
                <div class="card-inner">
                    <h5 class="card-title">Informasi Semester Aktif</h5>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <p class="text-soft mb-2">Tahun Akademik</p>
                            <h6>{{ $academicYear->ta }}</h6>
                        </div>
                        <div class="col-md-6">
                            <p class="text-soft mb-2">Semester</p>
                            <h6>{{ $academicYear->semester }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-main-layout>

@push('scripts')
<script>
    // Auto hide alerts after 3 seconds
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                $(alert).fadeOut('slow', function() {
                    $(this).remove();
                });
            });
        }, 3000);
    });

    document.querySelector('form').addEventListener('submit', function(e) {
        e.preventDefault();

        // Loading state
        Swal.fire({
            title: 'Menyimpan Pengaturan',
            text: 'Mohon tunggu...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Gunakan FormData untuk mengirim form
        const formData = new FormData(this);
        formData.append('_method', 'PUT'); // untuk method PUT

        fetch(this.action, {
            method: 'POST', // tetap menggunakan POST untuk FormData
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => {
            if (response.redirected) {
                window.location.href = response.url;
                return;
            }
            return response.json();
        })
        .then(data => {
            if (!data) return; // jika sudah di-redirect, data akan null

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    // Redirect ke halaman index setelah sukses
                    window.location.href = '{{ route("admin.edom.settings.index") }}';
                });
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: error.message || 'Terjadi kesalahan saat menyimpan pengaturan'
            });
        });
    });

    // Tambahkan CSS untuk animasi
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from { transform: translateX(100%); }
            to { transform: translateX(0); }
        }
        @keyframes slideOut {
            from { transform: translateX(0); }
            to { transform: translateX(100%); }
        }
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    `;
    document.head.appendChild(style);

    function toggleEdom() {
        fetch('{{ route("admin.edom.settings.toggle") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update status toggle di UI
                const toggleBtn = document.querySelector('.custom-switch-input');
                toggleBtn.checked = data.is_active;

                // Update teks status
                const statusText = document.querySelector('.edom-status-text');
                if (statusText) {
                    statusText.textContent = data.is_active ? 'Aktif' : 'Nonaktif';
                    statusText.className = `edom-status-text badge badge-${data.is_active ? 'success' : 'danger'}`;
                }

                // Tampilkan notifikasi
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 1500
                });
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: error.message || 'Terjadi kesalahan saat mengubah status EDOM'
            });
        });
    }
</script>
@endpush
