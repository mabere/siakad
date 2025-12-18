<x-main-layout>
    @section('title', 'Detail Pengajuan Surat')

    <div class="nk-content">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <!-- Header Section -->
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h4 class="nk-block-title text-capitalize">@yield('title'): {{
                                    $letterRequest->letterType->name }}</h4>

                            </div>
                            <div class="nk-block-head-content">
                                <a href="{{ route('student.request.surat.index') }}" class="btn btn-outline-primary">
                                    <em class="icon ni ni-arrow-left"></em>
                                    <span>Kembali</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="nk-block">
                        <div class="row g-gs">
                            <div class="card text-white card-title card-header bg-primary">
                                <h4>{{ $letterRequest->letterType->name }}</h4>
                            </div>
                            <!-- Left Column - Information Cards -->
                            <div class="col-lg-6">
                                <!-- Letter Information Card -->
                                <div class="card">
                                    <div class="card-inner">
                                        <h5 class="card-title title-border">Informasi Surat</h5>
                                        <div class="row g-3">
                                            <x-custom.form-group-readonly label="Jenis Surat"
                                                :value="$letterRequest->letterType->name" icon="ni ni-file-docs" />

                                            @if($letterRequest->reference_number)
                                            <x-custom.form-group-readonly label="Nomor Surat"
                                                :value="$letterRequest->reference_number" icon="ni ni-hash" />
                                            @endif

                                            <x-custom.form-group-readonly label="Tanggal Pengajuan"
                                                :value="$letterRequest->created_at->translatedFormat('d F Y H:i')"
                                                icon="ni ni-calendar" />

                                            @if ($letterRequest->approved_at)
                                            <x-custom.form-group-readonly label="Tanggal Disetujui"
                                                :value="$letterRequest->approved_at->translatedFormat('d F Y H:i')"
                                                icon="ni ni-calendar" />
                                            @endif

                                            @if ($letterRequest->approvedBy)
                                            <x-custom.form-group-readonly label="Disetujui Oleh"
                                                :value="$letterRequest->approvedBy->name" icon="ni ni-calendar" />
                                            @endif

                                            @if ($letterRequest->rejection_reason)
                                            <tr>
                                                <th>Alasan Penolakan</th>
                                                <td>{{ $letterRequest->rejection_reason }}</td>
                                            </tr>
                                            @endif

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="form-label">
                                                        Status
                                                    </label>
                                                    <div class="form-control-wrap">
                                                        <x-custom.status-badge :status="$letterRequest->status" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Form Data Card -->
                                <div class="card mt-3">
                                    <div class="card-inner">
                                        <h5 class="card-title title-border">Data Pengajuan</h5>
                                        <div class="row g-3">
                                            @foreach($letterRequest->form_data as $key => $value)
                                            <x-custom.form-group-readonly :label="ucwords(str_replace('_', ' ', $key))"
                                                :value="$value" />
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- Right Column - User Info & Actions -->
                            <div class="col-lg-6">
                                <!-- Applicant Data Card -->
                                <div class="card">
                                    <div class="card-inner">
                                        <h5 class="card-title title-border">Data Pemohon</h5>
                                        <div class="row g-3">
                                            <x-custom.form-group-readonly label="Nama"
                                                :value="$letterRequest->user->name" icon="ni ni-user" />

                                            <x-custom.form-group-readonly label="NIM/NIP"
                                                :value="$letterRequest->user->student->nim ?? $letterRequest->user->lecturer->nidn ?? 'N/A'"
                                                icon="ni ni-id-card" />

                                            <x-custom.form-group-readonly label="Program Studi"
                                                :value="$letterRequest->user->student->department->nama ?? $letterRequest->user->lecturer->department->nama ?? 'N/A'"
                                                icon="ni ni-building" />

                                            <x-custom.form-group-readonly label="Email"
                                                :value="$letterRequest->user->email" icon="ni ni-mail" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Approval Actions -->
                                @if($letterRequest->status === 'processing')
                                <div class="card mt-3">
                                    <div class="card-inner">
                                        <h5 class="card-title title-border">Tindakan Persetujuan</h5>
                                        <div class="form-group">
                                            <div class="btn-group w-100" role="group">
                                                <input type="radio" class="btn-check" name="action" id="approve"
                                                    autocomplete="off" onchange="toggleActionForm()">
                                                <label class="btn btn-outline-success" for="approve">
                                                    <em class="icon ni ni-check"></em> Setujui
                                                </label>

                                                <input type="radio" class="btn-check" name="action" id="reject"
                                                    autocomplete="off" onchange="toggleActionForm()">
                                                <label class="btn btn-outline-danger" for="reject">
                                                    <em class="icon ni ni-cross"></em> Tolak
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Approval Form -->
                                        <form action="{{ route('dekan.request.surat-masuk.approve', $letterRequest) }}"
                                            method="POST" class="action-form" id="approve_form" style="display: none;">
                                            @csrf
                                            <div class="form-group">
                                                <label class="form-label">Catatan</label>
                                                <textarea name="notes" class="form-control"
                                                    placeholder="Tambahkan catatan (opsional)" rows="2"></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-success w-100 mt-2">
                                                Konfirmasi Persetujuan
                                            </button>
                                        </form>

                                        <!-- Rejection Form -->
                                        <form action="{{ route('dekan.request.surat-masuk.reject', $letterRequest) }}"
                                            method="POST" class="action-form" id="reject_form" style="display: none;">
                                            @csrf
                                            <div class="form-group">
                                                <label class="form-label">Alasan Penolakan</label>
                                                <textarea name="rejection_reason" class="form-control"
                                                    placeholder="Berikan alasan penolakan" rows="2" required></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-danger w-100 mt-2">
                                                Konfirmasi Penolakan
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @endif

                                <!-- Approval History -->
                                @if($letterRequest->approval_history)
                                <div class="card mt-3">
                                    <div class="card-inner">
                                        <h5 class="card-title title-border">Riwayat Persetujuan</h5>
                                        <div class="timeline ps-3">
                                            @foreach($letterRequest->approval_history as $history)
                                            <x-custom.timeline-item :step="$history['step']"
                                                :timestamp="$history['timestamp']"
                                                :user-name="$history['user_name'] ?? 'User ID ' . $history['user_id']" />
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- Document Download -->
                                @if ($letterRequest->status === 'approved' && $letterRequest->document_path)
                                <div class="card mt-3">
                                    <div class="card-inner">
                                        <h5 class="card-title title-border">Dokumen Surat</h5>
                                        <a href="{{ route('letter.download', $letterRequest) }}"
                                            class="btn btn-primary w-100">
                                            <em class="icon ni ni-download"></em>
                                            Unduh Surat
                                        </a>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function toggleActionForm() {
            const forms = document.querySelectorAll('.action-form');
            forms.forEach(form => form.style.display = 'none');
            
            if(document.getElementById('approve').checked) {
                document.getElementById('approve_form').style.display = 'block';
            }
            if(document.getElementById('reject').checked) {
                document.getElementById('reject_form').style.display = 'block';
            }
        }
    </script>
    @endpush
</x-main-layout>