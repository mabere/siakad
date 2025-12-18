<x-main-layout>
    @section('title', 'Daftar Ujian yang Harus Dinilai')

    <div class="card card-bordered card-preview">
        <div class="card-inner">
            <div class="card-title-group mb-3 border-bottom">
                <div class="card-title">
                    <h5 class="card-title">Daftar Ujian Skripsi</h5>
                    <p class="text-soft">Berikut adalah daftar ujian skripsi yang perlu Anda nilai</p>
                </div>
            </div>
            <div class="card-inner p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="bg-light">
                            <tr>
                                <th scope="col">Mahasiswa</th>
                                <th scope="col">Judul Skripsi</th>
                                <th scope="col">Waktu & Lokasi</th>
                                <th scope="col" class="text-center">Status</th>
                                <th scope="col" class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($exams as $exam)
                            <tr>
                                <td>
                                    <div class="user-card">
                                        <div class="user-avatar bg-success-dim">
                                            <em class="icon ni ni-user-alt"></em>
                                        </div>
                                        <div class="user-info">
                                            <span class="lead-text">{{ $exam->thesis->student->user->name }}</span>
                                            <span class="sub-text">{{ $exam->thesis->student->nim }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-block" style="max-width: 300px;">
                                        <span class="lead-text" data-toggle="tooltip"
                                            title="{{ $exam->thesis->title }}">
                                            {{ \Illuminate\Support\Str::limit($exam->thesis->title, 50) }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <span class="lead-text">
                                        {{ \Carbon\Carbon::parse($exam->scheduled_at)->isoFormat('dddd, D MMMM YYYY
                                        [pukul] H:mm') }}
                                    </span>
                                    <span class="sub-text d-block">{{ $exam->location }}</span>
                                </td>
                                <td class="text-center">
                                    @if($exam->status == 'selesai')
                                    <span class="badge bg-outline-success">
                                        <em class="icon ni ni-check-circle"></em> Selesai
                                    </span>
                                    @elseif($exam->status == 'dijadwalkan')
                                    <span class="badge bg-outline-info">
                                        <em class="icon ni ni-calendar"></em> Dijadwalkan
                                    </span>
                                    @else
                                    <span class="badge bg-outline-primary">
                                        {{ ucfirst($exam->status) }}
                                    </span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <div class="btn-group">
                                        @if ($exam->status == 'selesai')
                                        <button type="button" class="btn btn-sm btn-info btn-trigger show-scores-btn"
                                            data-exam-id="{{ $exam->id }}" data-toggle="tooltip" title="Lihat Nilai">
                                            <em class="icon ni ni-eye-fill text-white"></em>
                                        </button>
                                        @else
                                        <a href="{{ route('nilai.examiner.exams.show', $exam->id) }}"
                                            class="btn btn-sm btn-primary btn-trigger" data-toggle="tooltip"
                                            title="Beri Penilaian">
                                            <em class="icon ni ni-edit-alt-fill"></em>
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <em class="icon ni ni-info text-soft" style="font-size: 2rem;"></em>
                                        <span class="text-soft">Tidak ada ujian yang perlu dinilai saat ini.</span>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewScoresModal" tabindex="-1" role="dialog" aria-labelledby="viewScoresModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewScoresModalLabel">Detail Penilaian Ujian</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="modal-scores-content">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close-modal-btn">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
    <script>
        $(document).ready(function() {
        $('.show-scores-btn').on('click', function(event) {
            event.preventDefault();

            let button = $(this);
            let examId = button.data('exam-id');
            let modal = $('#viewScoresModal');
            let scoresContent = $('#modal-scores-content');

            // Tampilkan modal dan spinner secara langsung
            modal.modal('show');
            scoresContent.html('<div class="d-flex justify-content-center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');

            // Lakukan panggilan AJAX untuk mengambil data
            $.ajax({
                url: '{{ url("nilai/ujian") }}/' + examId + '/scores',
                method: 'GET',
                success: function(response) {
                    // Gunakan setTimeout untuk menunda pengisian konten
                    // Durasi penundaan 1000ms = 1 detik
                    setTimeout(function() {
                        let htmlContent = '';

                        // Tampilkan skor per kriteria
                        if (response.scores && response.scores.length > 0) {
                            htmlContent += '<h6>Skor per Kriteria</h6>';
                            htmlContent += '<ul class="list-group list-group-flush">';
                            response.scores.forEach(function(item) {
                                htmlContent += '<li class="list-group-item d-flex justify-content-between align-items-center">';
                                htmlContent += '<div>' + item.criteria.name + ' <br> <small class="text-muted">' + (item.notes ? item.notes : 'Tidak ada catatan.') + '</small></div>';
                                htmlContent += '<span class="badge badge-primary badge-pill">' + item.score + '</span>';
                                htmlContent += '</li>';
                            });
                            htmlContent += '</ul>';
                        } else {
                            htmlContent += '<p class="text-muted">Tidak ada data skor yang ditemukan.</p>';
                        }

                        // Tampilkan komentar umum penguji
                        htmlContent += '<h6 class="mt-4">Catatan Umum Penguji</h6>';
                        htmlContent += '<p>' + (response.comment ? response.comment : 'Tidak ada catatan.') + '</p>';

                        scoresContent.html(htmlContent);
                    }, 1000); // <-- Durasi penundaan dalam milidetik (di sini 1 detik)
                },
                error: function(xhr) {
                    // Penundaan juga bisa diterapkan di sini
                    setTimeout(function() {
                        scoresContent.html('<p class="text-danger">Gagal memuat data. Silakan coba lagi.</p>');
                        console.error("Error loading scores:", xhr.responseText);
                    }, 1000);
                }
            });
        });
        $('.close-modal-btn').on('click', function() {
            $('#viewScoresModal').modal('hide');
        });
    });
    </script>
    @endpush
</x-main-layout>
