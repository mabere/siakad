<x-main-layout>
    @section('title', 'Mata Kuliah: ' . \App\Helpers\CurriculumHelper::formatCurriculumName($curriculum))

    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Mata Kuliah:
                    {{ \App\Helpers\CurriculumHelper::formatCurriculumName($curriculum) }}</h3>
            </div>
            <div class="nk-block-head-content">
                @can('create', App\Models\Course::class)
                <a href="{{ route('curriculums.courses.create', $curriculum) }}" class="btn btn-primary">
                    <em class="icon ni ni-plus"></em> Tambah MK Prodi
                </a>
                @endcan
                @can('attachMkdu', $curriculum)
                <a href="{{ route('curriculums.mkdu.create', $curriculum) }}" class="btn btn-info">
                    <em class="icon ni ni-link"></em> Tautkan MKDU
                </a>
                @endcan
                @can('export', App\Models\Course::class)
                <a href="{{ route('curriculums.courses.export', $curriculum) }}" class="btn btn-outline-primary">
                    <em class="icon ni ni-download"></em> Ekspor
                </a>
                @endcan
                @can('import', App\Models\Course::class)
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                    data-bs-target="#importModal">
                    <em class="icon ni ni-upload"></em> Impor
                </button>
                @endcan
                <a href="{{ route('curriculums.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </div>

    <div class="nk-block">
        <div class="card card-bordered">
            <div class="card-inner">
                @if(session('success'))
                <div class="alert alert-success alert-icon">
                    <em class="icon ni ni-check-circle"></em>
                    {{ session('success') }}
                </div>
                @endif
                @if($errors->has('error'))
                <div class="alert alert-danger alert-icon">
                    <em class="icon ni ni-cross-circle"></em>
                    {{ $errors->first('error') }}
                </div>
                @endif

                <div class="timeline timeline-bordered timeline-simple timeline-center">
                    @forelse($coursesBySemester as $semester => $semesterCourses)
                    <div class="timeline-items">
                        <div class="timeline-item-inner">
                            <div class="card-title card-header bg-info text-white mt-3">Semester {{ $semester }}</div>
                            <div class="timeline-content">
                                <div class="table-responsive">
                                    <table class="table table-striped align-middle">
                                        <thead>
                                            <tr>
                                                <th>Kode</th>
                                                <th>Nama</th>
                                                <th>SKS</th>
                                                <th>Kategori</th>
                                                <th>Sumber</th>
                                                <th>Prasyarat</th>
                                                <th>Silabus</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($semesterCourses as $course)
                                            <tr>
                                                <td>{{ $course->code }}</td>
                                                <td>{{ $course->name }}</td>
                                                <td>{{ $course->sks }}</td>
                                                <td>{{ $course->kategori ?? '-' }}</td>
                                                <td>
                                                    @if($course->is_mkdu)
                                                    <span class="badge bg-warning">MKDU</span>
                                                    @else
                                                    <span class="badge bg-primary">Prodi</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(!$course->is_mkdu)
                                                    @php
                                                    try {
                                                    echo \App\Helpers\CurriculumHelper::formatPrerequisites($course);
                                                    } catch (\Exception $e) {
                                                    echo '-';
                                                    \Log::error('Failed to format prerequisites', [
                                                    'course_id' => $course->id,
                                                    'error' => $e->getMessage(),
                                                    ]);
                                                    }
                                                    @endphp
                                                    @else
                                                    -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($course->syllabus_path)
                                                    <a href="{{ Storage::url($course->syllabus_path) }}"
                                                        class="btn btn-sm btn-outline-primary" target="_blank">Unduh</a>
                                                    @else
                                                    -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($course->is_mkdu)
                                                    <div class="btn-group">
                                                        @can('update', \App\Models\MkduCourse::class)
                                                        <a href="{{ route('mkdu.edit', $course->id) }}"
                                                            class="btn btn-md btn-icon btn-trigger text-warning"
                                                            title="Edit Data MKDU">
                                                            <em class="icon ni ni-edit"></em>
                                                        </a>
                                                        @endcan
                                                        @can('detachMkdu', $curriculum)
                                                        <a href="#" class="btn btn-md btn-icon btn-trigger text-danger"
                                                            title="Lepaskan MKDU dari Kurikulum Ini"
                                                            onclick="event.preventDefault(); document.getElementById('detach-mkdu-form-{{ $curriculum->id }}-{{ $course->id }}-{{ $course->semester_number_for_grouping }}').submit();">
                                                            <em class="icon ni ni-trash"></em>
                                                        </a>
                                                        <form
                                                            id="detach-mkdu-form-{{ $curriculum->id }}-{{ $course->id }}-{{ $course->semester_number_for_grouping }}"
                                                            action="{{ route('curriculums.mkdu.detach', [$curriculum->id, $course->id]) }}"
                                                            method="POST" style="display: none;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <input type="hidden" name="semester_number_to_detach"
                                                                value="{{ $course->semester_number_for_grouping }}">
                                                        </form>
                                                        @endcan
                                                    </div>
                                                    @else
                                                    @can('update', $course)
                                                    <div class="btn-group">
                                                        <a href="{{ route('curriculums.courses.edit', [$curriculum, $course]) }}"
                                                            class="btn btn-md btn-icon btn-trigger text-warning"
                                                            title="Edit Mata Kuliah Prodi">
                                                            <em class="icon ni ni-edit"></em>
                                                        </a>
                                                        <a href="#" class="btn btn-md btn-icon btn-trigger text-danger"
                                                            title="Hapus Mata Kuliah Prodi"
                                                            onclick="event.preventDefault(); document.getElementById('delete-program-form-{{ $course->id }}').submit();">
                                                            <em class="icon ni ni-trash"></em>
                                                        </a>
                                                        <form id="delete-program-form-{{ $course->id }}"
                                                            action="{{ route('curriculums.courses.destroy', [$curriculum, $course]) }}"
                                                            method="POST" style="display: none;">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                    </div>
                                                    @endcan
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center">Belum ada mata kuliah yang terdaftar di kurikulum ini.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @can('import', App\Models\Course::class)
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Impor Mata Kuliah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('curriculums.courses.import', $curriculum) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label" for="file">Pilih File Excel</label>
                            <input type="file" name="file" id="file" class="form-control" accept=".xlsx" required>
                            @error('file')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <p><a href="{{ asset('templates/course_import_template.xlsx') }}"
                                class="btn btn-sm btn-outline-secondary">Unduh Template Excel</a></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Impor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan

    <script>
        document.querySelectorAll('[id^="delete-program-form-"]').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if (confirm('Apakah Anda yakin ingin menghapus mata kuliah prodi ini?')) {
                    this.submit();
                }
            });
        });

        document.querySelectorAll('[id^="detach-mkdu-form-"]').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if (confirm('Apakah Anda yakin ingin melepaskan mata kuliah MKDU ini dari kurikulum ini? Ini tidak akan menghapus data master MKDU.')) {
                    this.submit();
                }
            });
        });
    </script>
</x-main-layout>
