<x-main-layout>
    @section('title', 'Pendaftaran Ujian Skripsi')

    <div class="card">
        <div class="card-body">
            <h4>Form Pendaftaran Ujian</h4>

            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('student.thesis.exam.register', $thesis->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <div class="form-control-wrap">
                        <div class="form-file">
                            <label class="form-label" for="lembar_persetujuan">Lembar Persetujuan (PDF)</label>
                            <input type="file" name="lembar_persetujuan" class="form-control" required id="customFile">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-control-wrap">
                        <div class="form-file">
                            <label class="form-label" for="lembar_persetujuan">Lembar Persetujuan (PDF)</label>
                            <input type="file" name="bukti_pembayaran" class="form-control" required id="customFile">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-control-wrap">
                        <div class="form-file">
                            <label class="form-label" for="draft_skripsi">Lembar Persetujuan (PDF)</label>
                            <input type="file" name="draft_skripsi" class="form-control" required id="customFile">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Ajukan Ujian</button>
            </form>
        </div>
    </div>
</x-main-layout>