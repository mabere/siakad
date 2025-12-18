<x-main-layout>
    @section('title', 'Manajemen Kategori EDOM')
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">@yield('title')</h3>
                <div class="nk-block-des text-soft">
                    <p>Kelola kategori kuesioner EDOM</p>
                </div>
            </div>
            <div class="nk-block-head-content">
                <div class="toggle-wrap nk-block-tools-toggle">
                    <a href="{{ route('admin.edom.categories.create') }}" class="btn btn-primary">
                        <em class="icon ni ni-plus"></em>
                        <span>Tambah Kategori</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="nk-block">
        <div class="card card-bordered card-preview">
            <div class="card-inner">
                <table class="datatable-init table">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                        <tr>
                            <td>{{ $loop->iteration }}.</td>
                            <td>{{ $category->key }}</td>
                            <td>{{ $category->value }}</td>
                            <td>{{ $category->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="dropdown">
                                    <a class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown">
                                        <em class="icon ni ni-more-h"></em>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <ul class="link-list-opt no-bdr">
                                            <li>
                                                <a href="{{ route('admin.edom.categories.edit', $category) }}">
                                                    <em class="icon ni ni-edit"></em>
                                                    <span>Edit</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#"
                                                    onclick="event.preventDefault(); 
                                                   document.getElementById('delete-form-{{ $category->id }}').submit();">
                                                    <em class="icon ni ni-trash"></em>
                                                    <span>Hapus</span>
                                                </a>
                                                <form id="delete-form-{{ $category->id }}"
                                                    action="{{ route('admin.edom.categories.destroy', $category) }}"
                                                    method="POST" class="d-none">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Check for success/error messages in session
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 1500
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '{{ session('error') }}'
            });
        @endif
    </script>
    @endpush
</x-main-layout>