<x-main-layout>
    @section('title', 'Edit Tipe Surat')
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

                                <form action="{{ route('dekan.letter-types.update', $letterType->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="mb-3">
                                        <label for="name" class="form-label">Nama Surat</label>
                                        <input type="text" name="name" class="form-control"
                                            value="{{ old('name', $letterType->name) }}" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="code" class="form-label">Kode Surat</label>
                                        <input type="text" name="code" class="form-control"
                                            value="{{ old('code', $letterType->code) }}" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Template Surat</label>
                                        <input type="text" name="description" class="form-control"
                                            value="{{ old('description', isset($letterType) ? ($letterType->letterTypeAssignments->first()->template_name ?? '') : '') }}"
                                            required>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="form-label">Field yang Dibutuhkan</label>
                                            <div class="form-control-wrap" id="required_fields_container">
                                                @foreach(old('required_fields', $letterType->required_fields) as $field)
                                                <div class="form-control-wrap d-flex gx-3 mb-3">
                                                    <div class="flex-grow-1">
                                                        <input type="text" name="required_fields[]" value="{{ $field }}"
                                                            class="form-control" required>
                                                    </div>
                                                    <div class="btn-wrap">
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            onclick="this.closest('.form-control-wrap').remove()">Hapus</button>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                            <button type="button" class="btn btn-outline-primary mt-3"
                                                onclick="addField()">Tambah Field</button>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="needs_approval" class="form-label">Perlu Persetujuan?</label>
                                        <input type="checkbox" name="needs_approval" value="1" {{ old('needs_approval',
                                            $letterType->needs_approval) ? 'checked' : '' }}>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                    <a href="{{ route('dekan.letter-types.index') }}"
                                        class="btn btn-secondary">Batal</a>
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
                    <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.form-control-wrap').remove()">Hapus</button>
                </div>
            `;
            container.appendChild(wrapper);
        }
    </script>
    @endpush
</x-main-layout>