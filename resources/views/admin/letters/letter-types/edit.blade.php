<x-main-layout>
    @section('title', 'Edit Jenis Surat')
    <div class="nk-content">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">@yield('title')</h3>
                                <div class="nk-block-des text-soft">
                                    <p>Form untuk mengubah tipe surat yang sudah ada.</p>
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
                                <form action="{{ route('admin.letter-types.update', $letterType) }}" method="POST"
                                    class="form-validate">
                                    @csrf
                                    @method('PUT')
                                    <div class="row g-gs">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="name">Nama Tipe Surat</label>
                                                <div class="form-control-wrap">
                                                    <input type="text"
                                                        class="form-control @error('name') error @enderror" id="name"
                                                        name="name" value="{{ old('name', $letterType->name) }}"
                                                        required>
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
                                                        name="code" value="{{ old('code', $letterType->code) }}"
                                                        required>
                                                    @error('code')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="level">Level Surat</label>
                                                <div class="form-control-wrap">
                                                    <select class="form-control @error('level') error @enderror"
                                                        id="level" name="level" required
                                                        onchange="toggleUnitSelection()">
                                                        <option value="">--Pilih Level--</option>
                                                        <option value="university" {{ old('level', $letterType->level)
                                                            == 'university' ? 'selected' : '' }}>Universitas</option>
                                                        <option value="faculty" {{ old('level', $letterType->level) ==
                                                            'faculty' ? 'selected' : '' }}>Fakultas</option>
                                                        <option value="department" {{ old('level', $letterType->level)
                                                            == 'department' ? 'selected' : '' }}>Program Studi</option>
                                                    </select>
                                                    @error('level')
                                                    <span class="invalid-feedback"><strong>{{ $message
                                                            }}</strong></span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6" id="faculty_selection" style="display: none;">
                                            <div class="form-group">
                                                <label class="form-label" for="faculty_id">Fakultas</label>
                                                <div class="form-control-wrap">
                                                    <select class="form-control @error('faculty_id') error @enderror"
                                                        id="faculty_id" name="faculty_id">
                                                        <option value="">--Pilih Fakultas--</option>
                                                        @foreach($faculties as $faculty)
                                                        <option value="{{ $faculty->id }}" {{ old('faculty_id',
                                                            $letterType->faculty_id) == $faculty->id ? 'selected' : ''
                                                            }}>
                                                            {{ $faculty->nama }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                    @error('faculty_id')
                                                    <span class="invalid-feedback"><strong>{{ $message
                                                            }}</strong></span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Pilih Departemen (Hanya jika Level Departemen) -->
                                        <div class="col-md-6" id="department_selection" style="display: none;">
                                            <div class="form-group">
                                                <label class="form-label" for="department_id">Program Studi</label>
                                                <div class="form-control-wrap">
                                                    <select class="form-control @error('department_id') error @enderror"
                                                        id="department_id" name="department_id">
                                                        <option value="">--Pilih Program Studi--</option>
                                                        @foreach($departments as $department)
                                                        <option value="{{ $department->id }}" {{ old('department_id',
                                                            $letterType->department_id) == $department->id ? 'selected'
                                                            : '' }}>
                                                            {{ $department->nama }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                    @error('department_id')
                                                    <span class="invalid-feedback"><strong>{{ $message
                                                            }}</strong></span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="template">Template Surat</label>
                                                <div class="form-control-wrap">
                                                    <input class="form-control @error('template') error @enderror"
                                                        id="template" name="template" rows="5" required
                                                        value="{{ old('template', $letterType->template) }}">
                                                    <input class="form-control @error('template') error @enderror"
                                                        id="template" name="template" rows="5" required
                                                        value="{{ old('description', isset($letterType) ? ($letterType->letterTypeAssignments->first()->template_name ?? '') : '') }}">
                                                    <div class="form-note">Gunakan {field_name} untuk menandai tempat
                                                        field yang dibutuhkan</div>
                                                    @error('template')
                                                    <span class="invalid-feedback"><strong>{{ $message
                                                            }}</strong></span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label">Field yang Dibutuhkan</label>
                                                <div class="form-control-wrap" id="required_fields_container">
                                                    @php
                                                    $fields = old('required_fields', $letterType->required_fields ??
                                                    []);
                                                    if (empty($fields)) {
                                                    $fields = [''];
                                                    }
                                                    @endphp
                                                    @foreach ($fields as $field)
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
                                                </div>
                                                <div class="form-control-wrap mt-3">
                                                    <button type="button" class="btn btn-outline-primary"
                                                        onclick="addField()">
                                                        <em class="icon ni ni-plus"></em>
                                                        <span>Tambah Field</span>
                                                    </button>
                                                </div>
                                                @error('required_fields')
                                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input"
                                                        name="needs_approval" id="needs_approval" value="1" {{
                                                        old('needs_approval', $letterType->needs_approval) ? 'checked' :
                                                    '' }}>
                                                    <label class="custom-control-label" for="needs_approval">Memerlukan
                                                        Persetujuan</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
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
        function toggleUnitSelection() {
            let level = document.getElementById('level').value;
            document.getElementById('faculty_selection').style.display = (level === 'faculty') ? '' : 'none';
            document.getElementById('department_selection').style.display = (level === 'department') ? '' : 'none';
        }
    </script>
    @endpush
</x-main-layout>