<x-main-layout>
    @section('title', 'Detail Publikasi')
    <div class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">@yield('title')</h4>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-2 font-weight-bold">Judul:</div>
                    <div class="col-md-1" style="width: 2px">:</div>
                    <div class="col-md-9 p-0">{{ $publikasi->title }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2 font-weight-bold">Penulis:</div>
                    <div class="col-md-1" style="width: 2px">:</div>
                    <div class="col-md-9 p-0">
                        @php
                        $authors = $publikasi->lecturers->pluck('nama_dosen')->toArray();
                        $count = count($authors);

                        if ($count === 1) {
                        echo $authors[0];
                        } elseif ($count === 2) {
                        echo $authors[0] . ' & ' . $authors[1];
                        } else {
                        $lastAuthor = array_pop($authors);
                        echo implode(', ', $authors) . ', & ' . $lastAuthor;
                        }
                        @endphp
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2 font-weight-bold">Media</div>
                    <div class="col-md-1" style="width: 2px">:</div>
                    <div class="col-md-9 p-0">{{ $publikasi->media }} - {{ $publikasi->media_name }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2 font-weight-bold">Edisi</div>
                    <div class="col-md-1" style="width: 2px">:</div>
                    <div class="col-md-9 p-0">{{ $publikasi->issue }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2 font-weight-bold">Tahun:</div>
                    <div class="col-md-1" style="width: 2px">:</div>
                    <div class="col-md-9 p-0">{{ $publikasi->year }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2 font-weight-bold">Halaman:</div>
                    <div class="col-md-1" style="width: 2px">:</div>
                    <div class="col-md-9 p-0">{{ $publikasi->page }}</div>
                </div>
                <div class="row">
                    <div class="col-md-2 font-weight-bold">Abstrak:</div>
                    <div class="col-md-1" style="width: 2px">:</div>
                    <div class="col-md-9 p-0">
                        <p>{{ $publikasi->abstract }}</p>
                    </div>
                </div>
            </div>
            <div class="card-footer text-right">
                <a href="{{ Auth::user()->isAdmin() ? route('admin.publication.index') : route('lecturer.publication.index') }}"
                    class="btn btn-warning">Back</a>
                <a href="{{ Auth::user()->isAdmin() ? route('admin.publication.edit', $publikasi->id) : route('lecturer.publication.edit', $publikasi->id) }}"
                    class="btn btn-secondary">Edit</a>
            </div>
        </div>
    </div>

</x-main-layout>