<x-main-layout>
    @section('title', 'Laporan EDOM')

    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <div class="nk-block-des text-soft">
                    <p>{{ $schedule->course->name }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="nk-block">
        <!-- Informasi Mata Kuliah -->
        <div class="card card-bordered card-preview">
            <div class="card-header">
                <h4 class="nk-block-title card-title">@yield('title')</h4>
            </div>
            <div class="card-inner">
                <div class="row g-gs">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label text-soft">
                                <h5>Mata Kuliah</h5>
                            </label>
                            <div class="form-control-wrap">
                                <p class="form-control-plaintext">{{ $schedule->course->name }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label text-soft">Dosen Pengampu</label>
                            <div class="form-control-wrap">
                                @foreach($schedule->lecturersInSchedule as $lecturer)
                                <p class="form-control-plaintext">{{ $lecturer->nama_dosen }}</p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label text-soft">Tahun Akademik</label>
                            <div class="form-control-wrap">
                                <p class="form-control-plaintext">{{ $academicYear->ta }} - {{ $academicYear->semester
                                    }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label text-soft">Total Responden</label>
                            <div class="form-control-wrap">
                                <p class="form-control-plaintext">{{ $totalResponden }} Mahasiswa</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hasil Evaluasi per Kategori -->
        <div class="card card-bordered card-preview mt-4">
            <div class="card-inner">
                <h5 class="card-title">Hasil Evaluasi per Kategori</h5>
                <div class="row g-gs">
                    @foreach($results as $category => $data)
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">{{ $categoryNames[$category] }}</h6>
                                    </div>
                                </div>
                                <div class="align-end flex-sm-wrap g-4 flex-md-nowrap">
                                    <div class="nk-sale-data">
                                        <span class="amount">{{ number_format($data['average'], 2) }}/5.00</span>
                                        <div class="progress progress-md">
                                            <div class="progress-bar bg-primary"
                                                data-progress="{{ ($data['average']/5)*100 }}"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Detail per Aspek -->
        <div class="card card-bordered card-preview mt-4">
            <div class="card-inner">
                <h5 class="card-title">Detail per Aspek Penilaian</h5>
                <table class="table table-ulogs">
                    <thead class="table-light">
                        <tr>
                            <th class="tb-col-os"><span class="overline-title">Aspek Penilaian</span></th>
                            <th class="tb-col-time"><span class="overline-title">Rata-rata</span></th>
                            <th class="tb-col-time"><span class="overline-title">Kategori</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($detailResults as $questionId => $detail)
                        <tr>
                            <td class="tb-col-os">{{ $detail['question_text'] }}</td>
                            <td class="tb-col-time">
                                <span class="sub-text">{{ number_format($detail['average'], 2)}}</span>
                            </td>
                            <td class="tb-col-time">
                                <span class="sub-text">{{ $categoryNames[$detail['category']]}}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Grafik Radar -->
        <div class="card card-bordered card-preview mt-4">
            <div class="card-header">
                <h5 class="card-title">Grafik Evaluasi</h5>
            </div>
            <div class="card-inner">
                <div class="nk-ck">
                    <canvas id="radarChart" style="max-height: 400px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('radarChart');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json(array_values($categoryNames)),
                    datasets: [{
                        label: 'Skor EDOM',
                        data: @json(array_column($results, 'average')),
                        fill: true,
                        backgroundColor: 'rgba(59, 130, 246, 0.5)',
                        borderColor: 'rgb(59, 130, 246)',
                        pointBackgroundColor: 'rgb(59, 130, 246)',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: 'rgb(59, 130, 246)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        r: {
                            angleLines: {
                                display: true
                            },
                            suggestedMin: 0,
                            suggestedMax: 5,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Hasil Evaluasi Dosen Mengajar'
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-main-layout>