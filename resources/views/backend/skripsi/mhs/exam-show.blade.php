<x-main-layout>
    @section('title', 'Detail Pendaftaran dan Pelaksanaan Ujian Skripsi')
    @include('backend.skripsi.progress-bar')
    <div class="content-page wide-md m-auto">
        <div class="nk-block-head nk-block-head-xs p-3 bg-primary">
            <h4 class="nk-block-title text-white text-center">@yield('title')</h4>
        </div>
        <div class="nk-block">
            <div class="card card-bordered">
                <div class="card-inner">
                    <!-- Status Section -->
                    <div class="status-card">
                        @if($thesisExam->status === 'lulus')
                        <div class="alert alert-success d-flex align-items-center mt-3">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <div>
                                <strong>Status Ujian:</strong>
                                <p class="mb-0">Selamat, Anda dinyatakan **Lulus** ujian skripsi!</p>
                            </div>
                        </div>
                        @elseif($thesisExam->status === 'lulus_revisi')
                        <div class="alert alert-info d-flex align-items-center mt-3">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <div>
                                <strong>Status Ujian:</strong>
                                <p class="mb-0">Anda dinyatakan **Lulus dengan Revisi**. Mohon selesaikan revisi sesuai
                                    catatan dari penguji.</p>
                            </div>
                        </div>
                        <div class="text-end mt-2">
                            <a href="{{ route('student.thesis.exam.revision.form', $exam->id) }}"
                                class="btn btn-warning">
                                <i class="bi bi-upload me-1"></i> Upload Revisi Dokumen
                            </a>
                        </div>
                        @elseif($thesisExam->status === 'ditolak')
                        <div class="alert alert-danger d-flex align-items-center mt-3">
                            <i class="bi bi-x-circle-fill me-2"></i>
                            <div>
                                <strong>Status Ujian:</strong>
                                <p class="mb-0">Mohon maaf, Anda dinyatakan **Tidak Lulus** ujian skripsi.</p>
                            </div>
                        </div>
                        @elseif($thesisExam->status === 'revisi')
                        <div class="alert alert-warning d-flex align-items-center mt-3">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <div>
                                <strong>Catatan Revisi:</strong>
                                <p class="mb-0">{{ $exam->revisi_notes ?? 'Tidak ada catatan revisi.' }}</p>
                            </div>
                        </div>
                        <div class="text-end">
                            <a href="{{ route('student.thesis.exam.revision.form', $exam->id) }}"
                                class="btn btn-warning mt-2">
                                <i class="bi bi-upload me-1"></i> Upload Revisi Dokumen
                            </a>
                        </div>
                        @endif
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <!-- Pembimbing Section -->
                            <div class="pembimbing-section mb-4">
                                <h6 class="fw-bold mb-3 card-header bg-primary d-flex text-white align-items-center">
                                    <i class="icon ni ni-users-fill me-2 white-white"></i>
                                    Pembimbing
                                </h6>
                                <ul class="list-group">
                                    @foreach($thesisExam->thesis->supervisions as $supervision)
                                    <li class="list-group-item">
                                        <span>
                                            <strong>{{ str_replace(['pembimbing_1', 'pembimbing_2'], ['Pembimbing 1',
                                                'Pembimbing 2'], $supervision->supervisor_role) }}
                                            </strong>
                                        </span>: {{ $supervision->supervisor->nama_dosen }}
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- Penguji Section -->
                            <div class="penguji-section mb-4">
                                <h6 class="fw-bold mb-3 card-header bg-primary text-white d-flex align-items-center">
                                    <i class="icon ni ni-user-list-fill me-whitetext-white"></i>
                                    Penguji
                                </h6>
                                <ul class="list-group">
                                    @foreach($thesisExam->examiners as $index => $examiner)
                                    <li class="list-group-item">
                                        <strong>Penguji {{ $index + 1 }}</strong>; {{ $examiner->lecturer->nama_dosen
                                        }}
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">

                        <div class="col-md-6">
                            <!-- Documents Section -->
                            <div class="documents-section mb-4">
                                <h6 class="fw-bold card-header bg-primary mb-3 d-flex text-white align-items-center">
                                    <i class="icon ni ni-files me-2 white-black"></i>
                                    Daftar Dokumen Persyaratan
                                </h6>
                                <div class="list-group">
                                    @foreach($thesisExam->documents as $doc)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="bi bi-file-earmark-text me-2"></i>
                                            {{ ucwords(str_replace('_', ' ', $doc->document_type)) }}
                                        </div>
                                        <div>
                                            <span
                                                class="badge bg-{{ $doc->status === 'approved' ? 'success' : ($doc->status === 'rejected' ? 'danger' : 'warning') }} me-2">
                                                {{ $doc->status }}
                                            </span>
                                            <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i> Lihat
                                            </a>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- Jadwal Ujian Section -->
                            <div class="jadwal-section mb-4">
                                <h6 class="fw-bold mb-3 card-header bg-primary d-flex text-white align-items-center">
                                    <i class="icon ni ni-calendar me-2 text-white"></i>
                                    Jadwal Ujian
                                </h6>
                                <div class="list-group">
                                    <ul class="list-group d-flex justify-content-evenly align-items-around">
                                        <li class="list-group-item">
                                            <strong>Tanggal dan Waktu: </strong><span class="float-right me-0">{{
                                                $thesisExam->scheduled_at ?
                                                $thesisExam->scheduled_at->format('d M Y, H:i') : 'Belum dijadwalkan'
                                                }}</span>
                                        </li>
                                        <li class="list-group-item">
                                            <strong>Lokasi: </strong> {{ $thesisExam->location ?? 'Belum ditentukan' }}
                                        </li>
                                        @if($thesisExam->final_score)
                                        <li class="list-group-item">
                                            <strong>Nilai Akhir: </strong> {{ round($thesisExam->final_score, 2) }}
                                        </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <!-- Progress Timeline -->
                    <div class="progress-section">
                        <div class="card-header bg-primary d-flex align-items-center justify-content-between">
                            <h6 class="fw-bold text-white">
                                <i class="icon ni ni-share-alt me-2 text-white"></i>
                                Progress Pendaftaran & Pelaksanaan Ujian
                            </h6>
                            @php
                            $badgeColor = 'warning'; // Default
                            $statusText = strtoupper($thesisExam->status);

                            if ($thesisExam->status === 'lulus') {
                            $badgeColor = 'success';
                            } elseif ($thesisExam->status === 'lulus_revisi') {
                            $badgeColor = 'info';
                            } elseif ($thesisExam->status === 'ditolak') {
                            $badgeColor = 'danger';
                            $statusText = 'DITOLAK';
                            }
                            @endphp
                            <span class="text-white"><strong>{{ strtoupper('Status') }}:</strong> <span
                                    class="badge bg-{{ $badgeColor }} text-white ms-1">{{ $statusText }}</span></span>
                        </div>
                        <ul class="zigzag-timeline mt-3">
                            @php
                            $phases = [
                            'diajukan' => ['title' => 'Pemberkasan Dokumen', 'icon' => 'ni ni-folder'],
                            'terverifikasi' => ['title' => 'Verifikasi Berkas oleh KTU', 'icon' => 'ni ni-list-check'],
                            'penguji_ditetapkan' => ['title' => 'Penetapan Penguji oleh Kaprodi', 'icon' =>
                            'ni ni-users-fill'],
                            'disetujui_dekan' => ['title' => 'Persetujuan Dekan', 'icon' => 'ni ni-user-check-fill'],
                            'dijadwalkan' => ['title' => 'Penjadwalan Ujian', 'icon' => 'ni ni-calendar'],
                            'pelaksanaan' => ['title' => 'Pelaksanaan Ujian', 'icon' => 'ni ni-pen2'],
                            'selesai' => ['title' => 'Penyelesaian Ujian', 'icon' => 'ni ni-check-circle']
                            ];
                            // Ambil status dari ThesisExam
                            $currentPhase = $thesisExam->status ?? 'diajukan';
                            // Atur logika timeline berdasarkan status
                            if (in_array($currentPhase, ['lulus', 'lulus_revisi', 'ditolak'])) {
                            $timelinePhase = 'selesai';
                            } elseif ($currentPhase === 'revisi') {
                            $timelinePhase = 'diajukan';
                            } else {
                            $timelinePhase = $currentPhase;
                            }
                            $phaseKeys = array_keys($phases);
                            $currentIndex = array_search($timelinePhase, $phaseKeys);
                            @endphp

                            @foreach($phases as $key => $phase)
                            @php
                            $isCompleted = $currentIndex > array_search($key, $phaseKeys) || in_array($currentPhase,
                            ['lulus', 'lulus_revisi', 'ditolak']);
                            $isCurrent = $key === $timelinePhase && !in_array($currentPhase, ['lulus', 'lulus_revisi',
                            'ditolak']);
                            $statusClass = $isCompleted ? 'completed' : ($isCurrent ? 'current' : 'pending');
                            $positionClass = $loop->iteration % 2 === 1 ? 'odd' : 'even';
                            // Tambahkan pengecekan untuk status 'ditolak' agar timeline tidak berlanjut
                            if ($currentPhase === 'ditolak' && $key === 'selesai') {
                            continue;
                            }
                            @endphp
                            <li class="timeline-item {{ $statusClass }} {{ $positionClass }}">
                                <div class="timeline-icon">
                                    <i class="icon {{ $phase['icon'] }}"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="me-3">{{ $loop->iteration }}. {{ $phase['title'] }}</strong>
                                        <span
                                            class="badge bg-{{ $isCompleted ? 'success' : ($isCurrent ? 'warning' : 'secondary') }}">
                                            @if($isCompleted)
                                            Selesai
                                            @elseif($isCurrent)
                                            {{ $key === 'terverifikasi' ? 'Sedang Diverifikasi' :
                                            ($key === 'penguji_ditetapkan' ? 'Sedang Berlangsung' :
                                            ($key === 'dijadwalkan' ? 'Sedang Dijadwalkan' :
                                            ($key === 'pelaksanaan' ? 'Sedang Dilaksanakan' :
                                            ($key === 'diajukan' && $currentPhase === 'revisi' ? 'Perlu Revisi' :
                                            'Sedang Diproses')))) }}
                                            @else
                                            Belum
                                            @endif
                                        </span>
                                    </div>
                                    @if($isCurrent)
                                    <div class="progress mt-2">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated"
                                            style="width: 50%"></div>
                                    </div>
                                    @endif
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-main-layout>
