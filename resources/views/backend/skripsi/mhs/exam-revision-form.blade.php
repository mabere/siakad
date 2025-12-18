<x-main-layout>
    @section('title', 'Revisi Dokumen Pendaftaran Ujian')

    <div class="card">
        <div class="card-body">
            <h5>Revisi Dokumen Ujian Skripsi</h5>
            <p>Catatan dari KTU: <strong>{{ $thesis->exam->revisi_notes }}</strong></p>

            <form action="{{ route('student.thesis.exam.revision.submit', $thesis->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="lembar_persetujuan" class="form-label">Lembar Persetujuan (PDF)</label>
                    <input type="file" name="lembar_persetujuan" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="draft_skripsi" class="form-label">Draft Skripsi Lengkap (PDF)</label>
                    <input type="file" name="draft_skripsi" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary">Kirim Revisi</button>
            </form>
        </div>
    </div>
</x-main-layout>