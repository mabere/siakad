<x-main-layout>
    @section('title', 'Edit Kategori EDOM')

    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">@yield('title')</h3>
                <div class="nk-block-des text-soft">
                    <p>Edit kategori untuk kuesioner EDOM</p>
                </div>
            </div>
        </div>
    </div>

    <div class="nk-block">
        <div class="card card-bordered">
            <div class="card-inner">
                <form action="{{ route('admin.edom.categories.update', $category) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label class="form-label" for="key">Kode Kategori</label>
                        <div class="form-control-wrap">
                            <input type="text" class="form-control" id="key" name="key" value="{{ $category->key }}"
                                required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="value">Nama Kategori</label>
                        <div class="form-control-wrap">
                            <input type="text" class="form-control" id="value" name="value"
                                value="{{ $category->value }}" required>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">Update Kategori</button>
                        <a href="{{ route('admin.edom.categories.index') }}" class="btn btn-outline-secondary">
                            Batal
                        </a>
                    </div>
                </form>
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