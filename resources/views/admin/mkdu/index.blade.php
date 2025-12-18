<x-main-layout>
    @section('title', 'Daftar Mata Kuliah MKDU')

    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Daftar Mata Kuliah MKDU</h3>
            </div>
            <div class="nk-block-head-content">
                <a href="{{ route('mkdu.create') }}" class="btn btn-primary">
                    <em class="icon ni ni-plus"></em> Tambah MKDU
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
                @if($errors->has('error'))
                <div class="alert alert-danger alert-icon">
                    <em class="icon ni ni-cross-circle"></em>
                    {{ $errors->first('error') }}
                </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>SKS</th>
                                <th>Semester</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mkduCourses as $mkduCourse)
                            <tr>
                                <td>{{ $mkduCourse->code }}</td>
                                <td>{{ $mkduCourse->name }}</td>
                                <td>{{ $mkduCourse->sks }}</td>
                                <td>@forelse($mkduCourse->curricula as $curriculum)
                                    {{ $curriculum->name }} (Semester: {{ $curriculum->pivot->semester_number }})<br>
                                    @empty
                                    <span class="text-muted">Belum ada relasi kurikulum.</span>
                                    @endforelse
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <a href="#" class="btn btn-sm btn-icon btn-trigger" data-bs-toggle="dropdown">
                                            <em class="icon ni ni-more-h"></em>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <ul class="link-list-opt no-bdr">
                                                <li>
                                                    <a href="{{ route('mkdu.edit', $mkduCourse) }}">
                                                        <em class="icon ni ni-edit"></em> Edit
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                        <li>
                                            <a href="javascript:void(0)"
                                                onclick="confirmDelete(event, '{{ route('mkdu.destroy', $mkduCourse) }}')">
                                                <em class="icon ni ni-trash"></em> Hapus
                                            </a>
                                        </li>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada mata kuliah MKDU.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        function confirmDelete(event, url) {
            event.preventDefault(); // Mencegah aksi default link
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus mata kuliah MKDU ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    form.innerHTML = `
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="DELETE">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
</x-main-layout>
