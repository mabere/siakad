<x-main-layout>
    @section('title', 'Daftar Ujian Skripsi')

    <div class="card mt-3">
        <div class="card-header">
            <h4>
                Formulir Pendaftaran Ujian Skripsi
                @if ($thesis->exam)
                <span class="badge bg-warning">Revisi</span>
                @endif
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
            <form action="{{ route('mahasiswa.thesis.exam.store', $thesis->id) }}" method="POST"
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
                        @if ($thesis->exam)
                        <input type="text" class="form-control" value="{{ optional($thesis->exam)->exam_type }}"
                            disabled readonly>
                        <input type="hidden" name="exam_type" value="{{ optional($thesis->exam)->exam_type }}">
                        @else
                        <select class="form-select @error('exam_type') is-invalid @enderror" id="exam_type"
                            name="exam_type" required>
                            <option value="">Pilih Jenis Ujian</option>
                            @foreach($examTypes as $type)
                            <option value="{{ $type->value }}" {{ old('exam_type')==$type->value ? 'selected' : '' }}>
                                {{ $type->label() }}
                            </option>
                            @endforeach
                        </select>
                        @error('exam_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>
                </div>

                <hr>

                <h5 class="mt-4 mb-3">Unggah Dokumen</h5>
                <div class="row">

                    @php
                    $docs = $thesis->documents->keyBy('document_type');
                    $allDocTypes = ['lembar_persetujuan', 'draft_skripsi', 'bukti_pembayaran'];

                    $isRevision = $thesis->exam && in_array($thesis->exam->status, ['revisi', 'ditolak']);

                    $docsToUpload = $isRevision
                    ? $docs->filter(fn($doc) => $doc->status === 'rejected')->pluck('document_type')->toArray()
                    : $allDocTypes;
                    @endphp

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

                                {{-- Tampilkan input file hanya jika statusnya rejected atau pendaftaran baru --}}
                                @if(in_array($docType, $docsToUpload))
                                <div class="my-3">
                                    <label for="{{ $docType }}" class="form-label">Unggah ulang (PDF)</label>
                                    <input type="file" class="form-control @error($docType) is-invalid @enderror"
                                        id="{{ $docType }}" name="{{ $docType }}">
                                    @error($docType)
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                @endif

                                {{-- Tampilkan catatan KTU jika ada --}}
                                @if($doc && $doc->notes)
                                <div class="alert alert-secondary mt-2 p-2">
                                    <small>Catatan KTU: {{ $doc->notes }}</small>
                                </div>
                                @endif

                                {{-- Tampilkan tombol lihat dokumen lama --}}
                                @if ($doc)
                                <p class="text-muted mt-1">Dokumen lama: <a
                                        href="{{ asset('storage/documents/' . $doc->file_path) }}" target="_blank"
                                        class="btn btn-sm btn-outline-primary"><i class="icon ni ni-eye me-1"></i>Lihat
                                        {{ $label
                                        }}</a></p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach

                </div>

                <button type="submit" class="btn btn-primary">
                    {{ $thesis->exam ? 'Ajukan Revisi Ujian' : 'Ajukan Pendaftaran Ujian' }}
                </button>
            </form>
        </div>
    </div>
</x-main-layout>