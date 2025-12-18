<x-main-layout>
    @section('title', 'Detail Evaluasi Mengajar')
    <div class="container">
        <div class="components-preview wide-lg mx-auto mb-3">
            <div class="nk-block nk-block-lg">
                <div class="nk-block-head">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h4 class="nk-block-title">Detail Evaluasi - {{ $schedule->course_name ?? 'N/A' }}</h4>
                        </div>
                        <div class="nk-block-head-content">
                            <a href="{{ route('lecturer.edom.index') }}" class="btn btn-primary">
                                <em class="icon ni ni-reply"></em>
                                <span>Kembali ke Dashboard EDOM</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="row g-2">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5>Informasi Mata Kuliah</h5>
                            <p><strong>Mata Kuliah:</strong> {{ $schedule->course_name ?? 'N/A' }}</p>
                            <p>
                                <strong>Kode MK:</strong> {{ $schedule->course_code ?? 'N/A' }}
                                <br>
                                <strong>Tipe MK:</strong> {{ $schedule->course_type ?? 'N/A' }}
                            </p>
                            <p>
                                <strong>Departemen:</strong>
                                @if($schedule->schedulable_type === 'App\Models\Course' &&
                                $schedule->course->department)
                                {{ $schedule->course->department->nama }}
                                @else
                                MKDU / Tidak Berlaku
                                @endif
                            </p>
                            <p><strong>Dosen Pengampu:</strong>
                                @foreach ($schedule->lecturersInSchedule as $lecturerItem)
                                {{ $lecturerItem->nama_dosen }}<br>
                                @endforeach
                            </p>
                            <p><strong>Jumlah Respons:</strong> {{ $schedule->responses->count() }}</p>
                            <p><strong>Persentase Kehadiran:</strong> {{ $attendancePercentage ?? 'N/A' }}%</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    @if ($schedule->responses->isNotEmpty())
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5>Skor Rata-rata per Kategori</h5>
                            <ul class="list-group">
                                @foreach ($averageRatings as $category => $avg)
                                <li class="list-group-item">{{ $category }}: {{ $avg }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-info">
                        Belum ada evaluasi untuk mata kuliah ini.
                    </div>
                    @endif
                </div>

                @if ($schedule->responses->isNotEmpty())
                <div class="card">
                    <div class="card-body">
                        <h5>Detail Respons Mahasiswa</h5>
                        <table class="table table-bordered">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>Mahasiswa</th>
                                    <th>Pertanyaan</th>
                                    <th>Kategori</th>
                                    <th>Rating</th>
                                    <th>Komentar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($responsesDetail as $response)
                                <tr>
                                    <td>{{ $response['student_id'] }}</td>
                                    <td>{{ $response['question'] }}</td>
                                    <td>{{ $response['category'] }}</td>
                                    <td>{{ $response['rating'] }}</td>
                                    <td>{{ $response['comment'] ?? 'Tidak ada komentar' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
                <div class="mt-3">
                    <a class="btn btn-sm btn-warning" href="{{ route('lecturer.edom.index') }}">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</x-main-layout>
