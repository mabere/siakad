<x-main-layout>
    @section('title', 'Daftar Ujian Skripsi')

    @php
    // Ambil data ujian terakhir untuk menentukan konteks
    $latestExam = $thesis->exams()->latest()->first();

    $isFirstRegistration = !$latestExam;
    $isReregistration = $latestExam && $latestExam->status === 'ditolak';

    $nextExamType = null;
    if ($isFirstRegistration) {
    $nextExamType = 'proposal';
    } elseif ($isReregistration) {
    $nextExamType = $latestExam->exam_type;
    } elseif ($latestExam && in_array($latestExam->status, ['lulus', 'lulus_revisi'])) {
    $nextExamType = ($latestExam->exam_type === 'proposal') ? 'hasil' : (($latestExam->exam_type === 'hasil') ? 'tutup'
    : null);
    }

    $formTitle = 'Formulir Pendaftaran Ujian ' . ucfirst($nextExamType);
    $buttonText = ($isReregistration) ? 'Ajukan Ulang Ujian' : 'Ajukan Pendaftaran Ujian';

    // PERBAIKAN: Ambil dokumen dari latestExam jika ada, atau koleksi kosong jika belum ada.
    $docs = $latestExam ? $latestExam->documents->keyBy('document_type') : collect();
    $allDocTypes = ['lembar_persetujuan', 'draft_skripsi', 'bukti_pembayaran'];
    @endphp

    <div class="card mt-3">
        <div class="card-header">
            <h4>
                {{ $formTitle }}
            </h4>
        </div>
        <div class="card-body">
            <x-custom.sweet-alert />

            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <form action="{{ route('mahasiswa.thesis.exam.register', $thesis->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="mb-3 col-9">
                        <label for="thesis_title" class="form-label">Judul Skripsi</label>
                        <input type="text" class="form-control" id="thesis_title" value="{{ $thesis->title }}" disabled
                            readonly>
                    </div>

                    <div class="mb-3 col-3">
                        <label for="exam_type" class="form-label">Jenis Ujian</label>
                        <input type="text" class="form-control" value="{{ ucfirst($nextExamType) }}" disabled readonly>
                        <input type="hidden" name="exam_type" value="{{ $nextExamType }}">
                    </div>
                </div>

                <hr>

                <h5 class="mt-4 mb-3">Unggah Dokumen</h5>
                <div class="row">
                    @foreach($allDocTypes as $docType)
                    @php
                    $doc = $docs[$docType] ?? null;
                    $label = ucwords(str_replace('_', ' ', $docType));
                    @endphp

                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="card-title">
                                    {{ $label }}
                                    @if($doc && $doc->status)
                                    <span
                                        class="ms-2 badge bg-{{ $doc->status === 'approved' ? 'success' : ($doc->status === 'rejected' ? 'danger' : 'warning') }}">
                                        {{ ucwords($doc->status) }}
                                    </span>
                                    @endif
                                </h6>

                                {{-- @if ($doc && $doc->notes)
                                <div class="alert alert-secondary mt-2 p-2">
                                    <small>Catatan KTU: {{ $doc->notes }}</small>
                                </div>
                                @endif --}}

                                <div class="my-3">
                                    <label for="{{ $docType }}" class="form-label">Unggah ulang (PDF)</label>
                                    <input type="file" class="form-control @error($docType) is-invalid @enderror"
                                        id="{{ $docType }}" name="{{ $docType }}" accept="application/pdf" {{ !$doc
                                        ? 'required' : '' }}>
                                    @error($docType)
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    {{-- @if ($doc)
                                    <p class="text-muted mt-1">Dokumen lama: <a
                                            href="{{ asset('storage/documents/' . $doc->file_path) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary"><i
                                                class="icon ni ni-eye me-1"></i>Lihat {{ $label }}</a></p>
                                    @endif --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <button type="submit" class="btn btn-primary">
                    {{ $buttonText }}
                </button>
            </form>
        </div>
    </div>
</x-main-layout>
