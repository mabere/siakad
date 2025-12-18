<x-main-layout>
    @section('title', 'Tambah Unit Pelaksana Teknis')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form action="{{ route('admin.units.store') }}" method="post">
                    @csrf
                    <div class="card-body">
                        <h4 class="card-title">@yield('title')</h4>
                        <div class="form-group row">
                            <label for="nama" class="col-sm-3 text-end control-label col-form-label">Nama Unit/Lembaga
                                Program Studi</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('nama') is-invalid @enderror" name="nama">
                                @error('nama') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kepala" class="col-sm-3 text-end control-label col-form-label">kepala
                                Unit/Lembaga
                                Program Studi</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('kepala') is-invalid @enderror"
                                    name="kepala">
                                @error('kepala') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="level" class="col-sm-3 text-end control-label col-form-label">level Unit/Lembaga
                                Program Studi</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('level') is-invalid @enderror"
                                    name="level">
                                @error('level') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <button type="submit"><i class="icon ni ni-send"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <x-custom.sweet-alert />
</x-main-layout>
