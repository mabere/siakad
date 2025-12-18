<x-main-layout>
    @section('title', 'Detail Data Penunjang')
    <div class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">@yield('title')</h4>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-2 font-weight-bold">Judul:</div>
                    <div class="col-md-10">{{ $penunjang->title }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2 font-weight-bold">Penulis:</div>
                    <div class="col-md-10">{{ $penunjang->lecturer->nama_dosen }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2 font-weight-bold">Peran:</div>
                    <div class="col-md-10">{{ $penunjang->peran }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2 font-weight-bold">Penyelenggara:</div>
                    <div class="col-md-10">{{ $penunjang->organizer }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2 font-weight-bold">Tahun:</div>
                    <div class="col-md-10">{{ $penunjang->date->format('d F Y') }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2 font-weight-bold">Tingkat:</div>
                    <div class="col-md-10">{{ $penunjang->level }}</div>
                </div>
            </div>
            <div class="card-footer text-right">
                <a href="{{ route('lecturer.penunjang.index') }}" class="btn btn-warning">Back</a>
                <a href="{{ route('lecturer.penunjang.edit', $penunjang->id) }}" class="btn btn-secondary">Edit</a>
            </div>
        </div>
    </div>

</x-main-layout>