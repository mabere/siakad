<x-main-layout>
    @section('title', 'Bimbingan PA')

    <div class="nk-content">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    @include('dosen.approval-krs.partials.header')
                    <div class="nk-block">
                        <div class="card">
                            <div class="card-inner">
                                @if(session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                                @endif

                                @include('dosen.approval-krs.partials.tab-navigation')

                                <div class="tab-content" id="krsTabContent">
                                    <div class="tab-pane fade show active" id="pending" role="tabpanel"
                                        aria-labelledby="pending-tab">
                                        @if($pendingKrs->isEmpty())
                                        <div class="alert alert-info mt-3">
                                            <p class="mb-0">Belum ada pengajuan bimbingan KRS dari Mahasiswa.</p>
                                        </div>
                                        @else
                                        @foreach($pendingKrs as $studentId => $studyPlans)
                                        @include('dosen.approval-krs.partials.krs-tabel', ['studyPlans' => $studyPlans,
                                        'showBulkActions' => false])
                                        @endforeach
                                        @endif
                                    </div>

                                    <div class="tab-pane fade" id="buklkrs" role="tabpanel"
                                        aria-labelledby="buklkrs-tab">
                                        @if($pendingKrs->isEmpty())
                                        <div class="alert alert-info mt-3">
                                            <p class="mb-0">Belum ada pengajuan bimbingan KRS dari Mahasiswa.</p>
                                        </div>
                                        @else
                                        @foreach($pendingKrs as $studentId => $studyPlans)
                                        @include('dosen.approval-krs.partials.krs-tabel', ['studyPlans' => $studyPlans,
                                        'showBulkActions' => true, 'studentId' => $studentId])
                                        @include('dosen.approval-krs.partials.bulk-modal', ['studentId' => $studentId,
                                        'studentName' => $studyPlans->first()->student->nama_mhs])
                                        @endforeach
                                        @endif
                                    </div>

                                    <div class="tab-pane fade" id="processed" role="tabpanel"
                                        aria-labelledby="processed-tab">
                                        @include('dosen.approval-krs.partials.processed_krs')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('dosen.approval-krs.partials.scripts')
</x-main-layout>