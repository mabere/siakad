<x-main-layout>
    @section('title', 'Edit Kelas')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form class="form-horizontal" action="{{ route('staff.kelas.update', $kelas->id) }}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <h4 class="card-title">@yield('title')</h4>
                        <div class="form-group row">
                            <label for="name" class="col-sm-3 text-end control-label col-form-label">Nama Kelas</label>
                            <div class="col-sm-9">
                                <input type="text" value="{{ $kelas->name }}"
                                    class="form-control @error('name') is-invalid @enderror" name="name">
                                @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="lecturer_id" class="col-sm-3 text-end control-label col-form-label">Dosen
                                PA</label>
                            <div class="col-sm-9">
                                <select type="text"
                                    class="select2 form-select shadow-none mt-3 @error('lecturer_id') is-invalid @enderror"
                                    name="lecturer_id">
                                    <option {{ ($kelas->lecturer->id) ? 'selected' : '' }} value="{{
                                        ($kelas->lecturer->id)
                                        }}">{{
                                        $kelas->lecturer->nama_dosen }}</option>
                                    @foreach ($dosen as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama_dosen }}</option>
                                    @endforeach
                                </select>
                                @error('lecturer_id') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="angkatan"
                                class="col-sm-3 text-end control-label col-form-label">Angkatan</label>
                            <div class="col-sm-9">
                                <input type="text" value="{{ $kelas->angkatan }}"
                                    class="form-control @error('angkatan') is-invalid @enderror" name="angkatan">
                                @error('angkatan') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="total" class="col-sm-3 text-end control-label col-form-label">Jumlah
                                Mahasiswa</label>
                            <div class="col-sm-9">
                                <input type="text" value="{{ $kelas->total }}"
                                    class="form-control @error('total') is-invalid @enderror" name="total">
                                @error('total') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="border-top">
                        <div class="card-body">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-main-layout>