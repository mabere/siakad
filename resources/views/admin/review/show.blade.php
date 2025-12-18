<x-main-layout>
    <div class="card card-bordered">
        <div class="card-inner">
            <div class="card-title-group mb-3">
                <div class="card-title">
                    <h4 class="title">Detail Hasil Ujian</h4>
                    <p class="text-soft">Tinjauan lengkap hasil penilaian untuk ujian ini.</p>
                </div>
                <div class="card-tools">
                    <a href="{{ route('review.nilai.ujian.index') }}" class="btn btn-primary">
                        <em class="icon ni ni-arrow-left"></em>
                        <span>Kembali</span>
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8 mb-4">
                    <div class="card card-bordered h-100">
                        <div class="card-inner">
                            <h6>Ringkasan Ujian</h6>
                            <hr>
                            <div class="row gy-3">
                                <div class="col-12">
                                    <h6 class="title">Mahasiswa</h6>
                                    <p>{{ $thesis_exam->thesis->student->user->name }} ({{
                                        $thesis_exam->thesis->student->nim }})</p>
                                </div>
                                <div class="col-12">
                                    <h6 class="title">Judul Skripsi</h6>
                                    <p>{{ $thesis_exam->thesis->title }}</p>
                                </div>
                                <div class="col-12">
                                    <h6 class="title">Jenis Ujian</h6>
                                    <p>{{ ucfirst($thesis_exam->exam_type) }}</p>
                                </div>
                                <div class="col-12">
                                    <h6 class="title">Waktu dan Lokasi</h6>
                                    <p>{{ \Carbon\Carbon::parse($thesis_exam->scheduled_at)->isoFormat('dddd, D MMMM
                                        YYYY [pukul] H:mm') }} di {{ $thesis_exam->location }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 col-xxl-12">
                    <div class="nk-order-ovwg-data buy">
                        <div class="amount">
                            <h2 class="display-3 text-primary">{{ number_format($thesis_exam->final_score, 2) }}</h2>
                        </div>
                        <div class="text-primary mb-2">
                            <h6 class="text-soft">Nilai Akhir Rata-Rata Penguji</h6>
                        </div>
                        <div class="title">@if($thesis_exam->status == 'selesai')
                            <span class="badge bg-warning">Menunggu Keputusan</span>
                            @elseif($thesis_exam->status == 'lulus')
                            <span class="badge bg-success">Lulus</span>
                            @elseif($thesis_exam->status == 'lulus_revisi')
                            <span class="badge bg-info">Lulus (Revisi)</span>
                            @elseif($thesis_exam->status == 'ditolak')
                            <span class="badge bg-danger">Ditolak</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="title">Detail Penilaian per Dosen Penguji</h5>
            <div class="row">
                @php
                $urut = 1;
                @endphp
                @forelse ($scoresByLecturer as $lecturerId => $scores)
                @php
                $lecturer = $scores->first()->lecturer;
                $totalWeightedScore = 0;
                $totalWeight = 1;
                $lecturerRole = 'Penguji';

                if ($lecturerId == $thesis_exam->chairman_id) {
                $lecturerRole = 'Ketua Penguji';
                } elseif ($lecturerId == $thesis_exam->secretary_id) {
                $lecturerRole = 'Sekretaris Penguji';
                }

                foreach ($scores as $index => $score) {
                $totalWeightedScore += ($score->score * $score->criteria->weight);
                $totalWeight += $score->criteria->weight;
                }
                $lecturerFinalScore = $totalWeight > 0 ? $totalWeightedScore / $totalWeight : 0;

                $examinerComment = optional($thesis_exam->examiners->where('lecturer_id',
                $lecturerId)->first())->comment;
                @endphp
                <div class="col-md-6 mb-4">
                    <div class="card card-bordered h-100">
                        <div class="card-inner">
                            <div class="card-header p-2 d-flex justify-content-between align-items-center mb-2">
                                <h6>{{ $lecturer->nama_dosen }} ({{ $lecturerRole . ' ' . $urut++}})</h6>
                                <span class="text-soft">Skor Total: {{ number_format($lecturerFinalScore, 2) }}</span>
                            </div>
                            <div class="nk-tnx-list">
                                <div class="nk-tnx-item">
                                    <div class="nk-tnx-col">
                                        <h6 class="title">Catatan Umum:</h6>
                                        <p>{{ $examinerComment ?? 'Tidak ada catatan.' }}</p>
                                    </div>
                                </div>
                            </div>
                            <h6 class="mt-3">Detail Skor:</h6>
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Kriteria</th>
                                        <th class="text-center">Bobot</th>
                                        <th class="text-center">Nilai</th>
                                        <th>Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($scores as $score)
                                    <tr>
                                        <td>{{ $score->criteria->name }}</td>
                                        <td class="text-center">{{ $score->criteria->weight }}%</td>
                                        <td class="text-center">{{ $score->score }}</td>
                                        <td>{{ $score->notes ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="alert alert-warning">
                        Belum ada penilaian yang masuk untuk ujian ini.
                    </div>
                </div>
                @endforelse
            </div>

            @if ($thesis_exam->status === 'selesai')
            <div class="card card-bordered mt-5">
                <div class="card-inner">
                    <h5 class="mb-3">Keputusan Akhir Ujian</h5>
                    <form action="{{ route('review.nilai.ujian.decide', $thesis_exam->id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">Tentukan Status Ujian</label>
                            <div class="form-control-wrap">
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="lulus" name="final_status"
                                        value="lulus" required>
                                    <label class="custom-control-label" for="lulus">Lulus Tanpa Revisi</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="lulus_revisi"
                                        name="final_status" value="lulus_revisi" required>
                                    <label class="custom-control-label" for="lulus_revisi">Lulus Dengan Revisi</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="ditolak" name="final_status"
                                        value="ditolak" required>
                                    <label class="custom-control-label" for="ditolak">Ditolak / Ujian Ulang</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group" id="revisi-notes-section" style="display: none;">
                            <label class="form-label" for="revisi_notes">Catatan/Revisi</label>
                            <div class="form-control-wrap">
                                <textarea name="revisi_notes" id="revisi_notes" class="form-control"
                                    rows="5"></textarea>
                            </div>
                            <small class="form-text text-muted">Wajib diisi jika status dipilih "Lulus Dengan Revisi"
                                atau "Ditolak".</small>
                        </div>

                        <button type="submit" class="btn btn-primary mt-3">Simpan Keputusan Akhir</button>
                    </form>
                </div>
            </div>
            @else
            <div class="card card-bordered mt-5">
                <div class="card-inner">
                    <h5 class="mb-3">Status Ujian Final</h5>
                    <p>Ujian ini telah memiliki keputusan akhir.</p>
                    <div class="nk-tnx-list">
                        @if($thesis_exam->status == 'lulus')
                        <div class="alert alert-success">Status: Lulus Tanpa Revisi</div>
                        @elseif($thesis_exam->status == 'lulus_revisi')
                        <div class="alert alert-info">Status: Lulus Dengan Revisi</div>
                        <h6 class="title mt-3">Catatan Revisi:</h6>
                        <p>{{ $thesis_exam->revisi_notes }}</p>
                        @elseif($thesis_exam->status == 'ditolak')
                        <div class="alert alert-danger">Status: Ditolak / Ujian Ulang</div>
                        <h6 class="title mt-3">Catatan:</h6>
                        <p>{{ $thesis_exam->revisi_notes }}</p>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    @push('scripts')
    <script>
        $(document).ready(function() {
            $('input[name="final_status"]').change(function() {
                if ($(this).val() === 'lulus') {
                    $('#revisi-notes-section').slideUp();
                    $('#revisi_notes').prop('required', false);
                } else {
                    $('#revisi-notes-section').slideDown();
                    $('#revisi_notes').prop('required', true);
                }
            });
        });
    </script>
    @endpush
</x-main-layout>
