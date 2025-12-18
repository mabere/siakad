@if($processedKrs->isEmpty())
<div class="alert alert-info mt-3">
    <p class="mb-0">Belum ada KRS yang diproses.</p>
</div>
@else
@foreach($processedKrs as $academicYearId => $studyPlans)
<div class="card card-bordered mb-3 mt-3">
    <div class="card-header">
        <h5 class="card-title">
            Tahun Akademik: {{ $studyPlans->first()->academicYear->ta ?? 'Belum Ditentukan' }}/{{
            $studyPlans->first()->academicYear->semester}}
        </h5>
    </div>
    <div class="card-body">
        <div class="accordion" id="accordionProcessed{{ $academicYearId }}">
            @foreach($studyPlans->groupBy('student_id') as $studentId => $studentPlans)
            <div class="card border">
                <div class="card-header" id="heading{{ $studentId }}" style="position: relative;">
                    <h6 class="mb-0">
                        <button class="btn btn-link text-dark w-100 text-left" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapse{{ $studentId }}" aria-expanded="true"
                            aria-controls="collapse{{ $studentId }}">
                            <i class="icon ni ni-user-fill"></i> | {{ $studentPlans->first()->student->nama_mhs }} (NIM:
                            {{ $studentPlans->first()->student->nim }})
                        </button>
                    </h6>
                    <span style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%);">
                        <i class="icon ni ni-caret-down-fill accordion-caret"></i>
                    </span>
                </div>
                <div id="collapse{{ $studentId }}" class="collapse {{ $loop->first ? 'show' : '' }}"
                    aria-labelledby="heading{{ $studentId }}" data-bs-parent="#accordionProcessed{{ $academicYearId }}">
                    <div class="card-body">
                        <table class="table table-striped table-hover" style="font-size: 14px;">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Kode</th>
                                    <th>Mata Kuliah</th>
                                    <th>SKS</th>
                                    <th>Status</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($studentPlans as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}.</td>
                                    <td>{{ $item->course?->code ?? '-' }}</td>
                                    <td>{{ $item->course?->name ?? '-' }}</td>
                                    <td>{{ $item->course?->sks ?? '-' }}</td>
                                    <td>
                                        <span
                                            class="badge {{ $item->status == 'approved' ? 'bg-success' : 'bg-danger' }}">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $item->notes ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endforeach
@endif