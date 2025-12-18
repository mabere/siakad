<x-main-layout>
    @section('title', 'Tambah Kelas')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form class="form-horizontal" action="{{ route('staff.kelas.store') }}" method="post">
                    @csrf
                    <div class="card-body">
                        <h4 class="card-title">@yield('title')</h4>
                        <div class="form-group row">
                            <label for="name" class="col-sm-3 text-end control-label col-form-label">Nama Kelas</label>
                            <div class="col-sm-9">
                                <input type="text" value="{{ old('name') }}"
                                    class="form-control @error('name') is-invalid @enderror" name="name"
                                    placeholder="Nama Kelas">
                            </div>
                            @error('name') <div class="text-muted">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group row">
                            <label for="lecturer_id" class="col-sm-3 text-end control-label col-form-label">Dosen
                                PA</label>
                            <div class="col-sm-9">
                                <select value="{{ old('lecturer_id') }}" name="lecturer_id"
                                    class="form-control @error('lecturer_id') is-invalid @enderror">
                                    <option value="">--Pilih--</option>
                                    @foreach ($dosen as $item)
                                    <option value="{{ $item->id }}" {{ old('lecturer_id')==$item->id ? 'selected' : ''
                                        }}>{{ $item->nama_dosen }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('lecturer_id') <div class="text-muted">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group row">
                            <label for="angkatan"
                                class="col-sm-3 text-end control-label col-form-label">Angkatan</label>
                            <div class="col-sm-9">
                                <input type="text" value="{{ old('angkatan') }}"
                                    class="form-control @error('angkatan') is-invalid @enderror" name="angkatan"
                                    placeholder="Angkatan">
                            </div>
                            @error('angkatan') <div class="text-muted">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group row">
                            <label for="total" class="col-sm-3 text-end control-label col-form-label">Daya Tampung
                                Mahasiswa</label>
                            <div class="col-sm-9">
                                <input type="text" value="{{ old('total') }}"
                                    class="form-control @error('total') is-invalid @enderror" name="total"
                                    placeholder="Total">
                            </div>
                            @error('total') <div class="text-muted">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="border-top">
                        <div class="card-body">
                            <button type="submit" class="btn btn-primary">Tambah</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-main-layout>
