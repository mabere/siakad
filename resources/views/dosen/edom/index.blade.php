<x-main-layout>
    @section('title', 'Evaluasi Dosen oleh Mahasiswa (EDOM)')
    <div class="nk-block">
        <div class="container py-4">
            <h3 class="mb-4">@yield('title')</h3>
            <div class="card card-bordered">
                <div class="card-inner">
                    <h6 class="title">Ringkasan EDOM</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong>Total Respons:</strong> {{ $totalResponses }}</p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Mata Kuliah Dievaluasi:</strong> {{ $totalCoursesEvaluated }}</p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Rata-rata Keseluruhan:</strong> {{ $overallAverageRating }}</p>
                        </div>
                    </div>
                    <div class="table-responsive mt-3">
                        <table class="table table-striped table-bordered">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>Mata Kuliah</th>
                                    <th>Program Studi</th>
                                    <th>Rata-rata Rating</th>
                                    <th>% Kehadiran</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($schedules as $schedule)
                                <tr>
                                    <td>
                                        {{ $schedule->course_name ?? 'N/A' }}
                                        <br>
                                        <small class="text-muted">({{ $schedule->course_code ?? 'N/A' }})</small>
                                    </td>
                                    <td>{{ $schedule->course->department->nama ?? 'MKDU / N/A' }}</td>
                                    <td>
                                        @if(isset($scheduleAverages[$schedule->id]) &&
                                        $scheduleAverages[$schedule->id]['ratings'])
                                        @foreach($scheduleAverages[$schedule->id]['ratings'] as $category => $rating)
                                        {{ $category }}: {{ $rating }}<br>
                                        @endforeach
                                        @else
                                        Tidak ada data
                                        @endif
                                    </td>
                                    <td>{{ $scheduleAverages[$schedule->id]['attendancePercentage'] ?? 'N/A' }}%</td>
                                    <td>
                                        <a class="btn btn-sm btn-info"
                                            href="{{ route('lecturer.edom.schedule.detail', $schedule->id) }}"
                                            data-toggle="tooltip" title="Lihat Detail">
                                            <em class="icon ni ni-eye"></em>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada mata kuliah yang dievaluasi.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-main-layout>
