<x-main-layout>
    @section('title', 'Detail Pengajuan Perbaikan Nilai')

    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <!-- Main Card -->
            <div class="card shadow-lg border-0 rounded-3 mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="card-title mb-0"><i class="icon ni ni-info-fill me-2"></i>Informasi Pengajuan</h5>
                </div>
                <div class="card-body">
                    <table class="table-responsive table">
                        <tr>
                            <th>ID</th>
                            <td>:</td>
                            <td><span class="text-muted me-1">#{{ $request->id }}</span></td>
                        </tr>
                        <tr>
                            <th>Mahasiswa</th>
                            <td>:</td>
                            <td><span class="text-muted me-1">{{ $request->user->name }}</span></td>
                        </tr>
                        <tr>
                            <th>Semester</th>
                            <td>:</td>
                            <td><span class="text-muted me-1">{{ $request->semester }}</span></td>
                        </tr>
                        <tr>
                            <th>Mata Kuliah</th>
                            <td>:</td>
                            <td><span class="text-muted me-1">{{ $request->course->code . '-' . $request->course->name
                                    }}</span></td>
                        </tr>
                        <tr>
                            <th>Dosen Pengampu</th>
                            <td>:</td>
                            <td><span class="text-muted me-1">
                                    @foreach ($lecturers as $lec)
                                    <li>{{ $loop->iteration }}. {{ $lec->nama_dosen }}</li>
                                    @endforeach
                                    @if ($lecturers->isEmpty()) Belum Ditentukan @endif
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Nilai Saat Ini</th>
                            <td>:</td>
                            <td><span class="badge bg-warning text-dark me-1">{{ $request->current_grade }}</span></td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>:</td>
                            <td><span class="badge bg-secondary text-white me-1">{{ ucfirst(str_replace('_', ' ',
                                    $request->status)) }}</span></td>
                        </tr>
                        <tr>
                            <th>Nilai Setelah Perbaikan</th>
                            <td>:</td>
                            <td><span class="badge bg-success text-dark me-1">{{ $request->requested_grade ?? 'Belum
                                    Ditentukan' }}</span></td>
                        </tr>
                    </table>

                    @if ($request->document_path)
                    <hr class="my-4">
                    <div class="alert alert-info d-flex align-items-center mt-3">
                        <div>
                            <strong>Download Dokumen:</strong>
                            <a href="{{ Storage::url($request->document_path) }}" target="_blank"
                                class="btn btn-link text-decoration-none">
                                <i class="icon ni ni-download fw-large text-secondary"></i>
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
                <!-- Link Kembali -->
                <div class="mt-3 p-3">
                    @if (auth()->user()->hasRole('mahasiswa'))
                    <a href="{{ route('mhs.remedial.index') }}" class="btn btn-outline-secondary">
                        <i class="icon ni ni-arrow-left me-2"></i>Kembali
                    </a>
                    @elseif (auth()->user()->hasRole('kaprodi'))
                    <a href="{{ route('kaprodi.remedial.index') }}" class="btn btn-outline-secondary">
                        <i class="icon ni ni-arrow-left me-2"></i>Kembali
                    </a>
                    @elseif (auth()->user()->hasRole('dosen'))
                    <a href="{{ route('dosen.remedial.index') }}" class="btn btn-outline-secondary">
                        <i class="icon ni ni-arrow-left me-2"></i>Kembali
                    </a>
                    @elseif (auth()->user()->hasRole('staff'))
                    <a href="{{ route('staff.remedial.index') }}" class="btn btn-outline-secondary">
                        <i class="icon ni ni-arrow-left me-2"></i>Kembali
                    </a>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <!-- Progress Timeline -->
            <div class="card shadow-lg border-0 rounded-3 mb-4">
                <div class="card-header bg-info text-white py-3">
                    <h5 class="card-title mb-0"><i class="icon ni ni-reports-alt me-2"></i>Progress Pengajuan</h5>
                </div>
                <div class="card-body">
                    <div class="progress-timeline">
                        @php
                        $steps = [
                        'submit' => ['Pengajuan Diajukan', 'Mahasiswa'],
                        'staff_review' => ['Review', 'Staff'],
                        'dosen_process' => ['Proses Nilai', 'Dosen'],
                        'staff_validate' => ['Validasi', 'Staff'],
                        'kaprodi_approve' => ['Persetujuan', 'Kaprodi'],
                        ];
                        @endphp
                        @foreach ($steps as $step => $label)
                        @php
                        $isCompleted = isset($request->approval_flow[$step]) && $request->approval_flow[$step] ===
                        'approved';
                        $historyEntry = collect($request->approval_history ?? [])->firstWhere('step', $step);
                        $timestamp = $historyEntry ? $historyEntry['timestamp'] : null;
                        $user = $historyEntry && $historyEntry['user_id'] ?
                        \App\Models\User::find($historyEntry['user_id']) : null;
                        @endphp
                        <div class="timeline-step {{ $isCompleted ? 'completed' : '' }}">
                            <div class="timeline-icon bg-gradient-{{ $isCompleted ? 'success' : 'secondary' }}">
                                <i class="icon ni ni-{{ $isCompleted ? 'check' : 'ellipsis-h' }}"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="mb-1">{{ $label[0] }}</h6>
                                <p class="text-muted mb-0">{{ $label[1] }}</p>
                                @if ($isCompleted && $timestamp)
                                <small class="text-success">
                                    <i class="icon ni ni-clock me-1"></i>{{ \Carbon\Carbon::parse($timestamp)->format('d
                                    M Y H:i') }}
                                    @if ($user)
                                    <br><span class="text-primary">{{ $user->name }}</span>
                                    @endif
                                </small>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @can('process-remedial')
    @if ($request->status === 'processing')
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-header bg-gradient-info text-white py-3">
            <h5 class="card-title mb-0"><i class="icon ni ni-edit me-2"></i>Proses Perbaikan Nilai</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('dosen.remedial.process', $request) }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="current_grade" class="form-label">Nilai Lama</label>
                        <select class="form-control bg-light" id="current_grade" name="current_grade" required>
                            <option value="">Pilih Nilai</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                        </select>
                        @error('current_grade')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="requested_grade" class="form-label">Nilai Baru</label>
                        <select class="form-control bg-light" id="requested_grade" name="requested_grade" required>
                            <option value="">Pilih Nilai</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                        </select>
                        @error('requested_grade')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-12">
                        <label for="notes" class="form-label">Catatan</label>
                        <textarea class="form-control bg-light" id="notes" name="notes" rows="3" maxlength="500"
                            required placeholder="Masukkan catatan proses"></textarea>
                        @error('notes')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-gradient-info px-4">
                            <i class="icon ni ni-save me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif
    @endcan


    <!-- Review Form (khusus Staff saat status submitted) -->
    @can('review-remedial')
    @if (in_array($request->status, ['submitted']))
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-header bg-gradient-info text-white py-3">
            <h5 class="card-title mb-0"><i class="icon ni ni-eye me-2"></i>Review Pengajuan</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('staff.remedial.review', $request) }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-12">
                        <label for="notes" class="form-label">Catatan Review</label>
                        <textarea class="form-control bg-light" id="notes" name="notes" rows="3" maxlength="500"
                            required placeholder="Masukkan catatan review"></textarea>
                        @error('notes')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-gradient-primary px-4">
                            <i class="icon ni ni-save me-2"></i>Simpan Review
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif
    @endcan

    <!-- Validate Form (khusus Staff saat status processing atau validated) -->
    @can('validate-remedial')
    @if (in_array($request->status, ['processing','validated']))
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-header bg-gradient-success text-white py-3">
            <h5 class="card-title mb-0"><i class="icon ni ni-check-circle me-2"></i>Validasi Pengajuan</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('staff.remedial.validate', $request) }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-12">
                        <label for="notes" class="form-label">Catatan Validasi</label>
                        <textarea class="form-control bg-light" id="notes" name="notes" rows="3" maxlength="500"
                            required placeholder="Masukkan catatan validasi"></textarea>
                        @error('notes')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-gradient-success px-4" {{ $request->status ===
                            'validated' ? '' : 'readonly' }}>
                            <i class="icon ni ni-check me-2"></i>Validasi Pengajuan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif
    @endcan

    <!-- Approve Form (khusus Kaprodi saat status pending_kaprodi) -->
    @can('approve-remedial')
    @if ($request->status === 'pending_kaprodi')
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-header bg-primary text-white py-3">
            <h5 class="card-title mb-0"><i class="icon ni ni-thumbs-up me-2"></i>Persetujuan Pengajuan</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('kaprodi.remedial.approve', $request) }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-12">
                        <label for="notes" class="form-label">Catatan Persetujuan</label>
                        <textarea class="form-control bg-light" id="notes" name="notes" rows="3" maxlength="500"
                            required placeholder="Masukkan catatan persetujuan atau penolakan"></textarea>
                        @error('notes')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="approval_status" class="form-label">Status Persetujuan</label>
                        <select class="form-control bg-light" id="approval_status" name="approval_status" required>
                            <option value="approved">Disetujui</option>
                            <option value="rejected">Ditolak</option>
                        </select>
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-gradient-primary px-4">
                            <i class="icon ni ni-check me-2"></i>Simpan Keputusan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif
    @endcan

    <style>
        .text-primary-gradient {
            background: linear-gradient(45deg, #0d6efd, #0dcaf0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .progress-timeline {
            position: relative;
            padding-left: 3rem;
        }

        .timeline-step {
            position: relative;
            padding: .35rem 0;
            border-left: 2px solid #dee2e6;
            padding-left: 2rem;
            margin-left: -1.5rem;
        }

        .timeline-step:last-child {
            border-left: 0;
        }

        .timeline-icon {
            position: absolute;
            left: -1.3rem;
            top: .5rem;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: white;
        }

        .timeline-content {
            background: #f8f9fa;
            padding: .75rem;
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .bg-gradient-primary {
            background: linear-gradient(45deg, #0d6efd, #0b5ed7);
        }

        .bg-gradient-info {
            background: linear-gradient(45deg, #0dcaf0, #0ba9d7);
        }

        .bg-gradient-success {
            background: linear-gradient(45deg, #198754, #157347);
        }

        .btn-gradient-primary {
            background: linear-gradient(45deg, #0d6efd, #0dcaf0);
            color: white;
            border: none;
        }
    </style>
    <x-custom.sweet-alert />

</x-main-layout>
