<x-main-layout>
    @section('title', 'Tambah Mata Kuliah')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form class="form-horizontal" method="POST" action="{{ route('staff.course.store') }}">
                    @csrf
                    <div class="card-body">
                        <h4 class="card-title">Tambah Mata Kuliah</h4>
                        <div class="form-group row">
                            <label for="name" class="col-sm-3 text-end control-label col-form-label">Nama
                                Mata Kuliah</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name">
                                @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="code" class="col-sm-3 text-end control-label col-form-label">Kode Mata
                                Kuliah</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('code') is-invalid @enderror" name="code">
                                @error('code') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="sks" class="col-sm-3 text-end control-label col-form-label">SKS Mata
                                Kuliah</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('sks') is-invalid @enderror" name="sks">
                                @error('sks') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="semester_number" class="col-sm-3 text-end control-label col-form-label">Tingkat</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('semester_number') is-invalid @enderror" name="semester_number">
                                @error('semester_number') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="semester"
                                class="col-sm-3 text-end control-label col-form-label">Semester</label>
                            <div class="col-sm-9">
                                <select class="form-control select2 form-select shadow-none
                                    @error('semester') is-invalid @enderror" id="semester" name="semester">
                                    <option value="">Select</option>
                                    <option value="Ganjil">Ganjil</option>
                                    <option value="Genap">Genap</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kategori"
                                class="col-sm-3 text-end control-label col-form-label">Kategori</label>
                            <div class="col-sm-9">
                                <select class="form-control select2 form-select shadow-none
                                    @error('kategori') is-invalid @enderror" id="kategori" name="kategori">
                                    <option value="">Select</option>
                                    <option value="Wajib">Wajib</option>
                                    <option value="Pilihan">Pilihan</option>
                                </select>
                                @error('kategori') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="border-top">
                            <div class="card-body">
                                <a href="{{ route('staff.course.index') }}" class="btn btn-danger">Cancel</a>
                                <button type="submit" class="btn btn-primary">Tambah</button>
                            </div>
                        </div>
                </form>
            </div>
        </div>
    </div>
</x-main-layout>
