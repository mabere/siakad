<x-main-layout>
    @section('title', 'Detail Mata Kuliah - {{ $course->name }}')
    <div class="nk-block">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 rounded-lg">
                        <div class="card-header bg-primary text-white text-center py-4">
                            <h4 class="mb-0"><u>{{ $course->name }}</u></h4>
                            <h6 class="text-light">Mata Kuliah</h6>
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
                                    <strong>Tingkat:</strong> <span>{{ $course->smt }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Semester:</strong> <span>{{ $course->semester }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Kategori:</strong> <span>{{ $course->kategori }}</span>
                                </li>
                            </ul>
                        </div>
                        <div class="card-footer bg-light text-center py-3">
                            <a href="{{ route('admin.mk.byDepartment', $course->department_id) }}"
                                class="btn btn-warning">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                            <a href="{{ route('admin.mk.edit', $course->id) }}" class="btn btn-info">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <form action="{{ route('admin.mk.destroy', $course->id) }}" method="POST"
                                style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('Yakin ingin menghapus?')">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-main-layout>
