<x-main-layout>
    @section('title', 'Detail Publikasi')

    <div class="container my-5">
        <div class="card shadow-lg border-0">
            <!-- Header -->
            <div class="card-header bg-gradient-primary text-white py-3">
                <h5 class="mb-0 fw-bold text-uppercase">Detail Publikasi</h5>
            </div>

            <!-- Body -->
            <div class="card-body p-4">
                <dl class="row mb-0">
                    <!-- Judul -->
                    <dt class="col-md-3 col-lg-2 fw-semibold text-muted">Judul</dt>
                    <dd class="col-md-9 col-lg-10">{{ $publikasi->title }}</dd>

                    <!-- Penulis -->
                    <dt class="col-md-3 col-lg-2 fw-semibold text-muted">Penulis</dt>
                    <dd class="col-md-9 col-lg-10">
                        @php
                        $authors = $publikasi->lecturers->pluck('nama_dosen')->toArray();
                        $count = count($authors);
                        if ($count === 1) {
                        echo $authors[0];
                        } elseif ($count === 2) {
                        echo $authors[0] . ' & ' . $authors[1];
                        } else {
                        $lastAuthor = array_pop($authors);
                        echo implode(', ', $authors) . ', & ' . $lastAuthor;
                        }
                        @endphp
                    </dd>

                    <!-- Edisi -->
                    <dt class="col-md-3 col-lg-2 fw-semibold text-muted">Edisi</dt>
                    <dd class="col-md-9 col-lg-10">{{ $publikasi->issue }}</dd>

                    <!-- Tahun -->
                    <dt class="col-md-3 col-lg-2 fw-semibold text-muted">Tahun</dt>
                    <dd class="col-md-9 col-lg-10">{{ $publikasi->year }}</dd>

                    <!-- Halaman -->
                    <dt class="col-md-3 col-lg-2 fw-semibold text-muted">Halaman</dt>
                    <dd class="col-md-9 col-lg-10">{{ $publikasi->page }}</dd>

                    <!-- Abstrak -->
                    <dt class="col-md-3 col-lg-2 fw-semibold text-muted">Abstrak</dt>
                    <dd class="col-md-9 col-lg-10">
                        <p class="text-justify me-5">{!! $publikasi->abstract !!}</p>
                    </dd>
                </dl>
            </div>

            <!-- Footer -->
            <div class="card-footer bg-light py-3 text-end">
                <a href="{{ url('admin/publication') }}" class="btn btn-outline-warning me-2">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
                <a href="{{ url('admin/publication/' . $publikasi->id . '/edit') }}" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i> Edit
                </a>
            </div>
        </div>
    </div>

    <!-- CSS Kustom -->
    <style>
        .bg-gradient-primary {
            background: linear-gradient(90deg, #007bff 0%, #0056b3 100%);
        }

        .card {
            border-radius: 10px;
            overflow: hidden;
        }

        .card-body dl dt {
            padding-top: 0.5rem;
            font-size: 0.95rem;
        }

        .card-body dl dd {
            padding-top: 0.5rem;
            font-size: 1rem;
            color: #333;
        }

        .text-justify {
            text-align: justify;
        }

        .btn {
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
        }
    </style>
</x-main-layout>