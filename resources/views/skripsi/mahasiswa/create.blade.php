<x-main-layout>
    @section('title', 'Ajukan Skripsi Baru')

    <div class="card mt-3">
        <div class="card-header">
            <h4>Formulir Pengajuan Skripsi</h4>
        </div>
        <div class="card-body">
            @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif
            @if (session('error'))
            <div class="alert alert-danger">
                <ul>
                    @if (is_array(session('error')))
                    @foreach (session('error') as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                    @else
                    <li>{{ session('error') }}</li>
                    @endif
                </ul>
            </div>
            @endif
            <form action="{{ route('mahasiswa.thesis.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="title" class="form-label">Judul Skripsi</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title"
                        value="{{ old('title') }}" required>
                    @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="supervisor_1_id" class="form-label">Dosen Pembimbing 1</label>
                    <select class="form-select @error('supervisor_1_id') is-invalid @enderror" id="supervisor_1_id"
                        name="supervisor_1_id" required>
                        <option value="">Pilih Dosen Pembimbing 1</option>
                        @foreach($lecturers as $lecturer)
                        <option value="{{ $lecturer->id }}" {{ old('supervisor_1_id')==$lecturer->id ? 'selected' : ''
                            }}>
                            {{ $lecturer->nama_dosen }}
                        </option>
                        @endforeach
                    </select>
                    @error('supervisor_1_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="supervisor_2_id" class="form-label">Dosen Pembimbing 2</label>
                    <select class="form-select @error('supervisor_2_id') is-invalid @enderror" id="supervisor_2_id"
                        name="supervisor_2_id" required>
                        <option value="">Pilih Dosen Pembimbing 2</option>
                        @foreach($lecturers as $lecturer)
                        <option value="{{ $lecturer->id }}" {{ old('supervisor_2_id')==$lecturer->id ? 'selected' : ''
                            }}>
                            {{ $lecturer->nama_dosen }}
                        </option>
                        @endforeach
                    </select>
                    @error('supervisor_2_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Ajukan Skripsi</button>
            </form>
        </div>
    </div>
</x-main-layout>
