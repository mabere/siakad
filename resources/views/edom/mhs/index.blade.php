<x-main-layout>
    @section('title', 'Evaluasi Dosen Oleh Mahasiswa (EDOM)')
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">@yield('title')</h3>
                <div class="nk-block-des text-soft">
                    <p>Daftar mata kuliah yang perlu dievaluasi</p>
                </div>
            </div>
        </div>
    </div>

    <x-custom.sweet-alert />

    <div class="container">
        @if (!$isEdomActive)
        <p class="alert alert-warning">Periode evaluasi belum aktif.</p>
        @elseif (!$schedules->count())
        <p class="alert alert-info">Tidak ada jadwal yang tersedia untuk dievaluasi.</p>
        @else

        <div class="container mt-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daftar Mata Kuliah</h5>
                    <span class="badge bg-light text-primary">{{ $schedules->count() }} Mata Kuliah</span>
                </div>
                <div class="card-body py-2 p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th scope="col">Mata Kuliah</th>
                                <th scope="col">Dosen</th>
                                <th scope="col" class="text-center">Status</th>
                                <th scope="col" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($schedules as $schedule)
                            <tr>
                                <td>
                                    <strong>{{ $schedule->course->name }}</strong>
                                    <small class="d-block text-muted">Kode MK: {{ $schedule->course->code }}</small>
                                </td>
                                <td>
                                    @foreach($schedule->lecturersInSchedule as $lecturer)
                                    <span class="badge bg-info text-white me-1">{{ $lecturer->nama_dosen }}</span>
                                    @endforeach
                                </td>
                                <td class="text-center">
                                    @if ($schedule->hasFilledEdom)
                                    <span class="badge bg-success">Sudah Dievaluasi</span>
                                    @else
                                    <span class="badge bg-warning text-dark">Belum Dievaluasi</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if (!$schedule->hasFilledEdom)
                                    <button class="btn btn-sm btn-outline-primary evaluate-btn"
                                        data-schedule-id="{{ $schedule->id }}" data-bs-toggle="modal"
                                        data-bs-target="#edomModal">
                                        <i class="fas fa-edit"></i> Evaluasi
                                    </button>
                                    @else
                                    <a href="{{ route('student.nilai.index') }}"
                                        class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-list-alt"></i> Lihat Nilai
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal untuk Form Evaluasi -->
        <div class="modal fade" id="edomModal" tabindex="-1" aria-labelledby="edomModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form id="edom-form" method="POST" action="">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="edomModalLabel">Evaluasi Dosen</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="schedule_id" id="schedule_id">

                            <!-- Tampilkan pertanyaan per kategori -->
                            @if ($questionnaire && $questionnaire->questions->count())
                            @php
                            $categories = $questionnaire->questions->groupBy('category');
                            $currentCategoryIndex = 0;
                            @endphp

                            @foreach ($categories as $category => $questions)
                            <div class="category-questions" data-category-index="{{ $currentCategoryIndex }}"
                                style="display: {{ $currentCategoryIndex == 0 ? 'block' : 'none' }};">
                                <h6>Kategori: {{ $category }}</h6>
                                @foreach ($questions as $question)
                                <div class="form-group">
                                    <label>{{ $question->question_text }}</label>
                                    <input type="number" class="form-control"
                                        name="responses[{{ $question->id }}][rating]" min="1" max="5" required>
                                    <input type="hidden" name="responses[{{ $question->id }}][question_id]"
                                        value="{{ $question->id }}">
                                </div>
                                @endforeach
                            </div>
                            @php $currentCategoryIndex++; @endphp
                            @endforeach

                            <!-- Navigasi Kategori -->
                            <div class="text-right mt-3">
                                <button type="button" class="btn btn-secondary prev-category"
                                    style="display: none;">Kategori Sebelumnya</button>
                                <button type="button" class="btn btn-primary next-category">Kategori Berikutnya</button>
                            </div>
                            @else
                            <p class="alert alert-warning">Tidak ada pertanyaan tersedia untuk kuesioner ini.</p>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary" id="submit-evaluation"
                                style="display: none;">Kirim Evaluasi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @endif
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            let currentCategoryIndex = 0;
            const totalCategories = $('.category-questions').length;

            // Fungsi untuk menampilkan kategori tertentu
            function showCategory(index) {
                $('.category-questions').hide();
                $(`.category-questions[data-category-index="${index}"]`).show();

                // Tampilkan/sembunyikan tombol navigasi
                if (index === 0) {
                    $('.prev-category').hide();
                } else {
                    $('.prev-category').show();
                }

                if (index === totalCategories - 1) {
                    $('.next-category').hide();
                    $('#submit-evaluation').show();
                } else {
                    $('.next-category').show();
                    $('#submit-evaluation').hide();
                }
            }

            // Navigasi ke kategori berikutnya
            $('.next-category').on('click', function() {
                if (currentCategoryIndex < totalCategories - 1) {
                    currentCategoryIndex++;
                    showCategory(currentCategoryIndex);
                }
            });

            // Navigasi ke kategori sebelumnya
            $('.prev-category').on('click', function() {
                if (currentCategoryIndex > 0) {
                    currentCategoryIndex--;
                    showCategory(currentCategoryIndex);
                }
            });

            // Inisialisasi tampilan kategori pertama
            showCategory(currentCategoryIndex);

            // Handle klik tombol evaluasi
            $('.evaluate-btn').on('click', function() {
                const scheduleId = $(this).data('schedule-id');
                $('#schedule_id').val(scheduleId);
                $('#edom-form').attr('action', '{{ route("student.edom.store", ":schedule") }}'.replace(':schedule', scheduleId));
            });

            // Handle submit form
            $('#edom-form').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                formData.append('_token', '{{ csrf_token() }}');

                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Yakin mengirim evaluasi ini?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Kirim',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: $(this).attr('action'),
                            method: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Berhasil',
                                        text: response.message,
                                        icon: 'success'
                                    }).then(() => {
                                        window.location.href = response.redirect;
                                    });
                                } else {
                                    Swal.fire('Gagal', response.message, 'error');
                                }
                            },
                            error: function(xhr) {
                                Swal.fire('Error', 'Terjadi kesalahan, coba lagi', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>

</x-main-layout>
