<x-main-layout>
    @section('title', 'Daftar Mahasiswa')
    @php
    $filePath = 'storage/template/template_mahasiswa.xlsx';
    $hashedFile = md5($filePath);
    $userLevel = Auth::user()->employee->level ?? null;
    $isStaff = $userLevel === 'department';
    $routePrefix = $isStaff ? 'staff' : 'ktu';
    @endphp

    <div class="components-preview wide-lg mx-auto">
        <div class="nk-block nk-block-lg">
            <div class="nk-block-head">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h4 class="nk-block-title">@yield('title')</h4>
                    </div>
                    @if ($isStaff)
                    {{-- Tombol-tombol ini hanya untuk Staff --}}
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <div class="toggle-expand-content">
                                <a class="btn btn-outline-primary" href="{{ asset('files/' . $hashedFile) }}"
                                    onclick="event.preventDefault(); window.location.href='{{ asset($filePath) }}';">
                                    <i class="icon ni ni-download me-1"></i> Download Template
                                </a>
                                <a href="{{ route('staff.mahasiswa.create') }}" class="btn btn-primary">
                                    <em class="icon ni ni-plus"></em>
                                    <span>Add</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card-header bg-primary p-2">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto">
                        <h6 class="m-0 font-weight-bold px-3 text-white">@yield('title')</h6>
                    </div>
                    @if ($isStaff)
                    <div class="col-auto d-flex">
                        <button type="submit" form="bulkAssignForm" class="btn btn-info btn-sm d-none"
                            id="bulk-activate-btn" onclick="return confirm('Aktifkan mahasiswa terpilih?')">
                            <i class="icon ni ni-check"></i> <span class="ms-1">Aktifkan</span>
                        </button>
                    </div>
                    @endif
                </div>
            </div>

            @if ($isStaff)
            <form action="{{ route('staff.mahasiswa.assign-multiple') }}" method="POST" id="bulkAssignForm">
                @csrf
            </form>
            @endif

            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    @if ($isStaff)
                    {{-- Form import dan tombol export hanya untuk Staff --}}
                    <div class="d-flex justify-content-start mb-3">
                        <form action="{{ route('staff.import.students') }}" method="POST" enctype="multipart/form-data"
                            class="d-flex align-items-center">
                            @csrf
                            <input type="file" name="file" class="form-control me-2">
                            <button type="submit" class="btn btn-success">
                                <i class="icon ni ni-file me-1"></i> Import
                            </button>
                        </form>
                        <a class="ms-1 btn btn-warning" href="{{ route('admin.mhs.export') }}">
                            <i class="icon ni ni-download me-1"></i> Ekspor
                        </a>
                    </div>
                    @endif
                    <br>
                    <table class="datatable-init nowrap table">
                        <thead class="table-dark">
                            <tr>
                                @if ($isStaff)
                                <th style="width: 40px;"><input type="checkbox" id="select-all" form="bulkAssignForm">
                                </th>
                                @endif
                                <th scope="col">No</th>
                                <th scope="col">Nama</th>
                                <th scope="col">NIM</th>
                                <th scope="col">Jenis Kelamin</th>
                                <th scope="col">Angkatan</th>
                                <th scope="col">Dosen PA</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            @forelse ($mhs as $item)
                            <tr>
                                @if ($isStaff)
                                <td>
                                    @if(!$item->user_id)
                                    <input type="checkbox" form="bulkAssignForm" name="selected_students[]"
                                        value="{{ $item->id }}">
                                    @endif
                                </td>
                                @endif
                                <td>{{ $loop->iteration }}.</td>
                                <td>{{ $item->nama_mhs }}
                                    @if ($item->user_id)
                                    <span class="badge bg-success">Aktif</span>
                                    @endif
                                </td>
                                <td>{{ $item->nim }}</td>
                                <td>{{ $item->gender }}</td>
                                <td>{{ $item->kelas->angkatan ?? '-'}}</td>
                                <td>{{ $item->advisor ? $item->advisor->nama_dosen : 'Belum ada PA' }}</td>
                                <td>
                                    {{-- Tombol Detail selalu terlihat untuk kedua peran --}}
                                    <a class="btn btn-sm btn-info"
                                        href="{{ route($routePrefix . '.mahasiswa.show.detail', $item->id) }}">
                                        <em class="icon ni ni-eye"></em>
                                    </a>

                                    @if ($isStaff)
                                    {{-- Tombol-tombol ini hanya untuk Staff --}}
                                    <a class="btn btn-sm btn-warning"
                                        href="{{ route($routePrefix . '.mahasiswa.edit', $item->id) }}">
                                        <em class="icon ni ni-edit"></em>
                                    </a>
                                    <x-custom.delete-button
                                        :action-url="route($routePrefix . '.mahasiswa.destroy', $item->id)" />

                                    @if($item->user_id === NULL)
                                    <form action="{{ route($routePrefix . '.mahasiswa.assign', $item->id) }}"
                                        method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-secondary" title="Aktifkan Akun">
                                            <i class="icon ni ni-lock"></i>
                                        </button>
                                    </form>
                                    @else
                                    <form action="{{ route($routePrefix . '.mahasiswa.unassign', $item->id) }}"
                                        method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Non-Aktifkan Akun"
                                            onclick="return confirm('Yakin ingin menonaktifkan akun?')">
                                            <em class="icon ni ni-unlock"></em>
                                        </button>
                                    </form>
                                    @endif
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ $isStaff ? '8' : '7' }}">Tidak ada data mahasiswa yang ditemukan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <x-custom.sweet-alert />

    @if ($isStaff)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Select all checkbox functionality
            document.getElementById('select-all').addEventListener('change', function(e) {
                const checkboxes = document.querySelectorAll('input[name="selected_students[]"][form="bulkAssignForm"]');
                checkboxes.forEach(checkbox => checkbox.checked = e.target.checked);
                toggleBulkButton();
            });

            // Toggle bulk activate button visibility
            document.querySelectorAll('input[name="selected_students[]"][form="bulkAssignForm"]').forEach(checkbox => {
                checkbox.addEventListener('change', toggleBulkButton);
            });

            function toggleBulkButton() {
                const checked = document.querySelectorAll(
                    'input[name="selected_students[]"][form="bulkAssignForm"]:checked'
                ).length > 0;

                const bulkBtn = document.getElementById('bulk-activate-btn');
                if (bulkBtn) {
                    bulkBtn.classList.toggle('d-none', !checked);
                }
            }
        });
    </script>
    @endif

</x-main-layout>
