<div class="nk-block">
    <div class="card card-bordered">
        <div class="card-inner">
            <h5 class="card-title">Pilih Mata Kuliah</h5>
            @if($isKrsActive)
            <form action="{{ route('student.krs.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Pilih Mata Kuliah</label>
                    <select name="selected_schedule_ids[]" class="form-control select2" multiple>
                        @forelse ($schedules as $schedule)
                        <option value="{{ $schedule->id }}">
                            {{ $schedule->course_name }} ({{ $schedule->course_code }}, {{ $schedule->sks_value }} SKS)
                            - {{ $schedule->hari ?? '-' }}, {{ $schedule->time_range }}
                            ({{ $schedule->kelas->name ?? ($schedule->is_mkdu ? 'MKDU' : '-') }})
                            - Dosen: {{ $schedule->lecturersInSchedule->pluck('name')->join(', ') ?? '-' }}
                        </option>
                        @empty
                        <option disabled>Tidak ada mata kuliah yang tersedia untuk dipilih.</option>
                        @endforelse
                    </select>
                    @error('selected_schedule_ids')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <p><strong>Total SKS saat ini: {{ $totalSks }}</strong> (Maksimum: {{ config('academic.max_sks', 24)
                        }})</p>
                </div>
                <button type="submit" class="btn btn-primary">Simpan KRS</button>
            </form>
            @else
            <p class="text-muted">Periode KRS telah selesai. Anda tidak dapat menambah mata kuliah.</p>
            @endif
        </div>
    </div>
</div>
