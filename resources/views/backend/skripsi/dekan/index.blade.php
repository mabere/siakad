<x-main-layout>
    @section('title', 'Persetujuan Penguji oleh Dekan')

    <div class="card">
        <div class="card-body">
            <h4 class="mb-4">Daftar Pengajuan Ujian Skripsi</h4>
            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Mahasiswa</th>
                        <th>Judul</th>
                        <th>Status</th>
                        <th>Tanggal Ajuan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Mengubah variabel dari $theses menjadi $exams --}}
                    @forelse ($exams as $exam)
                    @php
                    $status = strtolower(trim($exam->status ?? 'unknown'));

                    $statusLabels = [
                    'diajukan' => 'Menunggu Verifikasi KTU',
                    'revisi' => 'Revisi Diminta',
                    'terverifikasi' => 'Terverifikasi KTU',
                    'penguji_ditetapkan' => 'Menunggu Persetujuan Dekan',
                    'disetujui_dekan' => 'Disetujui Dekan',
                    'pelaksanaan' => 'Pelaksanaan Ujian',
                    'dijadwalkan' => 'Dijadwalkan',
                    'selesai' => 'Selesai',
                    ];

                    $statusClasses = [
                    'diajukan' => 'bg-warning text-dark',
                    'revisi' => 'bg-danger',
                    'terverifikasi' => 'bg-info text-dark',
                    'penguji_ditetapkan' => 'bg-primary',
                    'disetujui_dekan' => 'bg-success',
                    'pelaksanaan' => 'bg-info',
                    'dijadwalkan' => 'bg-secondary',
                    'selesai' => 'bg-dark',
                    ];
                    @endphp
                    <tr>
                        <td>{{ $exams->firstItem() + $loop->index }}.</td>
                        <td>{{ $exam->thesis->student->nama_mhs }}</td>
                        <td>{{ $exam->thesis->title }}</td>
                        <td>
                            <span class="badge {{ $statusClasses[$status] ?? 'bg-secondary' }}">
                                {{ $statusLabels[$status] ?? strtoupper($status) }}
                            </span>
                        </td>
                        <td>{{ $exam->created_at ? $exam->created_at->format('d M Y') : '-' }}</td>
                        <td>
                            @if($exam->status === 'penguji_ditetapkan')
                            <a href="{{ route('dekan.thesis.exam.show', ['exam' => $exam->id]) }}"
                                class="btn btn-sm btn-info">
                                <i class="fas fa-edit me-1"></i> Proses
                            </a>
                            @elseif(in_array($exam->status, ['disetujui_dekan', 'revisi_dekan', 'dijadwalkan',
                            'selesai']))
                            <a href="{{ route('dekan.thesis.exam.show', ['exam' => $exam->id]) }}"
                                class="btn btn-sm btn-secondary">
                                <i class="fas fa-eye me-1"></i> Detail
                            </a>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">Tidak ada pengajuan ujian skripsi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="d-flex justify-content-center mt-4">
                {{ $exams->links() }}
            </div>
        </div>
    </div>

    {{-- Modal Detail --}}
    <div class="modal fade" id="dekanDetailModal" tabindex="-1" aria-labelledby="dekanDetailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dekanDetailModalLabel">Detail Ujian Skripsi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="modal-content-placeholder">

                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between" id="modal-footer-actions">

                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dekanDetailModal = document.getElementById('dekanDetailModal');

            dekanDetailModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                // Mengambil examId
                const examId = button.getAttribute('data-exam-id');
                const thesisTitle = button.getAttribute('data-thesis-title');
                const studentName = button.getAttribute('data-student-name');
                const studentNim = button.getAttribute('data-student-nim');
                const examiners = JSON.parse(button.getAttribute('data-examiners'));
                const supervisors = JSON.parse(button.getAttribute('data-supervisors'));
                const examInfo = JSON.parse(button.getAttribute('data-exam-info'));
                const status = examInfo.status;

                const statusLabels = {
                    'diajukan': 'Menunggu Verifikasi KTU',
                    'revisi': 'Revisi Diminta',
                    'terverifikasi': 'Terverifikasi KTU',
                    'penguji_ditetapkan': 'Menunggu Persetujuan Dekan',
                    'disetujui_dekan': 'Disetujui Dekan',
                    'revisi_dekan': 'Revisi dari Dekan',
                    'pelaksanaan': 'Pelaksanaan Ujian',
                    'dijadwalkan': 'Dijadwalkan',
                    'selesai': 'Selesai',
                };
                const statusLabel = statusLabels[status] || status.toUpperCase();

                const modalBody = document.getElementById('modal-content-placeholder');
                modalBody.innerHTML = `
                    <h4>Judul Skripsi</h4>
                    <p>${thesisTitle}</p>

                    <h5>Mahasiswa</h5>
                    <p>${studentName} (${studentNim})</p>

                    <div class="row">
                        <div class="col-md-6">
                            <h5>Pembimbing</h5>
                            <ul>
                                ${supervisors.map((supervisor) => `
                                    <li>${supervisor}</li>
                                `).join('')}
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>Penguji</h5>
                            <ul>
                                ${examiners.map((examiner, index) => `
                                    <li>Penguji ${index + 1}: ${examiner}</li>
                                `).join('')}
                            </ul>
                        </div>
                    </div>
                    <hr>
                    <h5>Informasi Ujian & Persetujuan</h5>
                    <p><strong>Status:</strong> <span class="badge bg-primary">${statusLabel}</span></p>
                    <p><strong>Jenis Ujian:</strong> ${examInfo.exam_type ?? 'N/A'}</p>
                    <p><strong>Jadwal Ujian:</strong> ${examInfo.scheduled_at ? new Date(examInfo.scheduled_at).toLocaleDateString() : '-'}</p>
                    <p><strong>Lokasi:</strong> ${examInfo.location ?? '-'}</p>
                    ${status === 'revisi_dekan' ? `<p><strong>Catatan Revisi:</strong> ${examInfo.revisi_notes}</p>` : ''}
                `;

                const modalFooter = document.getElementById('modal-footer-actions');
                modalFooter.innerHTML = ''; // Kosongkan footer terlebih dahulu

                // Hanya tampilkan tombol aksi jika statusnya 'penguji_ditetapkan'
                if (status === 'penguji_ditetapkan') {
                    modalFooter.innerHTML = `
                        <form id="revisiForm" method="POST" action="/dekan/ujian/skripsi/${examId}/revisi">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="revisi_notes" value="Silakan periksa kembali penguji.">
                            <button type="submit" class="btn btn-warning">Minta Revisi</button>
                        </form>
                        <form id="approveForm" method="POST" action="/dekan/ujian/skripsi/${examId}/approve">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success">Setujui Penguji</button>
                        </form>
                    `;
                } else {
                    modalFooter.innerHTML = `<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>`;
                }
            });
        });
    </script>
    @endpush
</x-main-layout>
