<x-main-layout>
    @section('title', 'Edit Mata Kuliah')
    <div class="card card-bordered">
        <div class="card-inner">
            <div class="card-head">
                <h5 class="card-title">@yield('title')</h5>
            </div>
            <form method="POST" action="{{ route('staff.course.update', $course->id) }}">
                @csrf
                @method('PUT')
                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="form-label" for="name">Nama Mata Kuliah</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control" id="name" name="name" :value="old('name')"
                                    value="{{ ($course->name) }}">
                            </div>
                        </div>
                        @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="form-label" for="code">Kode Mata Kuliah</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control" id="code" name="code" :value="old('code')"
                                    value="{{ ($course->code) }}">
                            </div>
                        </div>
                        @error('code') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="form-label" for="sks">SKS Mata Kuliah</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control" id="sks" name="sks" :value="old('sks')"
                                    value="{{ ($course->sks) }}">
                            </div>
                        </div>
                        @error('sks') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="form-label" for="semester_number">Tingkat/Semester</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control" id="semester_number" name="semester_number"
                                    :value="old('semester_number')" value="{{ ($course->semester_number) }}">
                            </div>
                        </div>
                        @error('semester_number') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="form-label" for="kategori">Kategori</label>
                            <div class="form-control-wrap">
                                <select class="form-control form-select @error('kategori') is-invalid @enderror"
                                    id="kategori" name="kategori">
                                    <option value="">Pilih Kategori</option>
                                    <option value="Wajib" {{ old('kategori', $course->kategori) == 'Wajib' ? 'selected'
                                        : '' }}>Wajib</option>
                                    <option value="Pilihan" {{ old('kategori', $course->kategori) == 'Pilihan' ?
                                        'selected' : '' }}>Pilihan</option>
                                </select>
                            </div>
                        </div>
                        @error('kategori') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>


                    <div class="col-12 d-flex">
                        <div class="form-group float-right">
                            <a href="{{ route('staff.course.index') }}" class="btn btn-md btn-info">Cancel</a>
                            <button type="submit" class="btn btn-md btn-primary">Update</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-main-layout>