<x-main-layout>
    @section('title', 'Detail Pengajuan Surat')
    <div class="nk-content">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">@yield('title')</h3>
                            </div>
                            <div class="nk-block-head-content">
                                <div class="toggle-wrap nk-block-tools-toggle">
                                    <a href="{{ route('admin.letter-requests.index') }}"
                                        class="btn btn-outline-secondary">
                                        <em class="icon ni ni-arrow-left"></em>
                                        <span>Kembali</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="nk-block">
                        <div class="row g-gs">
                            <!-- Informasi Surat -->
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-inner">
                                        <h5 class="card-title">Informasi Surat</h5>
                                        <div class="row g-4">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="form-label">Jenis Surat</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control"
                                                            value="{{ $letterRequest->letterType->name }}" readonly>
                                                    </div>
                                                </div>
                                            </div>

                                            @if($letterRequest->reference_number)
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="form-label">Nomor Surat</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control"
                                                            value="{{ $letterRequest->reference_number }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="form-label">Tanggal Pengajuan</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control"
                                                            value="{{ $letterRequest->created_at->format('d/m/Y H:i') }}"
                                                            readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="form-label">Status</label>
                                                    <div class="form-control-wrap">
                                                        @php
                                                        $statusClass = [
                                                        'draft' => 'bg-gray',
                                                        'submitted' => 'bg-info',
                                                        'processing' => 'bg-warning',
                                                        'approved' => 'bg-success',
                                                        'rejected' => 'bg-danger',
                                                        'completed' => 'bg-success'
                                                        ][$letterRequest->status];

                                                        $statusLabel = [
                                                        'draft' => 'Draft',
                                                        'submitted' => 'Menunggu Persetujuan',
                                                        'processing' => 'Sedang Diproses',
                                                        'approved' => 'Disetujui',
                                                        'rejected' => 'Ditolak',
                                                        'completed' => 'Selesai'
                                                        ][$letterRequest->status];
                                                        @endphp
                                                        <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Data Pemohon -->
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-inner">
                                        <h5 class="card-title">Data Pemohon</h5>
                                        <div class="row g-4">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="form-label">Nama</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control"
                                                            value="{{ $letterRequest->user->name }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="form-label">NIM/NIP</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control"
                                                            value="{{ $letterRequest->user->lecturer->nidn }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="form-label">Program Studi</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control"
                                                            value="{{ $letterRequest->user->lecturer->department->nama }}"
                                                            readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="form-label">Email</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control"
                                                            value="{{ $letterRequest->user->email }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Data Surat -->
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-inner">
                                        <h5 class="card-title">Data Surat</h5>
                                        <div class="row g-4">
                                            @foreach($letterRequest->form_data as $key => $value)
                                            @if(in_array($key, $letterRequest->letterType->required_fields))
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="form-label">{{ ucwords(str_replace('_', '
                                                        ', $key)) }}</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control" value="{{ $value }}"
                                                            readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Approval Actions -->
                        @if($letterRequest->status === 'submitted')
                        <div class="nk-block mt-4">
                            <div class="card">
                                <div class="card-inner">
                                    <div class="row g-gs">
                                        <div class="col-lg-8">
                                            <form action="{{ route('admin.letter-requests.process', $letterRequest) }}"
                                                method="POST" id="approval-form">
                                                @csrf
                                                <div class="form-group">
                                                    <label class="form-label" for="action">Tindakan</label>
                                                    <select class="form-select" id="action" name="action" required>
                                                        <option value="">Pilih Tindakan</option>
                                                        <option value="approved">Setujui</option>
                                                        <option value="rejected">Tolak</option>
                                                    </select>
                                                </div>

                                                <div class="form-group" id="reference-number-group"
                                                    style="display: none;">
                                                    <label class="form-label" for="reference_number">Nomor Surat</label>
                                                    <input type="text" class="form-control" id="reference_number"
                                                        name="reference_number" placeholder="Masukkan nomor surat">
                                                </div>

                                                <div class="form-group" id="rejection-reason-group"
                                                    style="display: none;">
                                                    <label class="form-label" for="rejection_reason">Alasan
                                                        Penolakan</label>
                                                    <textarea class="form-control" id="rejection_reason"
                                                        name="rejection_reason" rows="3"
                                                        placeholder="Masukkan alasan penolakan"></textarea>
                                                </div>

                                                <div class="form-group">
                                                    <button type="submit" class="btn btn-primary" id="submit-btn">
                                                        <span class="spinner-border spinner-border-sm d-none"
                                                            role="status" aria-hidden="true"></span>
                                                        <span class="btn-text">Proses Surat</span>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($letterRequest->status === 'rejected')
                        <div class="nk-block mt-4">
                            <div class="card bg-danger bg-opacity-10">
                                <div class="card-inner">
                                    <h5 class="card-title text-danger">Alasan Penolakan</h5>
                                    <p>{{ $letterRequest->rejection_reason }}</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($letterRequest->document_path)
                        <div class="nk-block mt-4">
                            <div class="card">
                                <div class="card-inner">
                                    <h5 class="card-title">Dokumen Surat</h5>
                                    <a href="{{ route('admin.letter-requests.download', $letterRequest) }}"
                                        class="btn btn-primary">
                                        <em class="icon ni ni-download"></em>
                                        <span>Download Surat</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Handle form field visibility based on action
            $('#action').change(function() {
                const action = $(this).val();
                console.log('Action changed:', action); // Debug log
                
                if (action === 'approved') {
                    $('#reference-number-group').show();
                    $('#rejection-reason-group').hide();
                    $('#rejection_reason').val(''); // Clear rejection reason
                    $('#reference_number').prop('required', true);
                    $('#rejection_reason').prop('required', false);
                } else if (action === 'rejected') {
                    $('#reference-number-group').hide();
                    $('#rejection-reason-group').show();
                    $('#reference_number').val(''); // Clear reference number
                    $('#reference_number').prop('required', false);
                    $('#rejection_reason').prop('required', true);
                } else {
                    $('#reference-number-group').hide();
                    $('#rejection-reason-group').hide();
                    $('#reference_number').val('');
                    $('#rejection_reason').val('');
                    $('#reference_number').prop('required', false);
                    $('#rejection_reason').prop('required', false);
                }
            });

            // Handle form submission
            $('#approval-form').on('submit', function(e) {
                e.preventDefault();
                
                const form = $(this);
                const submitBtn = form.find('#submit-btn');
                const spinner = submitBtn.find('.spinner-border');
                const btnText = submitBtn.find('.btn-text');
                
                // Debug log form data
                const formData = form.serializeArray();
                console.log('Form data:', formData);
                
                // Validate form based on action
                const action = $('#action').val();
                if (action === 'rejected' && !$('#rejection_reason').val().trim()) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Alasan penolakan harus diisi ketika memilih tindakan Tolak'
                    });
                    return false;
                }
                
                if (action === 'approved' && !$('#reference_number').val().trim()) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Nomor surat harus diisi ketika memilih tindakan Setujui'
                    });
                    return false;
                }
                
                // Show loading state
                submitBtn.prop('disabled', true);
                spinner.removeClass('d-none');
                btnText.text('Memproses...');

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        console.log('Success response:', response); // Debug log
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                showConfirmButton: true
                            }).then((result) => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        console.log('Error response:', xhr.responseJSON); // Debug log
                        const response = xhr.responseJSON;
                        let errorMessage = response.message || 'Terjadi kesalahan saat memproses surat';
                        
                        // Check for validation errors
                        if (response.errors) {
                            errorMessage = Object.values(response.errors).flat().join('\n');
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: errorMessage
                        });
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false);
                        spinner.addClass('d-none');
                        btnText.text('Proses Surat');
                    }
                });
            });
        });
    </script>
    @endpush
</x-main-layout>