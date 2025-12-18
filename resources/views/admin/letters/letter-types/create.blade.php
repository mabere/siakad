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
                                    <a href="{{ route('admin.letter-types.index') }}"
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
                                <form action="{{ route('admin.letter-types.store') }}" method="POST"
                                    class="form-validate">
                                    @csrf
                                    <div class="row g-gs">
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="form-label" for="level">Level</label>
                                                <div class="form-control-wrap">
                                                    <select
                                                        class="form-control form-select @error('level') error @enderror"
                                                        id="level" name="level" required>
                                                        <option value="">--Pilih Level--</option>
                                                        <option value="university" {{ old('level')=='university'
                                                            ? 'selected' : '' }}>Universitas</option>
                                                        <option value="faculty" {{ old('level')=='faculty' ? 'selected'
                                                            : '' }}>Fakultas</option>
                                                        <option value="department" {{ old('level')=='department'
                                                            ? 'selected' : '' }}>Program Studi</option>
                                                    </select>
                                                    @error('level')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Pilihan Fakultas -->
                                        <div class="col-md-6 mb-3" id="faculty-selection" style="display: none;">
                                            <div class="form-group">
                                                <label for="faculty_id" class="form-label">Pilih Fakultas</label>
                                                <select name="faculty_id" id="faculty_id"
                                                    class="form-control form-select">
                                                    <option value="">-- Pilih Fakultas --</option>
                                                    @foreach($faculties as $faculty)
                                                    <option value="{{ $faculty->id }}">{{ $faculty->nama }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Pilihan Departemen -->
                                        <div class="col-md-6 mb-3" id="department-selection" style="display: none;">
                                            <div class="form-group">
                                                <label for="department_id" class="form-label">Pilih Departemen</label>
                                                <select name="department_id" id="department_id"
                                                    class="form-control form-select">
                                                    <option value="">-- Pilih Departemen --</option>
                                                    @foreach($departments as $department)
                                                    <option value="{{ $department->id }}">{{ $department->nama }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="name">Nama Tipe Surat</label>
                                                <div class="form-control-wrap">
                                                    <input type="text"
                                                        class="form-control @error('name') error @enderror" id="name"
                                                        name="name" value="{{ old('name') }}" required>
                                                    @error('name')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="code">Kode Surat</label>
                                                <div class="form-control-wrap">
                                                    <input type="text"
                                                        class="form-control @error('code') error @enderror" id="code"
                                                        name="code" value="{{ old('code') }}" required>
                                                    @error('code')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">

                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label class="form-label" for="template">Template Surat</label>
                                                <div class="form-control-wrap">
                                                    <input class="form-control @error('template') error @enderror"
                                                        id="template" name="template" required>
                                                    @error('template')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label">Field yang Dibutuhkan</label>
                                                <div class="form-note">Gunakan {field_name} untuk menandai tempat
                                                    field yang dibutuhkan</div>
                                                <div class="form-control-wrap" id="required_fields_container">
                                                    @if(old('required_fields'))
                                                    @foreach(old('required_fields') as $field)
                                                    <div class="form-control-wrap d-flex gx-3 mb-3">
                                                        <div class="flex-grow-1">
                                                            <input type="text" name="required_fields[]"
                                                                value="{{ $field }}" class="form-control" required>
                                                        </div>
                                                        <div class="btn-wrap">
                                                            <button type="button"
                                                                class="btn btn-icon btn-round btn-outline-danger"
                                                                onclick="this.closest('.form-control-wrap').remove()">
                                                                <em class="icon ni ni-trash"></em>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                    @else
                                                    <div class="form-control-wrap d-flex gx-3 mb-3">
                                                        <div class="flex-grow-1">
                                                            <input type="text" name="required_fields[]"
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
                                                    @endif
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
                                        <div class="col-12">
                                            <div class="form-group">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input"
                                                        name="needs_approval" id="needs_approval" value="1" {{
                                                        old('needs_approval') ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="needs_approval">Memerlukan
                                                        Persetujuan</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary">Simpan Tipe Surat</button>
                                            </div>
                                        </div>
                                    </div>
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

    <script>
        document.getElementById('level').addEventListener('change', function() {
        let level = this.value;
        document.getElementById('faculty-selection').style.display = level === 'faculty' ? 'block' : 'none';
        document.getElementById('department-selection').style.display = level === 'department' ? 'block' : 'none';
    });
    </script>

    @endpush
</x-main-layout>