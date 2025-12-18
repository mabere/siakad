<x-main-layout>
    @section('title', 'Detail Evaluasi Dosen dan Mata Kuliah')
    @once
    <style>
        body {
            background-color: #F5F7FA;
            color: #333;
            font-family: 'Poppins', sans-serif;
        }

        .header-title {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 1.5rem;
            color: #FFF;
            margin-bottom: 2rem;
        }

        .card-custom {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease-in-out;
        }

        .card-custom:hover {
            transform: translateY(-5px);
        }

        .btn-custom {
            background-color: #4CAF50;
            border-color: #4CAF50;
            color: #FFF;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            transition: background-color 0.3s ease;
        }

        .btn-custom:hover {
            background-color: #388E3C;
            color: #FFF;
        }

        .btn-back {
            background-color: #FF9800;
            border-color: #FF9800;
        }

        .btn-back:hover {
            background-color: #F57C00;
        }

        .table-custom thead {
            background: #4CAF50;
            color: #FFF;
        }

        .table-custom tbody tr:hover {
            background-color: #E8F5E9;
        }

        .list-group-item {
            border: none;
            border-bottom: 1px solid #ECEFF1;
            padding: 1rem;
        }

        .alert-custom {
            background-color: #FFF3E0;
            color: #FF9800;
            border: none;
            border-radius: 8px;
        }
    </style>
    @endonce

    <div class="container mt-5">
        <!-- Header dengan Gradient Elegan -->
        <div class="header-title">
            <h4 class="mb-0 text-white">
                <em class="icon ni ni-book-read me-2"></em>
                Detail Evaluasi - {{ $schedule->schedulable->name }}
            </h4>
        </div>

        <!-- Tombol Kembali -->
        <a href="{{ route('kaprodi.edom.reports') }}" class="btn btn-back btn-custom mb-4">
            <em class="icon ni ni-arrow-left me-2"></em>
            Kembali ke Laporan
        </a>

        <!-- Informasi Mata Kuliah -->
        <div class="card card-custom mb-4">
            <div class="card-body">
                <h5 class="text-primary mb-3">
                    <em class="icon ni ni-info me-2"></em>
                    Informasi Mata Kuliah
                </h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <p><strong>Mata Kuliah:</strong> {{ $schedule->course->name }}</p>
                        <p><strong>Departemen:</strong> {{ $department->nama }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Dosen Pengampu:</strong></p>
                        <ul class="list-unstyled ms-4">
                            @foreach ($schedule->lecturersInSchedule as $lecturer)
                            <li><em class="icon ni ni-user me-1"></em>{{ $lecturer->nama_dosen }}</li>
                            @endforeach
                        </ul>
                        <p><strong>Jumlah Respons:</strong> {{ $schedule->responses->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Skor Rata-rata per Kategori -->
        @if ($schedule->responses->count() > 0)
        <div class="card card-custom mb-4">
            <div class="card-body">
                <h5 class="text-primary mb-3">
                    <em class="icon ni ni-bar-chart me-2"></em>
                    Skor Rata-rata per Kategori
                </h5>
                <ul class="list-group">
                    @foreach ($averageRatings as $category => $avg)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>{{ $category }}</span>
                        <span class="badge bg-success rounded-pill">{{ number_format($avg, 2) }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @else
        <div class="alert alert-custom">
            <em class="icon ni ni-alert-circle me-2"></em>
            Belum ada evaluasi untuk mata kuliah ini.
        </div>
        @endif

        <!-- Detail Respons Mahasiswa -->
        @if ($schedule->responses->count() > 0)
        <div class="card card-custom">
            <div class="card-body">
                <h5 class="text-primary mb-3">
                    <em class="icon ni ni-table-view me-2"></em>
                    Detail Respons Mahasiswa
                </h5>
                <div class="table-responsive">
                    <table class="table table-striped table-custom">
                        <thead>
                            <tr>
                                <th scope="col">Mahasiswa</th>
                                <th scope="col">Pertanyaan</th>
                                <th scope="col">Kategori</th>
                                <th scope="col">Rating</th>
                                <th scope="col">Komentar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($responsesDetail as $response)
                            <tr>
                                <td>{{ $response['student_id'] }}</td>
                                <td>{{ $response['question'] }}</td>
                                <td>{{ $response['category'] }}</td>
                                <td>{{ $response['rating'] }}</td>
                                <td>{{ $response['comment'] ?? 'Tidak ada komentar' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Tidak ada data respons mahasiswa.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
</x-main-layout>