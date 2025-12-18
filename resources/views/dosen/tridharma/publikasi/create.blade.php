<x-main-layout>
    @section('title', 'Tambah Data Publikasi')
    <div class="nk-block-head">
        <div class="nk-block-head-content">
            <h4 class="title nk-block-title">@yield('title')</h4>
            <div class="nk-block-des">
                <p><a href="/dosen/publication">Back</a></p>
            </div>
        </div>
    </div>

    <div class="card card-bordered card-preview">
        <div class="card-inner">
            <div class="preview-block">
                <span class="preview-title-lg overline-title">Form @yield('title')</span>
                <form class="form-validate" method="POST" action="{{ route('lecturer.publication.store') }}">
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
                                <label class="form-label" for="lecturer_id">Penulis</label>
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
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="media">Media Publikasi</label>
                                <div class="form-control-wrap">
                                    <select name="media" id="media"
                                        class="form-control forl-select @error('media') is-invalid @enderror">
                                        <option value="">Pilih</option>
                                        <option value="Jurnal">Jurnal</option>
                                        <option value="Prosiding">Prosiding</option>
                                        <option value="Buku">Buku</option>
                                    </select>
                                    @error('media')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="media_name">Nama Media Publikasi</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" :value="old('media_name')" name="media_name"
                                        placeholder="Nama Media Publikasi">
                                </div>
                                @error('media_name')<div class="text-danger">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="form-label" for="issue">Edisi</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" :value="old('issue')" name="issue"
                                        placeholder="Edisi Terbitan">
                                </div>
                                @error('issue') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="form-label" for="year">Tahun</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" :value="old('year')" name="year"
                                        placeholder="Edisi Terbitan">
                                </div>
                                @error('year') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="form-label" for="page">Halaman</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" :value="old('page')" name="page"
                                        placeholder="Edisi Terbitan">
                                </div>
                                @error('page') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="form-label" for="citation">Jumlah Sitasi</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" :value="old('citation')" name="citation"
                                        placeholder="Edisi Terbitan">
                                </div>
                                @error('citation') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="form-label" for="abstract">Abstrak</label>
                                <div class="form-control-wrap">
                                    <textarea type="abstract" class="form-control" :value="old('abstract')"
                                        name="abstract" placeholder="Tulisakan Abstract"></textarea>
                                </div>
                                @error('abstract') <div class="text-danger">{{ $message }}</div> @enderror
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