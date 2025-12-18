<x-main-layout>
    @section('title', 'Tambah Tahun Akademik')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form class="form-horizontal" action="{{ route('admin.ta.store') }}" method="post">
                    @csrf
                    <div class="card-body">
                        <h4 class="card-title">@yield('title')</h4>
                        <div class="form-group row">
                            <label for="ta" class="col-sm-3 text-end control-label col-form-label">Tahun
                                Akademik</label>
                            <div class="col-sm-9">
                                <input type="text" value="{{ old('ta') }}"
                                    class="form-control @error('ta') is-invalid @enderror" name="ta"
                                    placeholder="Tahun Akademik">
                            </div>
                            @error('ta') <div class="text-muted">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group row">
                            <label for="semester"
                                class="col-sm-3 text-end control-label col-form-label">Semester</label>
                            <div class="col-sm-9">
                                <select type="text" value="{{ old('semester') }}"
                                    class="form-control @error('semester') is-invalid @enderror" name="semester">
                                    <option value="">--Pilih--</option>
                                    <option value="Ganjil">Ganjil</option>
                                    <option value="Genap">Genap</option>
                                </select>
                            </div>
                            @error('semester') <div class="text-muted">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group row">
                            <label for="start_date" class="col-sm-3 text-end control-label col-form-label">Mulai</label>
                            <div class="col-sm-9">
                                <input type="date" value="{{ old('start_date') }}"
                                    class="form-control @error('start_date') is-invalid @enderror" name="start_date"
                                    placeholder="Tahun Akademik">
                            </div>
                            @error('start_date') <div class="text-muted">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group row">
                            <label for="end_date" class="col-sm-3 text-end control-label col-form-label">Selesai</label>
                            <div class="col-sm-9">
                                <input type="date" value="{{ old('end_date') }}"
                                    class="form-control @error('end_date') is-invalid @enderror" name="end_date"
                                    placeholder="Tahun Akademik">
                            </div>
                            @error('end_date') <div class="text-muted">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group row">
                            <label for="krs_open_date" class="col-sm-3 text-end control-label col-form-label">KRS Mulai
                                Dibuka</label>
                            <div class="col-sm-9">
                                <input type="date" value="{{ old('krs_open_date') }}"
                                    class="form-control @error('krs_open_date') is-invalid @enderror"
                                    name="krs_open_date" placeholder="Tahun Akademik">
                            </div>
                            @error('krs_open_date') <div class="text-muted">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group row">
                            <label for="krs_close_date" class="col-sm-3 text-end control-label col-form-label">KRS
                                Ditutup</label>
                            <div class="col-sm-9">
                                <input type="date" value="{{ old('krs_close_date') }}"
                                    class="form-control @error('krs_close_date') is-invalid @enderror"
                                    name="krs_close_date" placeholder="Tahun Akademik">
                            </div>
                            @error('krs_close_date') <div class="text-muted">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="border-top">
                        <div class="card-body">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-main-layout>