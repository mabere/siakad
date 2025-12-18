<x-main-layout>
    @section('title', 'Tambah Fakultas')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form class="form-horizontal" action="{{ route('admin.faculty.store') }}" method="post">
                    @csrf
                    <div class="card-body">
                        <h4 class="card-title">@yield('title')</h4>
                        <div class="form-group row">
                            <label for="name" class="col-sm-3 text-end control-label col-form-label">Nama
                                Fakultas</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('nama') is-invalid @enderror" name="nama">
                                @error('nama') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="dekan" class="col-sm-3 text-end control-label col-form-label">Dekan</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('dekan') is-invalid @enderror"
                                    name="dekan">
                                @error('dekan') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="visi" class="col-sm-3 text-end control-label col-form-label">Visi</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('visi') is-invalid @enderror" name="visi">
                                @error('visi') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="misi" class="col-sm-3 text-end control-label col-form-label">Misi</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('misi') is-invalid @enderror" name="misi">
                                @error('misi') <div class="text-danger">{{ $message }}</div> @enderror
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