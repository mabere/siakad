<x-main-layout>
    @section('title', 'Detail Pengabdian Masyarakat')
    <div class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0 text-uppercase">@yield('title')</h4>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-2 font-weight-bold">Judul:</div>
                    <div class="col-md-10">{{ $pkm->title }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2 font-weight-bold">Penulis:</div>
                    <div class="col-md-10">
                        @php
                        $lecturers = $pkm->lecturers->pluck('nama_dosen')->toArray();
                        $lastLecturer = array_pop($lecturers);
                        @endphp
                        @if(count($lecturers) > 0)
                        {{ implode(', ', $lecturers) }}@if(count($lecturers) > 1),
                        @else

                        @endif
                        &
                        @endif
                        {{ $lastLecturer }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2 font-weight-bold">Media:</div>
                    <div class="col-md-10">{{ $pkm->pendanaan }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2 font-weight-bold">Tahun:</div>
                    <div class="col-md-10">{{ $pkm->year }}</div> <!-- Tahun -->
                </div>
            </div>
            <div class="card-footer text-right">
                <a href="{{ route('lecturer.pkm.index') }}" class="btn btn-warning">Back</a>
                <a href="{{ route('lecturer.pkm.edit', $pkm->id) }}" class="btn btn-secondary">Edit</a>
            </div>
        </div>
    </div>

</x-main-layout>