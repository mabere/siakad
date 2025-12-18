<x-main-layout>
    @section('title', 'Rincian Surat')
    <style>
        .gradient-title {
            background: linear-gradient(45deg, #2b6cb0, #667eea);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            font-weight: 600;
        }
    </style>
    <div class="container mt-5">
        <h1 class="mb-4">Detail Pengajuan Surat</h1>

        <a href="{{ route('kaprodi.request.surat-masuk.index') }}" class="btn btn-secondary mb-3">Kembali ke Daftar</a>

        <div class="card">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0 text-primary">{{ $letterRequest->letterType->name }}</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th>Pengaju</th>
                        <td>{{ $letterRequest->user->name }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <span class="badge {{ 
                                ($letterRequest->status === 'submitted' ? 'bg-warning' : 
                                ($letterRequest->status === 'processing' ? 'bg-info' : 
                                ($letterRequest->status === 'approved' ? 'bg-success' : 
                                ($letterRequest->status === 'rejected' ? 'bg-danger' : 'bg-secondary')))) }}">
                                {{ ucfirst($letterRequest->status) }}
                            </span>
                        </td>
                    </tr>
                    @if ($letterRequest->rejection_reason)
                    <tr>
                        <th>Alasan Penolakan</th>
                        <td><span class="text-danger">{{ $letterRequest->rejection_reason }}</span></td>
                    </tr>
                    @endif
                    <tr>
                        <th>Nomor Referensi</th>
                        <td>{{ $letterRequest->reference_number ?? 'Belum Diterbitkan' }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Pengajuan</th>
                        <td>{{ $letterRequest->created_at->format('d M Y H:i') }}</td>
                    </tr>
                    @if ($letterRequest->approved_at)
                    <tr>
                        <th>Tanggal Disetujui</th>
                        <td>{{ $letterRequest->approved_at->format('d M Y H:i') }}</td>
                    </tr>
                    @endif
                    @if ($letterRequest->approvedBy)
                    <tr>
                        <th>Disetujui Oleh</th>
                        <td>{{ $letterRequest->approvedBy->name }}</td>
                    </tr>
                    @endif
                    @if ($letterRequest->rejection_reason)
                    <tr>
                        <th>Alasan Penolakan</th>
                        <td>{{ $letterRequest->rejection_reason }}</td>
                    </tr>
                    @endif
                </table>

                <h5>Data Pengajuan</h5>
                <table class="table table-bordered">
                    @foreach ($letterRequest->form_data as $key => $value)
                    <tr>
                        <th>{{ $key }}</th>
                        <td>{{ $value }}</td>
                    </tr>
                    @endforeach
                </table>

                <div class="card shadow-lg">


                    <!-- Existing card body content remains the same -->

                    @if ($letterRequest->status === 'submitted')
                    <div class="card-footer bg-light">
                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="mb-3">Tindakan:</h5>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="actionType" id="reviewAction"
                                        value="review">
                                    <label class="form-check-label" for="reviewAction">
                                        Review
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="actionType" id="rejectAction"
                                        value="reject">
                                    <label class="form-check-label" for="rejectAction">
                                        Tolak
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Review Form -->
                        <div id="reviewForm" class="mt-3 d-none">
                            <form action="{{ route('kaprodi.request.surat-masuk.review', $letterRequest) }}"
                                method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Catatan Review</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3"
                                        placeholder="Masukkan catatan review..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-check-circle me-2"></i>Submit Review
                                </button>
                            </form>
                        </div>

                        <!-- Reject Form -->
                        <div id="rejectForm" class="mt-3 d-none">
                            <form action="{{ route('kaprodi.request.surat-masuk.reject', $letterRequest) }}"
                                method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="rejection_reason" class="form-label">Alasan Penolakan</label>
                                    <input type="text" class="form-control" id="rejection_reason"
                                        name="rejection_reason" required placeholder="Masukkan alasan penolakan...">
                                    @error('rejection_reason')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-times-circle me-2"></i>Submit Penolakan
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
                @if (!empty($letterRequest->approval_history))
                <h5>Riwayat Approval</h5>
                <ul class="list-group">
                    @foreach ($letterRequest->approval_history as $history)
                    <li class="list-group-item">
                        <strong>{{ ucfirst($history['step']) }}</strong> oleh User ID: {{ $history['user_name'] }}
                        pada {{ \Carbon\Carbon::parse($history['timestamp'])->format('d M Y H:i') }}
                    </li>
                    @endforeach
                </ul>
                @endif



                @if ($letterRequest->status === 'approved' && $letterRequest->document_path)
                <a href="{{ route('letter.download', $letterRequest) }}" class="btn btn-primary mt-3">
                    Download PDF
                </a>
                @endif
            </div>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const reviewRadio = document.getElementById('reviewAction');
            const rejectRadio = document.getElementById('rejectAction');
            const reviewForm = document.getElementById('reviewForm');
            const rejectForm = document.getElementById('rejectForm');

            function toggleForms() {
                if (reviewRadio.checked) {
                    reviewForm.classList.remove('d-none');
                    rejectForm.classList.add('d-none');
                } else if (rejectRadio.checked) {
                    rejectForm.classList.remove('d-none');
                    reviewForm.classList.add('d-none');
                } else {
                    reviewForm.classList.add('d-none');
                    rejectForm.classList.add('d-none');
                }
            }

            reviewRadio.addEventListener('change', toggleForms);
            rejectRadio.addEventListener('change', toggleForms);
        });
    </script>
</x-main-layout>