<div class="nk-block">
    <div class="card card-bordered">
        <div class="card-header">
            <h5 class="card-title">Riwayat KRS</h5>
        </div>
        <div class="card-inner d-flex justify-content-between mb-0">
            <h6>Tahun Akademik: {{ $ta->ta ?? '-' }} ({{ $ta->semester ?? '-' }})</h6>
            <a href="{{ route('student.krs.print', ['academic_year_id' => $ta->id]) }}" class="btn btn-sm btn-primary"
                target="_blank">
                <em class="icon ni ni-printer"></em>
                <span>KRS</span>
            </a>
        </div>
        <div class="card-inner pt-0">
            @forelse($krsHistory as $academicYearId => $studyPlans)
            <div class="table-responsive">
                <table class="table table-striped" border="1">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Mata Kuliah</th>
                            <th>SKS</th>
                            <th>Dosen</th>
                            <th>Status</th>
                            <th>Catatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($studyPlans as $studyPlan)
                        <tr>
                            <td>{{ $studyPlan->schedule->course_code ?? ($studyPlan->mkduCourse->code ?? '-') }}</td>
                            <td>{{ $studyPlan->schedule->course_name ?? ($studyPlan->mkduCourse->name ?? '-') }}</td>
                            <td>{{ $studyPlan->schedule->sks_value ?? ($studyPlan->mkduCourse->sks ?? 0) }}</td>
                            <td>
                                @if($studyPlan->schedule && $studyPlan->schedule->lecturersInSchedule->isNotEmpty())
                                @foreach($studyPlan->schedule->lecturersInSchedule as $lecturer)
                                {{ $lecturer->nama_dosen }}
                                @if(!$loop->last)<br>
                                @endif
                                @endforeach
                                @else
                                -
                                @endif
                            </td>
                            <td>
                                @if($studyPlan->status === 'approved')
                                <span class="badge bg-success">Approved</span>
                                @elseif($studyPlan->status === 'pending')
                                <span class="badge bg-warning">Pending</span>
                                @else
                                <span class="badge bg-danger">Tidak Diketahui</span>
                                @endif
                            </td>
                            <td>{{ $studyPlan->notes ?? '-' }}</td>
                            <td>
                                @if($studyPlan->status === 'pending' && $isKrsActive)
                                <form action="{{ route('student.krs.destroy', $studyPlan) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Hapus mata kuliah ini dari KRS?')">Hapus</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @empty
            <p>Belum ada riwayat KRS.</p>
            @endforelse
        </div>
    </div>
</div>
