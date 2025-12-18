<x-main-layout>
    @section('title', 'Dashboard KTU')

    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dashboard KTU</h1>
        </div>

        <div class="row">

            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Dosen
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $dosenCount }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Total Mahasiswa
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $mahasiswaCount }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Total Departemen
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $departmentsCount }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-building fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-6 col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Distribusi Status Mahasiswa</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-pie pt-4">
                            <canvas id="studentStatusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Distribusi Status Permohonan Ujian</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-pie pt-4">
                            <canvas id="examStatusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12 col-lg-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Mahasiswa per Program Studi</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-bar">
                            <canvas id="studentsPerDepartmentChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Permohonan Ujian Menunggu</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Mahasiswa</th>
                                        <th>Program Studi</th>
                                        <th>Judul Tesis</th>
                                        <th>Status</th>
                                        <th>Tanggal Pengajuan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingExams as $exam)
                                    <tr>
                                        <td>{{ $exam->thesis->student->user->name }}</td>
                                        <td>{{ $exam->thesis->student->department->nama }}</td>
                                        <td>{{ $exam->thesis->title }}</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $exam->status)) }}</td>
                                        <td>{{ $exam->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <a href="{{ route('ktu.thesis.exam.show', $exam->id) }}"
                                                class="btn btn-sm btn-primary">Verifikasi</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const studentsPerDepartmentData = {!! json_encode($studentsPerDepartment) !!};
            const studentsPerDepartmentLabels = studentsPerDepartmentData.map(d => d.label);
            const studentsPerDepartmentValues = studentsPerDepartmentData.map(d => d.total);
            const studentsPerDepartmentCtx = document.getElementById('studentsPerDepartmentChart').getContext('2d');
            new Chart(studentsPerDepartmentCtx, {
                type: 'bar',
                data: {
                    labels: studentsPerDepartmentLabels,
                    datasets: [{
                        label: 'Jumlah Mahasiswa',
                        data: studentsPerDepartmentValues,
                        backgroundColor: 'rgba(78, 115, 223, 0.8)',
                        borderColor: 'rgba(78, 115, 223, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // --- Data Status Mahasiswa (Pie Chart) ---
            const studentStatusData = {!! json_encode($studentStatusDistribution) !!};
            const studentStatusLabels = studentStatusData.map(d => d.label);
            const studentStatusValues = studentStatusData.map(d => d.total);
            const studentStatusCtx = document.getElementById('studentStatusChart').getContext('2d');
            new Chart(studentStatusCtx, {
                type: 'doughnut',
                data: {
                    labels: studentStatusLabels,
                    datasets: [{
                        data: studentStatusValues,
                        backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'],
                        hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda636'],
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    tooltips: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyFontColor: "#858796",
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        caretPadding: 10,
                    },
                    legend: {
                        display: true
                    },
                    cutoutPercentage: 80,
                },
            });

            // --- Data Status Permohonan Ujian (Pie Chart) ---
            const examStatusData = {!! json_encode($examStatusDistribution) !!};
            const examStatusLabels = examStatusData.map(d => d.label);
            const examStatusValues = examStatusData.map(d => d.total);
            const examStatusCtx = document.getElementById('examStatusChart').getContext('2d');
            new Chart(examStatusCtx, {
                type: 'doughnut',
                data: {
                    labels: examStatusLabels,
                    datasets: [{
                        data: examStatusValues,
                        backgroundColor: ['#36b9cc', '#f6c23e', '#1cc88a'],
                        hoverBackgroundColor: ['#2c9faf', '#dda636', '#17a673'],
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    tooltips: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyFontColor: "#858796",
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        caretPadding: 10,
                    },
                    legend: {
                        display: true
                    },
                    cutoutPercentage: 80,
                },
            });
        });
    </script>
    @endpush
</x-main-layout>
