<x-main-layout>
    @section('title', 'Daftar Kegiatan')
    <div class="nk-content">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h5 class="nk-block-title page-title">Jenis Surat</h5>
                            </div>
                            <div class="nk-block-head-content">
                                <div class="toggle-wrap nk-block-tools-toggle">
                                    <a href="{{ route('dekan.kegiatan.akademik.create') }}"
                                        class="btn btn-primary">Tambah</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="nk-block">
                        <div class="card">
                            <div class="card-inner">
                                <table class="table">
                                    <thead class="bg-info text-white">
                                        <tr>
                                            <th>No.</th>
                                            <th>Nama Kegiatan</th>
                                            <th>Descripsi</th>
                                            <th>Mulai</th>
                                            <th>Selesai</th>
                                            <th>Url</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($events as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}.</td>
                                            <td>{{ $item->title }}</td>
                                            <td>{{ $item->description }}</td>
                                            <td>{{ $item->start_date->format('H:i') }}</td>
                                            <td>{{ $item->end_date->format('H:i') }}</td>
                                            <td>
                                                @if($item->url)
                                                <a href="{{ $item->url }}" target="_blank"
                                                    class="text-blue-600 hover:text-blue-900">Link</a>
                                                @else
                                                -
                                                @endif
                                            </td>
                                            <td>
                                                @if($item->status == 'draft')
                                                <form action="{{ route('dekan.kegiatan.akademik.publish', $item->id) }}"
                                                    method="post">
                                                    @csrf
                                                    @method('POST')
                                                    <button class="btn btn-sm text-white bg-primary">
                                                        <i class="icon ni ni-upload-cloud"></i>Terbitkan</button>
                                                </form>
                                                @else
                                                Terpublikasi
                                                @endif
                                            </td>
                                        </tr>

                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-warning text-center"><span>Belum ada data
                                                    kegiatan.</span></td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-custom.sweet-alert />
    <div class="modal fade modal-xl" id="modalForm">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Form Tambah Kegiatan</h5>
                    <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('dekan.kegiatan.akademik.store') }}"
                        class="form-validate is-alter" id="facultyEventForm">
                        @csrf
                        <div class="form-group">
                            <label class="form-label" for="title">Judul Kegiatan</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="description">Uraian Kegiatan</label>
                            <div class="form-control-wrap">
                                <textarea class="form-control" id="description" name="description"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="start_date">Tanggal Mulai</label>
                            <div class="form-control-wrap">
                                <input type="datetime-local" class="form-control" id="start_date" name="start_date"
                                    step="1" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="end_date">Tanggal Selesai</label>
                            <div class="form-control-wrap">
                                <input type="datetime-local" class="form-control" id="end_date" name="end_date" step="1"
                                    required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="url">URL Kegiatan (Opsional)</label>
                            <div class="form-control-wrap">
                                <input type="url" class="form-control" id="url" name="url"
                                    placeholder="http://example.com/event-details">
                            </div>
                        </div>

                        {{-- Field Visibilitas --}}
                        <div class="form-group">
                            <label class="form-label" for="visibility">Visibilitas</label>
                            <div class="form-control-wrap">
                                <select id="visibility" name="visibility" class="form-control" required>
                                    <option value="faculty">Fakultas</option>
                                    <option value="department">Program Studi</option>
                                </select>
                            </div>
                        </div>

                        {{-- Prodi --}}
                        <div class="form-group" id="departmentGroup" style="display:none;">
                            <label class="form-label" for="department_id">Program Studi</label>
                            <div class="form-control-wrap">
                                <select id="department_id" name="department_id" class="form-control">
                                    @foreach($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Field Tahun Akademik --}}
                        <div class="form-group">
                            <label class="form-label" for="academic_year_id">Tahun Akademik</label>
                            <div class="form-control-wrap">
                                <select id="academic_year_id" name="academic_year_id" class="form-control" required>
                                    @foreach($academicYears as $academicYear)
                                    <option value="{{ $academicYear->id }}">{{ $academicYear->ta }}/{{
                                        $academicYear->semester }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-lg btn-primary">Tambah Kegiatan</button>
                        </div>
                    </form>
                </div>

                <div class="modal-footer bg-light">
                    <span class="sub-text">Modal Footer Text</span>
                </div>
            </div>
        </div>
    </div>

    @push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const visibilitySelect = document.getElementById('visibility');
            const departmentGroup = document.getElementById('departmentGroup');
            function toggleDepartmentGroup() {
                if (visibilitySelect.value === 'department') {
                    departmentGroup.style.display = 'block';
                } else {
                    departmentGroup.style.display = 'none';
                }
            }
            // Panggil saat halaman dimuat untuk mengatur tampilan awal
            toggleDepartmentGroup();
            // Panggil saat nilai select visibility berubah
            visibilitySelect.addEventListener('change', toggleDepartmentGroup);
        });
    </script>
    @endpush


</x-main-layout>
