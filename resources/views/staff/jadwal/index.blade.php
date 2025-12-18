<x-main-layout>
    @section('title', 'Daftar Jadwal Perkuliahan')
    @php
    $filePath = 'storage/template/template_schedule.xlsx';
    $hashedFile = md5($filePath);
    @endphp
    <div class="components-preview wide-lg mx-auto">
        <div class="nk-block nk-block-lg">
            <div class="nk-block-head">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h4 class="nk-block-title">@yield('title')</h4>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <div class="toggle-expand-content">
                                <a href="{{ route('staff.jadwal.create') }}" class="btn btn-success btn-sm">
                                    <em class="icon ni ni-plus"></em> Tambah
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <h5 class="overline-title title">Tahun Akademik: {{ $ta->ta
                        }}/{{ $ta->semester }}</h5>
                </div>
                <div class="card-inner">
                    @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                    @if (session('error'))
                    <div class="alert alert-danger">
                        <ul>
                            @if (is_array(session('error')))
                            @foreach (session('error') as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                            @else
                            <li>{{ session('error') }}</li>
                            @endif
                        </ul>
                    </div>
                    @endif
                    <form action="{{ route('staff.import.jadwal') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label>Upload File Excel</label>
                            <input type="file" name="file" class="form-control" accept=".xlsx,.xls" required>
                            @error('file')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Import</button>
                        <a class="btn btn-outline-primary" href="{{ asset('files/' . $hashedFile) }}"
                            onclick="event.preventDefault(); window.location.href='{{ asset($filePath) }}';">
                            <i class="icon ni ni-download me-1"></i> Download Template
                        </a>
                    </form>
                </div>
                <div class="card-inner">
                    <table class="datatable-init nowrap table">
                        <thead>
                            <tr>
                                <th scope="col">No.</th>
                                <th scope="col">Semester</th>
                                <th scope="col">Kode/Mata Kuliah</th>
                                <th scope="col">SKS</th>
                                <th scope="col">Dosen</th>
                                <th scope="col">Kelas</th>
                                <th scope="col">Hari</th>
                                <th scope="col">Waktu</th>
                                <th scope="col">Ruangan</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($items as $index => $item)
                            @php
                            $currentCourse = $item->course ?? $item->mkduCourse;
                            @endphp
                            <tr>
                                <td>{{ $index+1 }}.</td>
                                <td class="nk-tb-col">Semester {{ $currentCourse->semester_number }}</td>
                                <td class="nk-tb-col">{{ $currentCourse->code }} || {{ $currentCourse->name }}</td>
                                <td class="nk-tb-col">{{ $currentCourse->sks }}</td>
                                <td>
                                    @foreach ($item->lecturersInSchedule as $lecturer)
                                    <li>{{ $lecturer->nama_dosen }}</li>
                                    @endforeach
                                </td>
                                <td class="nk-tb-col">{{ $item->kelas->name }}</td>
                                <td class="nk-tb-col">{{ $item->hari }}</td>
                                <td class="nk-tb-col">{{ $item->waktu }}</td>
                                <td class="nk-tb-col">{{ $item->room->name }}</td>
                                <td width="9%">
                                    <form action="{{ route('staff.jadwal.destroy', $item->id) }}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <a class="btn btn-sm btn-warning"
                                            href="{{ route('staff.jadwal.edit', $item->id) }}">
                                            <em class="icon ni ni-edit"></em>
                                        </a>
                                        <button class="btn btn-danger btn-sm">
                                            <em class="icon ni ni-trash-fill"></em>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td>No data yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-main-layout>