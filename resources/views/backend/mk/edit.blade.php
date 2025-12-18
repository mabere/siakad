<x-main-layout>
    @section('title', 'Edit Mata Kuliah')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form class="form-horizontal" method="POST" action="{{ route('admin.mk.update', $course->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <h4 class="card-title">Edit Mata Kuliah - {{ $course->name }}</h4>
                        <p><strong>Department:</strong> {{ $course->department->nama }}</p>

                        <!-- Nama Mata Kuliah -->
                        <div class="form-group row">
                            <label for="mk" class="col-sm-3 text-end control-label col-form-label">Nama Mata
                                Kuliah</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('name') is-invalid @enderror" name="mk"
                                    value="{{ old('name', $course->name) }}">
                                @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- Kode Mata Kuliah -->
                        <div class="form-group row">
                            <label for="code" class="col-sm-3 text-end control-label col-form-label">Kode Mata
                                Kuliah</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('code') is-invalid @enderror" name="code"
                                    value="{{ old('code', $course->code) }}">
                                @error('code') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- SKS Mata Kuliah -->
                        <div class="form-group row">
                            <label for="sks" class="col-sm-3 text-end control-label col-form-label">SKS Mata
                                Kuliah</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control @error('sks') is-invalid @enderror" name="sks"
                                    value="{{ old('sks', $course->sks) }}">
                                @error('sks') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- Tingkat -->
                        <div class="form-group row">
                            <label for="smt" class="col-sm-3 text-end control-label col-form-label">Tingkat</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control @error('smt') is-invalid @enderror" name="smt"
                                    value="{{ old('smt', $course->smt) }}">
                                @error('smt') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- Semester -->
                        <div class="form-group row">
                            <label for="semester"
                                class="col-sm-3 text-end control-label col-form-label">Semester</label>
                            <div class="col-sm-9">
                                <select
                                    class="form-control select2 form-select shadow-none @error('semester') is-invalid @enderror"
                                    id="semester" name="semester">
                                    <option value="">Select</option>
                                    <option value="Ganjil" {{ old('semester', $course->semester) == 'Ganjil' ?
                                        'selected' : '' }}>Ganjil</option>
                                    <option value="Genap" {{ old('semester', $course->semester) == 'Genap' ? 'selected'
                                        : '' }}>Genap</option>
                                </select>
                                @error('semester') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- Kategori -->
                        <div class="form-group row">
                            <label for="kategori"
                                class="col-sm-3 text-end control-label col-form-label">Kategori</label>
                            <div class="col-sm-9">
                                <select
                                    class="form-control select2 form-select shadow-none @error('kategori') is-invalid @enderror"
                                    id="kategori" name="kategori">
                                    <option value="">Select</option>
                                    <option value="Wajib" {{ old('kategori', $course->kategori) == 'Wajib' ? 'selected'
                                        : '' }}>Wajib</option>
                                    <option value="Pilihan" {{ old('kategori', $course->kategori) == 'Pilihan' ?
                                        'selected' : '' }}>Pilihan</option>
                                </select>
                                @error('kategori') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="border-top">
                            <div class="card-body">
                                <a href="{{ route('admin.mk.byDepartment', $course->department_id) }}"
                                    class="btn btn-danger">Cancel</a>
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-main-layout>