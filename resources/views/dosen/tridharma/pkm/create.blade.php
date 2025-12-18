<x-main-layout>
    @section('title', 'Tambah Data PKM')
    <div class="nk-block-head">
        <div class="nk-block-head-content">
            <h4 class="title nk-block-title">@yield('title')</h4>
            <div class="nk-block-des">
                <p><a href="/dosen/pkm">Back</a></p>
            </div>
        </div>
    </div>

    <div class="card card-bordered card-preview">
        <div class="card-inner">
            <div class="preview-block">
                <span class="preview-title-lg overline-title">Form Update Data PKM</span>
                <form class="form-validate" method="POST" action="{{ route('lecturer.pkm.store') }}">
                    @csrf
                    <div class="row gy-4">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="title">Judul</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" :value="old('title')" name="title"
                                        placeholder="Judul Publikasi">
                                </div>
                                @error('title') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="pendanaan">Sumber Pendanaan</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" :value="old('pendanaan')" name="pendanaan"
                                        placeholder="Sumber Pendaan: Internal, Eksternal, atau Lain-lain">
                                </div>
                                @error('pendanaan') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="lecturer_id">Co-Penulis</label>
                                <div class="form-control-wrap">
                                    <select class="form-control form-select @error('lecturer_id') is-invalid @enderror"
                                        name="lecturer_id[]" multiple="multiple">
                                        <optgroup label="Pilih Penulis">
                                            @foreach($dosen as $dsn)
                                            <option value="{{ $dsn->id }}">{{ $dsn->id }} - {{ $dsn->nama_dosen }}
                                            </option>
                                            @endforeach
                                        </optgroup>
                                    </select>
                                    @error('lecturer_id') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label" for="year">Tahun Pelaksanaan</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" :value="old('year')" name="year"
                                        placeholder="Edisi Terbitan">
                                </div>
                                @error('year') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>

                    </div>
                    <br>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <button type="submit" class="btn btn-md btn-primary">Tambah</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-main-layout>