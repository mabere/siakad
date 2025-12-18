<x-main-layout>
    @section('title', 'Manajemen Pertanyaan EDOM')

    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">@yield('title')</h3>
                <div class="nk-block-des text-soft">
                    <p>Kelola pertanyaan {{ $questionnaire->title }}</p>
                </div>
            </div>
            <div class="nk-block-head-content">
                <div class="toggle-wrap nk-block-tools-toggle">
                    <a href="{{ route('admin.edom.questions.create', $questionnaire) }}" class="btn btn-primary">
                        <em class="icon ni ni-plus"></em>
                        <span>Tambah Pertanyaan</span>
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
                            <th>Pertanyaan</th>
                            <th>Kategori</th>
                            <th>Bobot</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($questions as $question)
                        <tr>
                            <td>{{ Str::limit($question->question_text, 50) }}</td>
                            <td>{{ $question->categoryName->value ?? 'Uncategorized' }}</td>
                            <td>{{ $question->weight }}</td>
                            <td>{{ $question->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="dropdown">
                                    <a class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown">
                                        <em class="icon ni ni-more-h"></em>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <ul class="link-list-opt no-bdr">
                                            <li>
                                                <a href="{{ route('admin.edom.questions.edit', $question) }}">
                                                    <em class="icon ni ni-edit"></em>
                                                    <span>Edit</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#"
                                                    onclick="event.preventDefault(); 
                                                   document.getElementById('delete-form-{{ $question->id }}').submit();">
                                                    <em class="icon ni ni-trash"></em>
                                                    <span>Hapus</span>
                                                </a>
                                                <form id="delete-form-{{ $question->id }}"
                                                    action="{{ route('admin.edom.questions.destroy', $question) }}"
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