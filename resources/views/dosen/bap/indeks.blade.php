<x-main-layout>
    @section('title', 'Pengisian BAP')

    @foreach ($jadwal->lecturers as $lecturer)
    @if (auth()->user()->id == $lecturer->id)
    @for ($i = $lecturer->pivot->start_pertemuan; $i <= $lecturer->pivot->end_pertemuan; $i++)
        <a href="{{ route('lecturer.bap-list', ['scheduleId' => $jadwal->id, 'pertemuan' => $i]) }}">
            Isi BAP untuk Pertemuan {{ $i }}
        </a>
        @endfor
        @endif
        @endforeach

</x-main-layout>