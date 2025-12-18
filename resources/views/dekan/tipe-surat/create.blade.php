<x-main-layout>
    @section('title', 'Tambah Jenis Surat')

    <div class="nk-content">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">Tambah Tipe Surat</h3>
                                <div class="nk-block-des text-soft">
                                    <p>Form untuk menambahkan tipe surat baru.</p>
                                </div>
                            </div>
                            <div class="nk-block-head-content">
                                <div class="toggle-wrap nk-block-tools-toggle">
                                    <a href="{{ route('dekan.letter-types.index') }}"
                                        class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
                                        <em class="icon ni ni-arrow-left"></em>
                                        <span>Kembali</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="nk-block">
                        <div class="card">
                            @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                            <div class="card-inner">
                                <form action="{{ route('dekan.letter-types.store') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Nama Surat</label>
                                        <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                                            required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="code" class="form-label">Kode Surat</label>
                                        <input type="text" name="code" class="form-control" value="{{ old('code') }}"
                                            required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Template Surat</label>
                                        <input type="text" name="description" class="form-control"
                                            value="{{ old('description') }}" required>
                                    </div>

                                    <!-- Field yang Dibutuhkan -->
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="form-label">Field yang Dibutuhkan</label>
                                            <div class="form-note">Gunakan {field_name} untuk menandai tempat field yang
                                                dibutuhkan</div>
                                            <div class="form-control-wrap" id="required_fields_container">
                                                @forelse(old('required_fields', ['']) as $field)
                                                <div class="form-control-wrap d-flex gx-3 mb-3">
                                                    <div class="flex-grow-1">
                                                        <input type="text" name="required_fields[]" value="{{ $field }}"
                                                            class="form-control" required>
                                                    </div>
                                                    <div class="btn-wrap">
                                                        <button type="button"
                                                            class="btn btn-icon btn-round btn-outline-danger"
                                                            onclick="this.closest('.form-control-wrap').remove()">
                                                            <em class="icon ni ni-trash"></em>
                                                        </button>
                                                    </div>
                                                </div>
                                                @empty
                                                <div class="form-control-wrap d-flex gx-3 mb-3">
                                                    <div class="flex-grow-1">
                                                        <input type="text" name="required_fields[]" class="form-control"
                                                            required>
                                                    </div>
                                                    <div class="btn-wrap">
                                                        <button type="button"
                                                            class="btn btn-icon btn-round btn-outline-danger"
                                                            onclick="this.closest('.form-control-wrap').remove()">
                                                            <em class="icon ni ni-trash"></em>
                                                        </button>
                                                    </div>
                                                </div>
                                                @endforelse
                                            </div>
                                            <div class="form-control-wrap mt-3">
                                                <button type="button" class="btn btn-outline-primary"
                                                    onclick="addField()">
                                                    <em class="icon ni ni-plus"></em>
                                                    <span>Tambah Field</span>
                                                </button>
                                            </div>
                                            @error('required_fields')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Perlu Persetujuan -->
                                    <div class="mb-3">
                                        <label for="needs_approval" class="form-label">Perlu Persetujuan?</label>
                                        <input type="checkbox" name="needs_approval" value="1" {{ old('needs_approval',
                                            1) ? 'checked' : '' }}>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function addField() {
        const container = document.getElementById('required_fields_container');
        const wrapper = document.createElement('div');
        wrapper.className = 'form-control-wrap d-flex gx-3 mb-3';
        wrapper.innerHTML = `
            <div class="flex-grow-1">
                <input type="text" name="required_fields[]" class="form-control" required>
            </div>
            <div class="btn-wrap">
                <button type="button" class="btn btn-icon btn-round btn-outline-danger" onclick="this.closest('.form-control-wrap').remove()">
                    <em class="icon ni ni-trash"></em>
                </button>
            </div>
        `;
        container.appendChild(wrapper);
    }
    </script>
    @endpush

</x-main-layout>