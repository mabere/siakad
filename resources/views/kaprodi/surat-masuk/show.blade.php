<x-main-layout>
    @section('title', 'Detail Ajuan Surat')

    <div class="container py-4">
        <div class="card shadow-sm border-0 rounded-lg">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Detail Ajuan Surat</h4>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th width="30%">Nama Pemohon</th>
                            <td>{{ $letterRequest->user->name }}</td>
                        </tr>
                        <tr>
                            <th>Judul Surat</th>
                            <td>{{ $letterRequest->letterType->name }}</td>
                        </tr>
                        <tr>
                            <th>Tujuan Surat</th>
                            <td>{{ $letterRequest->letterType->department->nama }}</td>
                        </tr>
                        <tr>
                            <th>Otoritas</th>
                            <td>{{ $letterRequest->letterType->department->kaprodi }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <span class="badge 
                                    {{ $letterRequest->status == 'draft' ? 'bg-warning' : '' }}
                                    {{ $letterRequest->status == 'submitted' ? 'bg-info' : '' }}
                                    {{ $letterRequest->status == 'approved' ? 'bg-success' : '' }}
                                    {{ $letterRequest->status == 'rejected' ? 'bg-danger' : '' }}
                                ">
                                    {{ ucfirst($letterRequest->status) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Catatan</th>
                            <td>{{ $letterRequest->status == 'rejected' ? $letterRequest->rejection_reason ?? '-' :
                                $letterRequest->notes ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>

                @if($letterRequest->status == 'submitted')
                <div class="mt-4">
                    <h5 class="text-primary">Tindakan</h5>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="action" value="approve" id="approveRadio">
                        <label class="form-check-label" for="approveRadio">Setujui</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="action" value="reject" id="rejectRadio">
                        <label class="form-check-label" for="rejectRadio">Tolak</label>
                    </div>
                </div>

                <div id="approveFormContainer" class="mt-3 d-none">
                    <form action="{{ route('kaprodi.letter-requests.update', $letterRequest->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan Persetujuan</label>
                            <textarea class="form-control" name="notes" id="notes" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">Setujui</button>
                        <a href="{{ route('kaprodi.letter-requests.index') }}" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>

                <div id="rejectFormContainer" class="mt-3 d-none">
                    <form action="{{ route('kaprodi.letter-requests.reject', $letterRequest->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="rejection_reason" class="form-label">Catatan Penolakan</label>
                            <textarea class="form-control" name="rejection_reason" id="rejection_reason" rows="3"
                                required></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger">Tolak</button>
                        <a href="{{ route('kaprodi.letter-requests.index') }}" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>

                <script>
                    document.querySelectorAll('input[name="action"]').forEach(radio => {
                        radio.addEventListener('change', function() {
                            document.getElementById('approveFormContainer').classList.toggle('d-none', this.value !== 'approve');
                            document.getElementById('rejectFormContainer').classList.toggle('d-none', this.value !== 'reject');
                        });
                    });
                </script>
                @endif

                @if($letterRequest->document_path)
                <div class="mt-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h5 class="card-title">Dokumen Surat</h5>
                            <a href="{{ route('kaprodi.letter-requests.download', $letterRequest) }}"
                                class="btn btn-primary">
                                <i class="bi bi-download"></i> Download Surat
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <div class="card-footer">
                <a class="btn btn-warning btn-md" href="{{ route('kaprodi.letter-requests.index') }}">Kembali</a>
            </div>
        </div>
    </div>
</x-main-layout>