<x-main-layout>
    @section('title', 'Daftar Kurikulum')

    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Daftar Kurikulum</h3>
            </div>
            <div class="nk-block-head-content">
                <a href="{{ route('curriculums.create') }}" class="btn btn-primary">
                    <em class="icon ni ni-plus"></em> Tambah Kurikulum
                </a>
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

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nama Kurikulum</th>
                                <th>Program Studi</th>
                                <th>Tahun Akademik</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($curriculums as $curriculum)
                            <tr>
                                <td>{{ \App\Helpers\CurriculumHelper::formatCurriculumName($curriculum) }}</td>
                                <td>{{ $curriculum->department->nama }}</td>
                                <td>{{ $curriculum->academicYear->ta }} {{ $curriculum->academicYear->semester }}</td>
                                <td>
                                    <span
                                        class="badge bg-{{ $curriculum->status === 'active' ? 'success' : ($curriculum->status === 'draft' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst($curriculum->status) }}
                                    </span>
                                </td>
                                <td>@can('create', App\Models\Curriculum::class)
                                    <li>
                                        <a href="#" data-bs-toggle="modal"
                                            data-bs-target="#copyModal-{{ $curriculum->id }}">
                                            <em class="icon ni ni-copy"></em> Salin Kurikulum
                                        </a>
                                    </li>
                                    @endcan
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <a href="#" class="btn btn-sm btn-icon btn-trigger" data-bs-toggle="dropdown">
                                            <em class="icon ni ni-more-h"></em>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <ul class="link-list-opt no-bdr">
                                                <li><a href="{{ route('curriculums.show', $curriculum) }}"><em
                                                            class="icon ni ni-eye"></em> Show</a></li>
                                                <li><a href="{{ route('curriculums.edit', $curriculum) }}"><em
                                                            class="icon ni ni-edit"></em> Edit</a></li>
                                                <li>
                                                    <a href="#"
                                                        onclick="event.preventDefault(); document.getElementById('delete-form-{{ $curriculum->id }}').submit();">
                                                        <em class="icon ni ni-trash"></em> Hapus
                                                    </a>
                                                    <form id="delete-form-{{ $curriculum->id }}"
                                                        action="{{ route('curriculums.destroy', $curriculum) }}"
                                                        method="POST" style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>

                                                <li>
                                                    <a href="{{ route('curriculums.courses.index', $curriculum) }}">
                                                        <em class="icon ni ni-list"></em> Lihat Mata Kuliah
                                                    </a>
                                                </li>

                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada kurikulum.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Copy Kurikulum -->
    @can('create', App\Models\Curriculum::class)
    @foreach($curriculums as $curriculum)
    <div class="modal fade" id="copyModal-{{ $curriculum->id }}" tabindex="-1"
        aria-labelledby="copyModalLabel-{{ $curriculum->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="copyModalLabel-{{ $curriculum->id }}">Salin Kurikulum: {{
                        $curriculum->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('curriculums.copy', $curriculum) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label" for="name-{{ $curriculum->id }}">Nama Kurikulum Baru</label>
                            <input type="text" name="name" id="name-{{ $curriculum->id }}" class="form-control"
                                value="{{ $curriculum->name }} (Copy)" required>
                            @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="academic_year_id-{{ $curriculum->id }}">Tahun
                                Akademik</label>
                            <select name="academic_year_id" id="academic_year_id-{{ $curriculum->id }}"
                                class="form-select" required>
                                @foreach(\App\Models\AcademicYear::where('status', 'active')->get() as $academicYear)
                                <option value="{{ $academicYear->id }}" {{ $academicYear->id ==
                                    ($curriculum->academic_year_id + 1) ? 'selected' : '' }}>{{ $academicYear->ta }} ({{
                                    $academicYear->semester }})
                                </option>
                                @endforeach
                            </select>
                            @error('academic_year_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="status-{{ $curriculum->id }}">Status</label>
                            <select name="status" id="status-{{ $curriculum->id }}" class="form-select" required>
                                <option value="active">Aktif</option>
                                <option value="draft" selected>Tidak Aktif</option>
                            </select>
                            @error('status')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Salin</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
    @endcan
    <script>
        document.querySelectorAll('[id^="delete-form-"]').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if (confirm('Apakah Anda yakin ingin menghapus mata kuliah ini?')) {
                    this.submit();
                }
            });
        });
    </script>
</x-main-layout>
