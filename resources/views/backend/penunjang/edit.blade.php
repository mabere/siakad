<x-main-layout>
    @section('title', 'Edit Data Penunjang')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form class="form-horizontal" action="{{ route('admin.penunjang.update', $penunjang->id) }}" method="post" enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <div class="card-body">
                        <h4 class="card-title">@yield('title')</h4>
                        
                        <div class="form-group row">
                            <label class="col-sm-3 text-end control-label col-form-label">Dosen</label>
                            <div class="col-sm-9">
                                <select name="lecturer_id" class="form-select @error('lecturer_id') is-invalid @enderror">
                                    <option value="">Pilih Dosen</option>
                                    @foreach($lecturers as $dosen)
                                        <option value="{{ $dosen->id }}" {{ $penunjang->lecturer_id == $dosen->id ? 'selected' : '' }}>
                                            {{ $dosen->nama_dosen }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('lecturer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 text-end control-label col-form-label">Judul Kegiatan</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                    name="title" value="{{ old('title', $penunjang->title) }}">
                                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 text-end control-label col-form-label">Penyelenggara</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('organizer') is-invalid @enderror" 
                                    name="organizer" value="{{ old('organizer', $penunjang->organizer) }}">
                                @error('organizer') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 text-end control-label col-form-label">Tingkat Kegiatan</label>
                            <div class="col-sm-9">
                                <select name="level" class="form-select @error('level') is-invalid @enderror">
                                    <option value="">Pilih Tingkat</option>
                                    @foreach(['Nasional', 'Internasional', 'Regional'] as $level)
                                        <option value="{{ $level }}" {{ $penunjang->level == $level ? 'selected' : '' }}>
                                            {{ $level }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('level') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 text-end control-label col-form-label">Peran</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('peran') is-invalid @enderror" 
                                    name="peran" value="{{ old('peran', $penunjang->peran) }}">
                                @error('peran') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 text-end control-label col-form-label">Tanggal Kegiatan</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control @error('date') is-invalid @enderror" 
                                    name="date" value="{{ old('date', $penunjang->date) }}">
                                @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 text-end control-label col-form-label">Bukti Kegiatan</label>
                            <div class="col-sm-9">
                                <input type="file" class="form-control @error('proof') is-invalid @enderror" 
                                    name="proof">
                                @if($penunjang->proof)
                                    <small class="text-muted">File saat ini: {{ $penunjang->proof }}</small>
                                @endif
                                @error('proof') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="border-top">
                        <div class="card-body">
                            <button type="submit" class="btn btn-primary">
                                Simpan
                            </button>
                            <a href="{{ route('admin.penunjang.index') }}" class="btn btn-secondary">
                                Kembali
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-main-layout>