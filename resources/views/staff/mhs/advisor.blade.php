<x-main-layout>
    @section('title', 'Penugasan Dosen PA')

    <div class="container mt-4">
        <div class="nk-block">
            <div class="nk-block-head">
                <h4 class="nk-block-title text-lg text-primary">@yield('title')<h43>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Mahasiswa Belum Memiliki Dosen PA</h6>
                </div>

                <div class="card-body">
                    @if ($students->isEmpty())
                    <div class="alert alert-info d-flex align-items-center">
                        <em class="icon ni ni-alert-circle text-primary me-2"></em>
                        <span>Tidak ada mahasiswa yang belum memiliki Dosen PA.</span>
                    </div>
                    @else
                    <form method="POST" action="{{ route('staff.mahasiswa.advisor.store') }}" class="needs-validation"
                        novalidate>
                        @csrf

                        <div class="mb-3">
                            <h6 class="text-muted"><em class="icon ni ni-users"></em> Pilih Mahasiswa</h6>
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="select-all"></th>
                                        <th>Nama Mahasiswa</th>
                                        <th>NIM</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($students as $student)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="form-check-input student-checkbox"
                                                name="student_ids[]" value="{{ $student->id }}">
                                        </td>
                                        <td>{{ $student->nama_mhs }}</td>
                                        <td>{{ $student->nim }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @error('student_ids') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <h6 class="text-muted"><em class="icon ni ni-user"></em> Pilih Dosen PA</h6>
                            <select class="form-select select2" name="advisor_id" data-placeholder="Pilih Dosen PA">
                                <option value="">Pilih Dosen</option>
                                @foreach ($lecturers as $lecturer)
                                <option value="{{ $lecturer->id }}">{{ $lecturer->nama_dosen }}</option>
                                @endforeach
                            </select>
                            @error('advisor_id') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-primary">
                                <em class="icon ni ni-user-add"></em> Tugaskan Dosen PA
                            </button>
                        </div>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Select2 for better dropdown experience
            $('.select2').select2();

            // Select all checkbox functionality
            document.getElementById("select-all").addEventListener("change", function () {
                let checkboxes = document.querySelectorAll(".student-checkbox");
                checkboxes.forEach(checkbox => checkbox.checked = this.checked);
            });
        });
    </script>
    @endpush
</x-main-layout>