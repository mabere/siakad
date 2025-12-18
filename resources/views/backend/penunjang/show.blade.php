<x-main-layout>
    @section('title', 'Detail Penunjang Dosen')
    <div class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">@yield('title')</h3>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-2 font-weight-bold">Judul :</div>
                    <div class="col-md-10">{{ $pkm->title }}</div> <!-- Edisi -->
                </div>
                <div class="row mb-3">
                    <div class="col-md-2 font-weight-bold">Penulis:</div>
                    <div class="col-md-10">@foreach($pkm->lecturers as $dosen)
                        <span>{{ $dosen->nama_dosen }}</span>
                        @endforeach
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2 font-weight-bold">Edisi:</div>
                    <div class="col-md-10">{{ $pkm->pendanaan }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2 font-weight-bold">Tahun:</div>
                    <div class="col-md-10">{{ $pkm->year }}</div>
                </div>
            </div>
            <div class="card-footer text-right">
                <a href="{{ url('admin/pkm') }}" class="btn btn-warning">Back</a>
                <a href="{{ url('admin/pkm/' . $pkm->id . '/edit') }}" class="btn btn-secondary">Edit</a>
            </div>
        </div>
    </div>

</x-main-layout>