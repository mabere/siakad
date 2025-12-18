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

        <form id="edomForm" action="{{ route('edom.store', $schedule->id) }}" method="POST">
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
                                                    name="answers[{{ $question['id'] }}]"
                                                    id="q{{ $question['id'] }}_{{ $i }}" value="{{ $i }}" {{
                                                    old("answers.{$question['id']}")==$i ? 'checked' : '' }}>
                                                <label class="custom-control-label"
                                                    for="q{{ $question['id'] }}_{{ $i }}">{{ $i }}</label>
                                            </div>
                                            </li>
                                            @endfor
                                    </ul>
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
                        <button type="submit" class="btn btn-lg btn-info">
                            <i class="icon ni ni-save"></i>
                        </button>
                        <a href="{{ route('edom.index') }}" class="btn btn-lg btn-danger">
                            <i class="icon ni ni-arrow-left"></i>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.getElementById('edomForm').addEventListener('submit', function(e) {
            // Hitung total pertanyaan (tidak termasuk textarea komentar)
            const totalQuestions = document.querySelectorAll('.custom-control-group').length;

            // Hitung pertanyaan yang sudah dijawab
            const answeredQuestions = document.querySelectorAll('input[type="radio"]:checked').length;

            console.log('Debug:', {
                totalQuestions: totalQuestions,
                answeredQuestions: answeredQuestions
            });

            if (answeredQuestions < totalQuestions) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: `Mohon isi semua pertanyaan sebelum mengirim evaluasi! (${answeredQuestions}/${totalQuestions} pertanyaan terjawab)`,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#1ee0ac'
                });
            } else {
                // Konfirmasi sebelum submit
                e.preventDefault();
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
                        document.getElementById('edomForm').submit();
                    }
                });
            }
        });

        // Tambahkan event listener untuk debugging
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
    </x-app-layout>