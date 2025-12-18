<!-- Modal Form untuk Tambah/Edit -->
<div class="modal fade" id="adminEventModal" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
        <form id="adminEventForm" class="modal-content">
            @csrf
            <input type="hidden" id="adminEventId" name="id">
            <div class="modal-header">
                <h5 class="modal-title">Form Kegiatan Akademik</h5>
                <a href="#" class="close" data-bs-dismiss="modal"><em class="icon ni ni-cross"></em></a>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <x-form.input label="Judul" name="title" id="adminTitle" required />
                    </div>
                    <div class="col-md-6">
                        <x-form.select label="Tahun Akademik" name="academic_year_id" id="adminAcademicYearId"
                            :options="$academicYears" />
                    </div>
                    <div class="col-md-6">
                        <x-form.input type="datetime-local" label="Tanggal Mulai" name="start_date" id="adminStartDate"
                            step="1" required />
                    </div>
                    <div class="col-md-6">
                        <x-form.input type="datetime-local" label="Tanggal Selesai" name="end_date" id="adminEndDate"
                            step="1" required />
                    </div>
                    <div class="col-12">
                        <x-form.textarea label="Deskripsi" name="description" id="adminDescription" />
                    </div>
                    <div class="col-md-6">
                        <x-form.input type="url" label="URL (opsional)" name="url" id="adminUrl" />
                    </div>
                    <div class="col-md-6">
                        <x-form.select label="Cakupan" name="visibility" id="adminVisibility"
                            :options="['public' => 'Publik', 'faculty' => 'Fakultas', 'department' => 'Prodi']"
                            required />
                    </div>
                    <div class="col-md-6" id="adminFacultyGroup" style="display:none;">
                        <x-form.select label="Fakultas" name="faculty_id" id="adminFacultyId" :options="$faculties" />
                    </div>
                    <div class="col-md-6" id="adminDepartmentGroup" style="display:none;">
                        <x-form.select label="Program Studi" name="department_id" id="adminDepartmentId"
                            :options="$departments" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" type="submit">Simpan</button>
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
            </div>
        </form>
    </div>
</div>