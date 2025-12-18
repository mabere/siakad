<x-main-layout>
    @section('title', 'Tambah Mahasiswa ke Kelas')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Tambah Mahasiswa ke Kelas: {{ $kelas->name }}</h4>
                </div>
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                    @endif

                    <form action="{{ route('staff.kelas.store-students', $kelas->id) }}" method="POST">
                        @csrf
                        <div class="table-responsive">
                            <table id="zero_config" class="table table-striped table-bordered">
                                <thead class="bg-primary">
                                    <tr>
                                        <th><input type="checkbox" id="select-all"></th>
                                        <th>No</th>
                                        <th>NIM</th>
                                        <th>Nama</th>
                                        <th>Program Studi</th>
                                        <th>Angkatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($mahasiswa as $index => $student)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="student_ids[]" value="{{ $student->id }}"
                                                class="student-checkbox">
                                        </td>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td class="text-center">{{ $student->nim }}</td>
                                        <td class="text-left">{{ $student->nama_mhs }}</td>
                                        <td>{{ $student->department->nama }}</td>
                                        <td class="text-center">-</td> <!-- Angkatan kosong karena belum ada kelas -->
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-danger text-center">Tidak ada mahasiswa yang belum
                                            memiliki kelas di program studi ini.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('staff.kelas.show', $kelas->id) }}" class="btn btn-secondary">
                                <i class="fa fa-arrow-left"></i> Kembali
                            </a>
                            @if ($mahasiswa->isNotEmpty())
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-plus"></i> Tambah ke Kelas
                            </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Select all checkbox functionality
        document.getElementById('select-all').addEventListener('change', function() {
            document.querySelectorAll('.student-checkbox').forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    </script>
    @endpush
</x-main-layout>