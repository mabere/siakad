<x-main-layout>
    @section('title', 'Tambah Unit Pelaksana Teknis')
    <div class="nk-block-head">
        <div class="nk-block-head-content">
            <h4 class="title nk-block-title">@yield('title')</h4>
            <div class="nk-block-des">

            </div>
        </div>
    </div>

    <div class="card card-bordered card-preview">
        <div class="card-inner">
            <div class="preview-block">
                <span class="preview-title-lg overline-title">Form @yield('title')</span>
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <form action="{{ route('admin.units.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <!-- Nama Unit -->
                    <div class="form-group row mt-3">
                        <label for="nama" class="col-sm-3 col-form-label text-end">Nama Unit</label>
                        <div class="col-sm-9">
                            <input type="text" name="nama" id="nama"
                                class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}"
                                placeholder="Masukkan Nama Unit">
                            @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Level Unit -->
                    <div class="form-group row mt-3">
                        <label for="level" class="col-sm-3 col-form-label text-end">Level Unit</label>
                        <div class="col-sm-9">
                            <select name="level" id="level"
                                class="form-control form-select @error('level') is-invalid @enderror">
                                <option value="">-- Pilih Level --</option>
                                <option value="Universitas" {{ old('level')=='Universitas' ? 'selected' : '' }}>
                                    Universitas</option>
                                <option value="Fakultas" {{ old('level')=='Fakultas' ? 'selected' : '' }}>Fakultas
                                </option>
                                <option value="Program Studi" {{ old('level')=='Program Studi' ? 'selected' : '' }}>
                                    Program Studi</option>
                                <option value="Lainnya" {{ old('level')=='Lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            @error('level')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Kode Unit -->
                    <div class="form-group row mt-3">
                        <label for="code" class="col-sm-3 col-form-label text-end">Kode Unit</label>
                        <div class="col-sm-9">
                            <input type="text" name="code" id="code"
                                class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}"
                                placeholder="Masukkan Kode Unit">
                            @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Pilihan Sumber Kepala Unit -->
                    <div class="form-group row mt-3">
                        <label for="kepala_source" class="col-sm-3 col-form-label text-end">Sumber Kepala Unit</label>
                        <div class="col-sm-9">
                            <select name="kepala_source" id="kepala_source"
                                class="form-control form-select @error('kepala_source') is-invalid @enderror">
                                <option value="lecturer" {{ old('kepala_source', 'lecturer' )=='lecturer' ? 'selected'
                                    : '' }}>Dari Data Dosen</option>
                                <option value="manual" {{ old('kepala_source')=='manual' ? 'selected' : '' }}>Input
                                    Manual</option>
                            </select>
                            @error('kepala_source')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Dropdown untuk Kepala Unit dari Data Dosen -->
                    <div class="form-group row mt-3" id="lecturer_select_div"
                        style="display: {{ old('kepala_source', 'lecturer') == 'lecturer' }};">
                        <label for="lecturer_id" class="col-sm-3 col-form-label text-end">Pilih Kepala Unit
                            (Dosen)</label>
                        <div class="col-sm-9">
                            <select name="lecturer_id" id="lecturer_id"
                                class="form-control form-select @error('lecturer_id') is-invalid @enderror">
                                <option value="">-- Pilih Dosen --</option>
                                @foreach($lecturers as $lecturer)
                                <option value="{{ $lecturer->id }}" {{ old('lecturer_id')==$lecturer->id ?
                                    'selected' : '' }}>
                                    {{ $lecturer->nama_dosen }}
                                </option>
                                @endforeach
                            </select>
                            @error('lecturer_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Input manual untuk Kepala Unit -->
                    <div class="form-group row mt-3" id="manual_input_div"
                        style="display: {{ old('kepala_source')=='manual' ? 'block' : 'none' }};">
                        <label for="kepala_unit" class="col-sm-3 col-form-label text-end">Kepala Unit</label>
                        <div class="col-sm-9">
                            <input type="text" name="kepala_unit" id="kepala_unit"
                                class="form-control @error('kepala_unit') is-invalid @enderror"
                                value="{{ old('kepala_unit') }}" placeholder="Masukkan Nama Kepala Unit">
                            @error('kepala_unit')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Input manual untuk NIP Kepala (jika manual) -->
                    <div class="form-group row mt-3" id="manual_input_nip_div"
                        style="display: {{ old('kepala_source')=='manual' ? 'block' : 'none' }};">
                        <label for="nip_kepala" class="col-sm-3 col-form-label text-end">NIP Kepala</label>
                        <div class="col-sm-9">
                            <input type="text" name="nip_kepala" id="nip_kepala"
                                class="form-control @error('nip_kepala') is-invalid @enderror"
                                value="{{ old('nip_kepala') }}" placeholder="Masukkan NIP kepala">
                            @error('nip_kepala')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Field tambahan: Email -->
                    <div class="form-group row mt-3">
                        <label for="email" class="col-sm-3 col-form-label text-end">Email</label>
                        <div class="col-sm-9">
                            <input type="email" name="email" id="email"
                                class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"
                                placeholder="Masukkan Email">
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Field tambahan: Telepon -->
                    <div class="form-group row mt-3">
                        <label for="phone" class="col-sm-3 col-form-label text-end">Telepon</label>
                        <div class="col-sm-9">
                            <input type="text" name="phone" id="phone"
                                class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}"
                                placeholder="Masukkan Nomor Telepon">
                            @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Field tambahan: Signature -->
                    <div class="form-group row mt-3">
                        <label for="signature_path" class="col-sm-3 col-form-label text-end">Signature</label>
                        <div class="col-sm-9">
                            <input type="file" name="signature_path" id="signature_path"
                                class="form-control-file @error('signature_path') is-invalid @enderror">
                            @error('signature_path')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Tombol Submit -->
                    <div class="form-group row mt-4">
                        <div class="col-sm-9 offset-sm-3">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
        $('#kepala_source').on('change', function() {
            var source = $(this).val();
            if (source === 'manual') {
                $('#lecturer_select_div').hide();
                $('#manual_input_div').show();
                $('#manual_input_nip_div').show();
            } else {
                $('#manual_input_div').hide();
                $('#manual_input_nip_div').hide();
                $('#lecturer_select_div').show();
            }
        });
    });
    </script>
    @endpush
</x-main-layout>