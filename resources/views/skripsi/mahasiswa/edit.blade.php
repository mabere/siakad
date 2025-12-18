<x-main-layout>
    @section('title', 'Edit Skripsi')

    <div class="card mt-3">
        <div class="card-header">
            <h4>Edit Pengajuan Skripsi</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('mahasiswa.thesis.update', $thesis->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="title" class="form-label">Judul Skripsi</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $thesis->title) }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="supervisor_1_id" class="form-label">Dosen Pembimbing 1</label>
                    <select class="form-select @error('supervisor_1_id') is-invalid @enderror" id="supervisor_1_id" name="supervisor_1_id" required>
                        <option value="">Pilih Dosen Pembimbing 1</option>
                        @foreach($lecturers as $lecturer)
                            <option value="{{ $lecturer->id }}"
                                {{ old('supervisor_1_id', $existingSupervisors[0] ?? '') == $lecturer->id ? 'selected' : '' }}>
                                {{ $lecturer->user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('supervisor_1_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="supervisor_2_id" class="form-label">Dosen Pembimbing 2</label>
                    <select class="form-select @error('supervisor_2_id') is-invalid @enderror" id="supervisor_2_id" name="supervisor_2_id" required>
                        <option value="">Pilih Dosen Pembimbing 2</option>
                        @foreach($lecturers as $lecturer)
                            <option value="{{ $lecturer->id }}"
                                {{ old('supervisor_2_id', $existingSupervisors[1] ?? '') == $lecturer->id ? 'selected' : '' }}>
                                {{ $lecturer->user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('supervisor_2_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </form>
        </div>
    </div>
</x-main-layout>
