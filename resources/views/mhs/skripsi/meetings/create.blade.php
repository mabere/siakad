<x-main-layout>
    @section('title', 'Ajuan Bimbingan Skripsi')

    <div class="nk-content">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">Ajukan Bimbingan Skripsi</h3>
                            </div>
                            <div class="nk-block-head-content">
                                <a href="{{ route('student.thesis.supervision.index') }}"
                                    class="btn btn-outline-secondary">
                                    <em class="icon ni ni-arrow-left"></em>
                                    <span>Kembali</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="nk-block">
                        <div class="card">
                            <div class="card-body">
                                <form action="{{ route('student.thesis.supervision.meeting.store') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="supervisor_id" value="{{ $supervision->supervisor_id }}">

                                    <div class="row g-gs">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Pembimbing</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $supervision->supervisor->nama_dosen }}" readonly>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="meeting_date">Tanggal Bimbingan</label>
                                                <input type="datetime-local" class="form-control" id="meeting_date"
                                                    name="meeting_date" required>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-label" for="topic">Topik Bimbingan</label>
                                                <input type="text" class="form-control" id="topic" name="topic"
                                                    required>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-label" for="description">Deskripsi</label>
                                                <textarea class="form-control" id="description" name="description"
                                                    rows="4" required></textarea>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-label" for="attachment">Lampiran
                                                    (PDF/DOC/DOCX)</label>
                                                <input type="file" class="form-control" id="attachment"
                                                    name="attachment" accept=".pdf,.doc,.docx">
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary">
                                                    Ajukan Bimbingan
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-main-layout>