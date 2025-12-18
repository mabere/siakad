<x-main-layout>
    @section('title', 'Daftar Pengajuan Ujian Skripsi')

    <div class="card mt-3">
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <div class="card-body">
            <h4>Daftar Pengajuan Ujian Skripsi</h4>
            <table class="table table-bordered table-hover mt-3">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Mahasiswa</th>
                        <th>NIM</th>
                        <th>Judul Skripsi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $exam)
                    <tr>
                        <td>{{ $exams->firstItem() + $loop->index }}</td>
                        <td>{{ $exam->thesis->student->nama_mhs ?? 'N/A' }}</td>
                        <td>{{ $exam->thesis->student->nim ?? 'N/A' }}</td>
                        <td>{{ $exam->thesis->title }}</td>
                        <td>
                            @php
                            $status = $exam->status;
                            $badgeClass = match($status) {
                            'terverifikasi' => 'bg-info text-dark',
                            'penguji_ditetapkan' => 'bg-primary',
                            'disetujui_dekan' => 'bg-success',
                            'dijadwalkan' => 'bg-info',
                            'pelaksanaan' => 'bg-info',
                            'lulus', 'lulus_revisi', 'selesai' => 'bg-success',
                            'revisi_dekan', 'ditolak' => 'bg-danger',
                            default => 'bg-secondary'
                            };
                            @endphp
                            <span class="badge {{ $badgeClass }}">
                                {{ match($status) {
                                'terverifikasi' => 'Siap Tetapkan Penguji',
                                'penguji_ditetapkan' => 'Menunggu Persetujuan Dekan',
                                'revisi_dekan' => 'Revisi Penguji Dekan',
                                'disetujui_dekan' => 'Disetujui Dekan',
                                'dijadwalkan' => 'Sudah Dijadwalkan',
                                'pelaksanaan' => 'Sedang Berlangsung',
                                'lulus' => 'Lulus',
                                'lulus_revisi' => 'Lulus (Revisi)',
                                'ditolak' => 'Ditolak',
                                'selesai' => 'Selesai',
                                default => 'Menunggu Verifikasi KTU'
                                } }}
                            </span>
                        </td>
                        <td>
                            @if($exam->status === 'terverifikasi')
                            <a href="{{ route('kaprodi.thesis.examiners.form', $exam->id) }}"
                                class="btn btn-sm btn-primary">
                                <em class="icon ni ni-users me-1"></em> Tetapkan Penguji
                            </a>
                            @elseif($exam->status === 'penguji_ditetapkan' || $exam->status === 'revisi_dekan')
                            <a href="{{ route('kaprodi.thesis.examiners.form', $exam->id) }}"
                                class="btn btn-sm btn-warning">
                                <em class="icon ni ni-edit me-1"></em> Lihat/Ubah Penguji
                            </a>
                            @else
                            <a href="{{ route('kaprodi.thesis.exam.show', $exam->id) }}"
                                class="btn btn-sm btn-secondary">
                                <em class="icon ni ni-eye me-1"></em> Lihat Detail
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">Belum ada pengajuan ujian yang menunggu penetapan penguji.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $exams->links() }}
        </div>
    </div>

    <div class="modal fade" id="examinerDetailModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Detail Penguji Skripsi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group mb-3" id="examiners-list">
                        {{-- Data penguji akan diisi oleh JavaScript --}}
                    </ul>
                    <button id="editExaminersButton" class="btn btn-sm btn-warning mt-3" data-bs-toggle="modal"
                        data-bs-target="#editExaminersModal">
                        Ubah Penguji
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editExaminersModal" tabindex="-1" aria-labelledby="editExaminersModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editExaminersModalLabel">Ubah Penguji Skripsi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editExaminersForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div id="dynamic-examiners-fields">
                            {{-- Select box penguji akan di-generate oleh JavaScript --}}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const allLecturers = @json($lecturers);
        const examinerDetailModal = document.getElementById('examinerDetailModal');
        const editExaminersModal = document.getElementById('editExaminersModal');

        // Modal untuk melihat detail penguji
        examinerDetailModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const examinersData = JSON.parse(button.getAttribute('data-examiners'));
            const examId = button.getAttribute('data-exam-id');
            const examType = button.getAttribute('data-exam-type');

            const examinersList = document.getElementById('examiners-list');
            examinersList.innerHTML = '';
            examinersData.forEach((examiner, index) => {
                const li = document.createElement('li');
                li.className = 'list-group-item';
                li.innerHTML = `<strong>Penguji ${index + 1}:</strong> ${examiner.lecturer.user.name}`;
                examinersList.appendChild(li);
            });

            const editExaminersButton = document.getElementById('editExaminersButton');
            editExaminersButton.setAttribute('data-exam-id', examId);
            editExaminersButton.setAttribute('data-exam-type', examType);
            editExaminersButton.setAttribute('data-examiners', JSON.stringify(examinersData.map(e => e.lecturer.id)));
        });

        // Modal untuk mengubah penguji
        editExaminersModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const examId = button.getAttribute('data-exam-id');
            const examType = button.getAttribute('data-exam-type');
            const currentExaminerIds = JSON.parse(button.getAttribute('data-examiners'));

            const form = document.getElementById('editExaminersForm');
            form.action = `/ujian/skripsi/${examId}/penguji`;

            const requiredExaminers = (examType === 'tutup') ? 3 : 2;
            const examinersFieldsContainer = document.getElementById('dynamic-examiners-fields');
            examinersFieldsContainer.innerHTML = '';

            for (let i = 0; i < requiredExaminers; i++) {
                const lecturerId = currentExaminerIds[i] || '';
                const div = document.createElement('div');
                div.className = 'mb-3';
                div.innerHTML = `
                    <label for="examiner_${i + 1}" class="form-label">Penguji ${i + 1}</label>
                    <select class="form-select" name="examiners[]" required>
                        <option value="">-- Pilih Dosen --</option>
                        ${allLecturers.map(lecturer => `
                            <option value="${lecturer.id}" ${lecturer.id == lecturerId ? 'selected' : ''}>
                                ${lecturer.user.name}
                            </option>
                        `).join('')}
                    </select>
                `;
                examinersFieldsContainer.appendChild(div);
            }
        });
    </script>
    @endpush
</x-main-layout>
