<x-main-layout>
    @section('title','Edit Unit Pelaksana Teknis')
    <div class="nk-block-head">
        <div class="nk-block-head-content">
            <h4 class="title nk-block-title">@yield('title')</h4>
            <div class="nk-block-des">

            </div>
        </div>
    </div>

    <div class="card card-bordered card-preview">
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <div class="card-inner">
            <div class="preview-block">
                <form action="{{ route('admin.units.update', $unit->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-group row">
                        <label for="parent_id" class="col-sm-3 col-form-label text-end">Unit Induk</label>
                        <div class="col-sm-9">
                            <select name="parent_id" id="parent_id"
                                class="form-control form-select @error('parent_id') is-invalid @enderror">
                                <option value="">-- Pilih Unit Induk (Opsional) --</option>
                                @if(isset($parentUnits))
                                @foreach($parentUnits as $unitParent)
                                <option value="{{ $unitParent->id }}" {{ old('parent_id', $unit->parent_id) ==
                                    $unitParent->id ? 'selected' : '' }}>
                                    {{ $unitParent->nama }}
                                </option>
                                @endforeach
                                @endif
                            </select>
                            @error('parent_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row mt-3">
                        <label for="nama" class="col-sm-3 col-form-label text-end">Nama Unit/Lembaga</label>
                        <div class="col-sm-9">
                            <input type="text" name="nama" id="nama"
                                class="form-control @error('nama') is-invalid @enderror"
                                value="{{ old('nama', $unit->nama) }}" placeholder="Masukkan nama unit/lembaga">
                            @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row mt-3">
                        <label for="code" class="col-sm-3 col-form-label text-end">Kode Unit/Lembaga</label>
                        <div class="col-sm-9">
                            <input type="text" name="code" id="code"
                                class="form-control @error('code') is-invalid @enderror"
                                value="{{ old('code', $unit->code) }}" placeholder="Masukkan nama unit/lembaga">
                            @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row mt-3">
                        <label for="kepala_source" class="col-sm-3 col-form-label text-end">Sumber Kepala</label>
                        <div class="col-sm-9">
                            <select name="kepala_source" id="kepala_source"
                                class="form-control form-select @error('kepala_source') is-invalid @enderror">
                                <option value="lecturer" {{ old('kepala_source', $unit->kepala_source) == 'lecturer' ?
                                    'selected' : '' }}>Dosen</option>
                                <option value="manual" {{ old('kepala_source', $unit->kepala_source) == 'manual' ?
                                    'selected' : '' }}>Manual</option>
                            </select>
                            @error('kepala_source')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>


                    <div class="form-group row mt-3" id="lecturer_div"
                        style="display: {{ old('kepala_source', $unit->kepala_source) == 'lecturer' ? 'block' : 'none' }}">
                        <label for="lecturer_id" class="col-sm-3 col-form-label text-end">Kepala Unit (Dosen)</label>
                        <div class="col-sm-9">
                            <select name="lecturer_id" id="lecturer_id"
                                class="form-control form-select @error('lecturer_id') is-invalid @enderror">
                                <option value="">-- Pilih Dosen --</option>
                                @if(isset($lecturers))
                                @foreach($lecturers as $lecturer)
                                <option value="{{ $lecturer->id }}" {{ old('lecturer_id', $unit->lecturer_id) ==
                                    $lecturer->id ? 'selected' : '' }}>
                                    {{ $lecturer->nama_dosen }}
                                </option>
                                @endforeach
                                @endif
                            </select>
                            @error('lecturer_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row mt-3" id="manual_div"
                        style="display: {{ old('kepala_source', $unit->kepala_source) == 'manual' ? '' : '' }}">
                        <label for="kepala_unit" class="col-sm-3 col-form-label text-end">Kepala Unit</label>
                        <div class="col-sm-9">
                            <input type="text" name="kepala_unit" id="kepala_unit"
                                class="form-control @error('kepala_unit') is-invalid @enderror"
                                value="{{ old('kepala_unit', $unit->kepala_unit) }}"
                                placeholder="Masukkan nama kepala unit">
                            @error('kepala_unit')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>


                    <div class="form-group row mt-3">
                        <label for="nip_kepala" class="col-sm-3 col-form-label text-end">NIP Kepala</label>
                        <div class="col-sm-9">
                            <input type="text" name="nip_kepala" id="nip_kepala"
                                class="form-control @error('nip_kepala') is-invalid @enderror"
                                value="{{ old('nip_kepala', $unit->nip_kepala) }}" placeholder="Masukkan NIP kepala">
                            @error('nip_kepala')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row mt-3">
                        <label for="email" class="col-sm-3 col-form-label text-end">Email</label>
                        <div class="col-sm-9">
                            <input type="email" name="email" id="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $unit->email) }}" placeholder="Masukkan email unit/lembaga">
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row mt-3">
                        <label for="phone" class="col-sm-3 col-form-label text-end">Telepon</label>
                        <div class="col-sm-9">
                            <input type="text" name="phone" id="phone"
                                class="form-control @error('phone') is-invalid @enderror"
                                value="{{ old('phone', $unit->phone) }}">
                            @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row mt-3">
                        <label for="level" class="col-sm-3 col-form-label text-end">Level Unit/Lembaga</label>
                        <div class="col-sm-9">
                            <select name="level" id="level"
                                class="form-control form-select @error('level') is-invalid @enderror">
                                <option value="">Pilih Level</option>
                                <option value="Universitas" {{ old('level', $unit->level) == 'Universitas' ? 'selected'
                                    : '' }}>Universitas</option>
                                <option value="Fakultas" {{ old('level', $unit->level) == 'Fakultas' ? 'selected' : ''
                                    }}>Fakultas</option>
                                <option value="Program Studi" {{ old('level', $unit->level) == 'Program Studi' ?
                                    'selected' :
                                    '' }}>Prodi</option>
                                <option value="Lainnya" {{ old('level', $unit->level) == 'Lainnya' ? 'selected' : ''
                                    }}>Lainnya</option>
                            </select>
                            @error('level')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row mt-3">
                        <label for="signature_path" class="col-sm-3 col-form-label text-end">Tanda Tangan</label>
                        <div class="col-sm-9">
                            <input type="file" name="signature_path" id="signature_path"
                                class="form-control-file @error('signature_path') is-invalid @enderror">
                            @error('signature_path')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($unit->signature_path)
                            <div class="mt-2">
                                <img src="{{ asset('storage/images/staff/' . $unit->signature_path) }}"
                                    alt="Tanda Tangan" width="150">
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row mt-3">
                        <label for="active" class="col-sm-3 col-form-label text-end">Status Aktif</label>
                        <div class="col-sm-9">
                            <div class="form-check">
                                <input type="hidden" name="active" value="0">
                                <input type="checkbox" name="active" id="active" value="1" class="form-check-input" {{
                                    old('active', $unit->active) == 1 ? 'checked' : '' }}>
                                <label class="form-check-label" for="active">Aktif</label>
                            </div>
                            @error('active')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row mt-4">
                        <div class="col-sm-9 offset-sm-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="icon ni ni-send"></i> Update
                            </button>
                        </div>
                    </div>

                    <script>
                        const kepalaSourceSelect = document.getElementById('kepala_source');
                        const lecturerDiv = document.getElementById('lecturer_div');
                        const manualDiv = document.getElementById('manual_div');
                    
                        kepalaSourceSelect.addEventListener('change', function() {
                            if (this.value === 'lecturer') {
                                lecturerDiv.style.display = 'block';
                                manualDiv.style.display = 'none';
                            } else if (this.value === 'manual') {
                                lecturerDiv.style.display = 'none';
                                manualDiv.style.display = 'block';
                            }
                        });
                    </script>
                </form>
            </div>
        </div>
    </div>

</x-main-layout>