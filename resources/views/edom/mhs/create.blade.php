<x-main-layout>
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Isi EDOM</h3>
                <div class="nk-block-des text-soft">
                    <p>{{ $schedule->course->name }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="nk-block">
        @if ($errors->any())
        <div class="alert alert-danger alert-icon">
            <em class="icon ni ni-cross-circle"></em>
            <ul class="list">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form id="edomForm" action="{{ route('student.edom.store', $schedule->id) }}" method="POST">
            @csrf

            <!-- Informasi Mata Kuliah -->
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label text-soft">Mata Kuliah</label>
                                <div class="form-control-wrap">
                                    <p class="form-control-plaintext">{{ $schedule->course->name }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label text-soft">Dosen Pengampu</label>
                                <div class="form-control-wrap">
                                    @foreach($schedule->lecturersInSchedule as $lecturer)
                                    <p class="form-control-plaintext">{{ $lecturer->nama_dosen }}</p>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Petunjuk Pengisian -->
            <div class="card card-bordered card-preview mt-4">
                <div class="card-inner">
                    <h5 class="card-title">Petunjuk Pengisian</h5>
                    <div class="alert alert-info">
                        <ul class="list">
                            <li>Berikan penilaian sesuai dengan kenyataan yang Anda alami</li>
                            <li>Skala penilaian: 1 (Sangat Kurang) - 5 (Sangat Baik)</li>
                            <li>Semua pertanyaan wajib diisi</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Form Pertanyaan -->
            @foreach($questions as $category => $categoryQuestions)
            <div class="card card-bordered card-preview mt-4">
                <div class="card-inner">
                    <h5 class="card-title">{{ $category }}</h5>
                    <div class="row g-4">
                        @foreach($categoryQuestions as $question)
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">{{ $question['question_text'] }}</label>
                                <div class="form-control-wrap">
                                    <ul class="custom-control-group">
                                        @for($i = 1; $i <= 5; $i++) <li>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" class="custom-control-input"
                                                    name="responses[{{ $question['id'] }}][rating]"
                                                    id="q{{ $question['id'] }}_{{ $i }}" value="{{ $i }}" {{
                                                    old("responses.{$question['id']}.rating")==$i ? 'checked' : '' }}>
                                                <label class="custom-control-label"
                                                    for="q{{ $question['id'] }}_{{ $i }}">{{ $i }}</label>
                                            </div>
                                            </li>
                                            @endfor
                                    </ul>
                                    <input type="hidden" name="responses[{{ $question['id'] }}][question_id]"
                                        value="{{ $question['id'] }}">
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach

            <!-- Komentar -->
            <div class="card card-bordered card-preview mt-4">
                <div class="card-inner">
                    <div class="form-group">
                        <label class="form-label">Komentar/Saran (Opsional)</label>
                        <div class="form-control-wrap">
                            <textarea class="form-control" name="comments" rows="3"
                                placeholder="Berikan komentar atau saran untuk pengembangan pembelajaran...">{{ old('comments') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Submit -->
            <div class="card card-bordered card-preview mt-4">
                <div class="card-inner">
                    <div class="form-group">
                        <a href="{{ route('student.edom.index') }}" class="btn btn-sm btn-danger">
                            <i class="icon ni ni-reply"></i>
                        </a>
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="icon ni ni-save"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.getElementById('edomForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                // Validasi semua pertanyaan terjawab
                const totalQuestions = document.querySelectorAll('.custom-control-group').length;
                const answeredQuestions = document.querySelectorAll('input[type="radio"]:checked').length;

                if (answeredQuestions < totalQuestions) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: `Mohon isi semua pertanyaan sebelum mengirim evaluasi! (${answeredQuestions}/${totalQuestions} pertanyaan terjawab)`,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#1ee0ac'
                    });
                    return;
                }

                // Konfirmasi sebelum submit via AJAX
                Swal.fire({
                    icon: 'question',
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin menyimpan evaluasi ini?',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#1ee0ac',
                    cancelButtonColor: '#f1f3f5'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: this.action,
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
                                let errorMessage = 'Terjadi kesalahan saat menyimpan evaluasi';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                Swal.fire('Error', errorMessage, 'error');
                            }
                        });
                    }
                });
            });

            // Debugging untuk radio button
            document.querySelectorAll('input[type="radio"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    const totalQuestions = document.querySelectorAll('.custom-control-group').length;
                    const answeredQuestions = document.querySelectorAll('input[type="radio"]:checked').length;
                    console.log('Radio changed:', {
                        questionId: this.name,
                        value: this.value,
                        totalQuestions: totalQuestions,
                        answeredQuestions: answeredQuestions
                    });
                });
            });
    </script>
    @endpush
</x-main-layout>