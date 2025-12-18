<x-main-layout>
    @section('title', 'Daftar Dosen')
    @php
    $filePath = 'storage/template/template_dosen.xlsx';
    $hashedFile = md5($filePath);
    $userLevel = Auth::user()->employee->level ?? null;
    $isStaff = $userLevel === 'department';
    $isKtu = $userLevel === 'faculty';
    $routePrefix = $isKtu ? 'ktu' : 'staff';
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
                                <a href="{{ route('staff.dosen.create') }}" class="btn btn-primary">
                                    <em class="icon ni ni-plus"></em>
                                    <span>Add</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <div class="card-title">
                        <h5 class="nk-block-title">@yield('title'): {{ $departmentStaff->nama ?? 'Fakultas' }}</h5>
                    </div>
                    @if ($isStaff)
                    {{-- Form import dan tombol export hanya untuk Staff --}}
                    <div class="d-flex justify-content-start mb-3">
                        <form action="{{ route('staff.import.lecturer') }}" method="POST" enctype="multipart/form-data"
                            class="d-flex align-items-center">
                            @csrf
                            <input type="file" name="file" class="form-control me-2">
                            <button type="submit" class="btn btn-success">
                                <i class="icon ni ni-file me-1"></i> Import
                            </button>
                        </form>
                        <a class="ms-1 btn btn-warning" href="{{ url('/staf/lecturer/export') }}">
                            <i class="icon ni ni-download me-1"></i> Ekspor
                        </a>
                    </div>
                    @endif
                    <div class="row">
                        <table class="datatable-init nowrap table">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Nama</th>
                                    <th scope="col">NIDN</th>
                                    <th scope="col">Jenis Kelamin</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($dosen as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}.</td>
                                    <td>
                                        {{ $item->nama_dosen }}
                                        @if($item->user_id)
                                        <a class="badge bg-success" data-toggle="tooltip" data-placement="top"
                                            title="Akun dosen telah aktif">
                                            <i class="icon ni ni-check"></i>
                                        </a>
                                        @endif
                                    </td>
                                    <td>{{ $item->nidn }}</td>
                                    <td>{{ $item->gender }}</td>
                                    <td>
                                        {{-- Tombol Detail selalu terlihat untuk kedua peran --}}
                                        <a class="btn btn-sm btn-info"
                                            href="{{ route($routePrefix . '.dosen.show', $item->id) }}">
                                            <em class="icon ni ni-eye"></em>
                                        </a>

                                        @if ($isStaff)
                                        {{-- Tombol-tombol ini hanya untuk Staff --}}
                                        <a class="btn btn-sm btn-warning"
                                            href="{{ route($routePrefix . '.dosen.edit', $item->id) }}">
                                            <em class="icon ni ni-edit"></em>
                                        </a>
                                        <x-custom.delete-button
                                            :action-url="route($routePrefix . '.dosen.destroy', $item->id)" />

                                        @if ($item->user_id)
                                        <form action="{{ route($routePrefix . '.dosen.unassign', $item->id) }}"
                                            method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-warning"
                                                title="Non-Aktifkan Akun"
                                                onclick="return confirm('Yakin ingin menonaktifkan akun?')">
                                                <em class="icon ni ni-unlock"></em>
                                            </button>
                                        </form>
                                        @else
                                        <form action="{{ route($routePrefix . '.dosen.assign', $item->id) }}"
                                            method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">Aktifkan</button>
                                        </form>
                                        @endif
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5">Tidak ada data dosen yang ditemukan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-custom.sweet-alert />
</x-main-layout>
