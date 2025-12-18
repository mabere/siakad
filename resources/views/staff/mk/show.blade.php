<x-main-layout>
    @section('title', 'Detail Mata Kuliah')

    <div class="nk-block">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 rounded-lg">
                        <div class="card-header bg-primary text-white text-center py-4">
                            <h5 class="mb-0"><u>@yield('title')</u></h5>
                            <h6 class="text-white">{{ $course->name }}</h6>
                        </div>
                        <div class="card-body p-4">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Program Studi:</strong> <span>{{ $course->department->nama }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Kode Mata Kuliah:</strong> <span>{{ $course->code }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Jumlah SKS:</strong> <span>{{ $course->sks }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Semester:</strong> <span>{{ $course->semester_number }}
                                        @if(in_array($course->semester_number, [1, 3, 5, 7]))
                                        (Ganjil)
                                        @elseif(in_array($course->semester_number, [2, 4, 6, 8]))
                                        (Genap)
                                        @else
                                        -
                                        @endif</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Kategori:</strong> <span>{{ $course->kategori }}</span>
                                </li>
                            </ul>
                        </div>
                        <div class="card-footer bg-light text-center py-3">
                            <a href="/staff/course" class="btn btn-warning">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-main-layout>