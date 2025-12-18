<div class="card card-bordered mb-3 mt-3">
    <div class="card-header">
        <h5 class="card-title">
            Data KRS {{ $studyPlans->first()->student->nama_mhs }}
            <small class="d-block text-muted">
                NIM: {{ $studyPlans->first()->student->nim }} |
                Kelas: {{ $studyPlans->first()->student->kelas->name }}
            </small>
        </h5>
        @if($showBulkActions)
        <div class="card-tools">
            <button type="button" class="btn btn-sm btn-success me-2" data-bs-toggle="modal"
                data-bs-target="#bulkApproveModal{{ $studentId }}">Setujui Semua</button>
            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                data-bs-target="#bulkRejectModal{{ $studentId }}">Tolak Semua</button>
        </div>
        @endif
    </div>
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Mata Kuliah</th>
                    <th>SKS</th>
                    <th>Dosen</th>
                    <th>Hari</th>
                    <th>Waktu</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($studyPlans as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->course?->code ?? '-' }}</td>
                    <td>{{ $item->course?->name ?? '-' }}</td>
                    <td>{{ $item->course?->sks ?? '-' }}</td>
                    <td>{{ $item->first_lecturer_name }}</td>
                    <td>{{ $item->hari ?? '-' }}</td>
                    <td>{{ $item->waktu }}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <button type="button" class="me-1 btn btn-sm btn-success" data-bs-toggle="modal"
                                data-bs-target="#approveModal{{ $item->id }}">
                                <em class="icon ni ni-check"></em>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                data-bs-target="#rejectModal{{ $item->id }}">
                                <em class="icon ni ni-cross"></em>
                            </button>
                        </div>
                        @include('dosen.approval-krs.partials.approve_reject_modals', ['item' => $item])
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>