<x-main-layout>
    @section('title', 'Edit Tahun Akademik')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form class="form-horizontal" action="{{ route('admin.ta.update', $ta->id) }}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <a href="/admin/ta">Back</a>
                        <h4 class="card-title">@yield('title')</h4>
                        <div class="form-group row">
                            <label for="ta" class="col-sm-3 text-end control-label col-form-label">Tahun
                                Akademik</label>
                            <div class="col-sm-9">
                                <input type="text" value="{{ $ta->ta }}" class="form-control" name="ta" disabled>
                            </div>
                            @error('ta') <div class="text-muted">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group row">
                            <label for="krs_open_date" class="col-sm-3 text-end control-label col-form-label">KRS Mulai
                                Dibuka</label>
                            <div class="col-sm-9">
                                <input type="date" value="{{ $ta->krs_open_date }}"
                                    class="form-control @error('krs_open_date') is-invalid @enderror"
                                    name="krs_open_date">
                            </div>
                            @error('krs_open_date') <div class="text-muted">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group row">
                            <label for="krs_close_date" class="col-sm-3 text-end control-label col-form-label">KRS
                                Ditutup</label>
                            <div class="col-sm-9">
                                <input type="date" value="{{ $ta->krs_close_date }}"
                                    class="form-control @error('krs_close_date') is-invalid @enderror"
                                    name="krs_close_date">
                            </div>
                            @error('krs_close_date') <div class="text-muted">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group row">
                            <label for="semester"
                                class="col-sm-3 text-end control-label col-form-label">Semester</label>
                            <div class="col-sm-9">
                                <input type="text" value="{{ $ta->semester }}" class="form-control" name="semester"
                                    disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="status" class="col-sm-3 text-end control-label col-form-label">Status</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control"
                                    value="{{ $ta->status == 0 ? 'Tidak Aktif' : 'Aktif' }}" disabled>
                            </div>
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