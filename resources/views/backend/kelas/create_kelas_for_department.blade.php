<x-main-layout>
    @section('title', 'Tambah Kelas')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form class="form-horizontal" action="{{ route('admin.kelas.store') }}" method="post">
                    @csrf
                    <div class="card-body">
                        <h4 class="card-title">@yield('title')</h4>

                        <!-- Hidden input untuk department_id -->
                        <input type="hidden" name="department_id" value="{{ $department_id }}">

                        <div class="form-group row">
                            <label for="name" class="col-sm-3 text-end control-label col-form-label">Nama Kelas</label>
                            <div class="col-sm-9">
                                <input type="text" value="{{ old('name') }}"
                                    class="form-control @error('name') is-invalid @enderror" name="name"
                                    placeholder="Nama Kelas">
                                @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="advisor_id" class="col-sm-3 text-end control-label col-form-label">Dosen
                                PA</label>
                            <div class="col-sm-9">
                                <select class="form-control form-select @error('advisor_id') is-invalid @enderror"
                                    name="advisor_id">
                                    <option value="">--Pilih--</option>
                                    @foreach ($dosen as $item)
                                    <option value="{{ $item->id }}" {{ old('advisor_id')==$item->id ? 'selected' : ''
                                        }}>
                                        {{ $item->nama_dosen }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('advisor_id') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="angkatan"
                                class="col-sm-3 text-end control-label col-form-label">Angkatan</label>
                            <div class="col-sm-9">
                                <input type="text" value="{{ old('angkatan') }}"
                                    class="form-control @error('angkatan') is-invalid @enderror" name="angkatan"
                                    placeholder="Angkatan">
                                @error('angkatan') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="total" class="col-sm-3 text-end control-label col-form-label">Daya Tampung
                                Mahasiswa</label>
                            <div class="col-sm-9">
                                <input type="text" value="{{ old('total') }}"
                                    class="form-control @error('total') is-invalid @enderror" name="total"
                                    placeholder="Daya Tampung">
                                @error('total') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
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