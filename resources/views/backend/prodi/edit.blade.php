<x-main-layout>
    @section('title', 'Edit Program Studi')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form class="form-horizontal" action="{{ route('admin.prodi.update', $department->id) }}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <h4 class="card-title">@yield('title')</h4>
                        <div class="form-group row">
                            <label for="name" class="col-sm-3 text-end control-label col-form-label">Nama
                                Program Studi</label>
                            <div class="col-sm-9">
                                <input type="text" value="{{ $department->nama }}"
                                    class="form-control @error('nama') is-invalid @enderror" name="nama">
                                @error('nama') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="faculty_id"
                                class="col-sm-3 text-end control-label col-form-label">Fakultas</label>
                            <div class="col-sm-9">
                                <select class="form-control select2 form-select shadow-none mt-3 
                                    @error('faculty_id') is-invalid @enderror" name="faculty_id">
                                    <option {{ ($department->faculty->id) ? 'selected' : '' }} value="
                                        {{ ($department->faculty->id) }}">
                                        {{ $department->faculty->nama }}</option>

                                    @foreach ($faculty as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                                @error('faculty_id') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>


                        <div class="form-group row">
                            <label for="kaprodi" class="col-sm-3 text-end control-label col-form-label">Kaprodi</label>
                            <div class="col-sm-9">
                                <input type="text" value="{{ $department->kaprodi }}"
                                    class="form-control @error('kaprodi') is-invalid @enderror" name="kaprodi">
                                @error('kaprodi') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="jenjang" class="col-sm-3 text-end control-label col-form-label">Jenjang</label>
                            <div class="col-sm-9">
                                <input type="text" value="{{ $department->jenjang }}"
                                    class="form-control @error('jenjang') is-invalid @enderror" name="jenjang">
                                @error('jenjang') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="visi" class="col-sm-3 text-end control-label col-form-label">Visi</label>
                            <div class="col-sm-9">
                                <input type="text" value="{{ $department->visi }}"
                                    class="form-control @error('visi') is-invalid @enderror" name="visi">
                                @error('visi') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="misi" class="col-sm-3 text-end control-label col-form-label">Misi</label>
                            <div class="col-sm-9">
                                <input type="text" value="{{ $department->misi }}"
                                    class="form-control @error('misi') is-invalid @enderror" name="misi">
                                @error('misi') <div class="text-danger">{{ $message }}</div> @enderror
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