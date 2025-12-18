<x-main-layout>
    @section('title', 'Detail Verifikasi Ujian Skripsi')

    {{-- Notifikasi --}}
    @if ($errors->any())
    <div class="alert alert-danger">
        <h6>Ada masalah dengan verifikasi:</h6>
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif
    @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">Detail Verifikasi Ujian Skripsi</h5>
        </div>

        <div class="card-body">
            <div class="mb-4">
                <h6 class="fw-bold text-primary mb-3">Informasi Mahasiswa</h6>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-2">
                            <span class="text-muted">Nama</span>
                            <p class="fw-bold">{{ $exam->thesis->student->user->name }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-2">
                            <span class="text-muted">NIM</span>
                            <p class="fw-bold">{{ $exam->thesis->student->nim }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-2">
                            <span class="text-muted">Program Studi</span>
                            <p class="fw-bold">{{ $exam->thesis->student->department->nama ?? '-' }}</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="mb-2">
                            <span class="text-muted">Judul Skripsi</span>
                            <p class="fw-bold">{{ $exam->thesis->title }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            {{-- LOGIKA KONDISIONAL UNTUK MENAMPILKAN FORMULIR --}}
            @if(in_array($exam->status, ['diajukan', 'revisi']))
            <form action="{{ route('ktu.thesis.exam.verify', $exam->id) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <h6 class="fw-bold text-primary mb-3">Dokumen Ujian</h6>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Jenis Dokumen</th>
                                    <th>Status Saat Ini</th>
                                    <th>Lihat Dokumen</th>
                                    <th>Verifikasi</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($exam->documents as $doc)
                                <tr>
                                    <td>{{ ucwords(str_replace('_', ' ', $doc->document_type)) }}</td>
                                    <td>
                                        @php
                                        $badgeColor = [
                                        'pending' => 'warning',
                                        'verifikasi' => 'success',
                                        'revisi' => 'danger'
                                        ];
                                        @endphp
                                        <span class="badge bg-{{ $badgeColor[$doc->status] ?? 'secondary' }}">
                                            {{ ucwords($doc->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($doc->file_path)
                                        <a href="{{ Storage::url($doc->file_path) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            <em class="icon ni ni-eye me-1"></em> Cek Dokumen
                                        </a>
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td>
                                        <select name="doc_statuses[{{ $doc->id }}]" class="form-select form-select-sm"
                                            required>
                                            <option value="pending" {{ $doc->status === 'pending' ? 'selected' : ''
                                                }}>Pending</option>
                                            <option value="verifikasi" {{ $doc->status === 'verifikasi' ? 'selected' :
                                                '' }}>Setujui</option>
                                            <option value="revisi" {{ $doc->status === 'revisi' ? 'selected' : ''
                                                }}>Revisi</option>
                                        </select>
                                    </td>
                                    <td>
                                        <textarea name="doc_notes[{{ $doc->id }}]" class="form-control form-control-sm"
                                            rows="1"
                                            placeholder="Catatan opsional...">{{ old("doc_notes.$doc->id", $doc->notes) }}</textarea>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <hr class="my-4">

                <div class="m-3 p-3">
                    <div class="mb-4">
                        <label for="notes" class="form-label fw-bold text-primary">Catatan Verifikasi Ujian</label>
                        <textarea name="notes" class="form-control" rows="3"
                            placeholder="Catatan opsional untuk ujian secara keseluruhan...">{{ old('notes', $exam->revisi_notes) }}</textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('ktu.thesis.exam.index') }}" class="btn p-3 btn-danger">
                            <em class="icon ni ni-reply"></em> Batal
                        </a>
                        <button type="submit" name="action" value="revisi" class="btn p-3 btn-warning">
                            <em class="icon ni ni-undo"></em> Minta Revisi
                        </button>
                        <button type="submit" name="action" value="setujui" class="btn p-3 btn-success">
                            <em class="icon ni ni-check-circle"></em> Verifikasi
                        </button>
                    </div>
                </div>
            </form>
            @else
            {{-- TAMPILAN JIKA FORMULIR TIDAK DIBUTUHKAN --}}
            <div class="mb-4">
                <h6 class="fw-bold text-primary mb-3">Status Verifikasi & Dokumen Ujian</h6>
                <div class="alert alert-info">
                    Verifikasi ujian telah selesai. Status saat ini: **{{ strtoupper($exam->status) }}**.
                    @if ($exam->revisi_notes)
                    <p class="mt-2 mb-0">Catatan Verifikasi Ujian: {{ $exam->revisi_notes }}</p>
                    @endif
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Jenis Dokumen</th>
                                <th>Status Dokumen</th>
                                <th>Catatan</th>
                                <th>Lihat Dokumen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($exam->documents as $doc)
                            <tr>
                                <td>{{ ucwords(str_replace('_', ' ', $doc->document_type)) }}</td>
                                <td>
                                    @php
                                    $badgeColor = [
                                    'pending' => 'warning',
                                    'verifikasi' => 'success',
                                    'revisi' => 'danger'
                                    ];
                                    @endphp
                                    <span class="badge bg-{{ $badgeColor[$doc->status] ?? 'secondary' }}">
                                        {{ ucwords($doc->status) }}
                                    </span>
                                </td>
                                <td>{{ $doc->notes ?? '-' }}</td>
                                <td>
                                    @if($doc->file_path)
                                    <a href="{{ Storage::url($doc->file_path) }}" target="_blank"
                                        class="btn btn-sm btn-outline-primary">
                                        <em class="icon ni ni-eye me-1"></em> Cek Dokumen
                                    </a>
                                    @else
                                    -
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-main-layout>
