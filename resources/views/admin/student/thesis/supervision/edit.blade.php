<x-main-layout>
    @section('title', 'Edit Pembimbingan Skripsi')

    <div class="nk-content">
        @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">Edit Bimbingan Skripsi</h3>
                            </div>
                        </div>
                    </div>

                    <div class="nk-block">
                        <div class="card">
                            <div class="card-body">
                                <form action="{{ route('admin.thesis.supervision.update', $supervision->id) }}"
                                    method="POST" class="form-validate">
                                    @csrf
                                    @method('PUT')

                                    <div class="row g-gs">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Mahasiswa</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $supervision->student->nim }} - {{ $supervision->student->nama_mhs }}"
                                                    readonly>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Program Studi</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $supervision->student->department->nama }}" readonly>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="supervisor1_id">Pembimbing 1</label>
                                                <select class="form-select js-select2" name="supervisor1_id" required>
                                                    <option value="">Pilih Pembimbing 1</option>
                                                    @foreach($lecturers as $lecturer)
                                                    <option value="{{ $lecturer->id }}" {{ $supervision->
                                                        supervisor_id == $lecturer->id ? 'selected' : '' }}>
                                                        {{ $lecturer->nama_dosen }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="supervisor2_id">Pembimbing 2</label>
                                                <select class="form-select js-select2" name="supervisor2_id" required>
                                                    <option value="">Pilih Pembimbing 2</option>
                                                    @foreach($lecturers as $lecturer)
                                                    <option value="{{ $lecturer->id }}" {{ $secondarySupervisor->
                                                        supervisor_id == $lecturer->id ? 'selected' : '' }}>
                                                        {{ $lecturer->nama_dosen }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="status">Status</label>
                                                <select class="form-select" name="status" required>
                                                    <option value="active" {{ $supervision->status == 'active' ?
                                                        'selected' : '' }}>
                                                        Aktif
                                                    </option>
                                                    <option value="completed" {{ $supervision->status == 'completed'
                                                        ? 'selected' : '' }}>
                                                        Selesai
                                                    </option>
                                                    <option value="terminated" {{ $supervision->status ==
                                                        'terminated' ? 'selected' : '' }}>
                                                        Diberhentikan
                                                    </option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary"><i
                                                        class="icon ni ni-save-fill"></i></button>
                                                <a href="{{ route('admin.thesis.supervision.index') }}"
                                                    class="btn btn-outline-secondary">Batal</a>
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

    @push('scripts')
    <script>
        $(document).ready(function() {
                $('.js-select2').select2({
                    width: '100%'
                });
    
                // Prevent selecting same supervisor
                $('select[name="supervisor1_id"], select[name="supervisor2_id"]').on('change', function() {
                    let supervisor1 = $('select[name="supervisor1_id"]').val();
                    let supervisor2 = $('select[name="supervisor2_id"]').val();
    
                    if (supervisor1 && supervisor2 && supervisor1 === supervisor2) {
                        alert('Pembimbing 1 dan Pembimbing 2 tidak boleh sama.');
                        $(this).val('').trigger('change');
                    }
                });
            });
    </script>
    @endpush
</x-main-layout>