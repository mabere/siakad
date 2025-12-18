<x-main-layout>
    @section('title', 'Manajemen Kegiatan Akademik')
    <x-custom.sweet-alert />
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Manajemen Kegiatan Akademik</h3>
                <div class="nk-block-des text-soft">
                    <p>Daftar seluruh kegiatan akademik di lingkungan kampus.</p>
                </div>
            </div>
            <div class="nk-block-head-content">
                <button class="btn btn-primary" onclick="openCreateModal()">
                    <em class="icon ni ni-plus"></em>
                    <span>Tambah Kegiatan</span>
                </button>
            </div>
        </div>
    </div>

    <div class="card card-bordered">
        <div class="card-inner">
            <div class="table-responsive">
                <table class="datatable-init table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Judul</th>
                            <th>Waktu</th>
                            <th>Status</th>
                            <th>Cakupan</th>
                            <th>Dimbuat Oleh</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($events as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->title }}</td>
                            <td>{{ $item->start_date->format('Y-m-d H:i') }} - {{ $item->end_date->format('Y-m-d H:i')
                                }}</td>
                            <td>
                                <span class="badge
                                    @if($item->status === 'published') badge-success
                                    @elseif($item->status === 'draft') badge-warning
                                    @else badge-danger @endif">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td>{{ ucfirst($item->visibility) }}</td>
                            <td>{{ $item->createdBy->name ?? '-' }}</td>
                            <td>
                                <ul class="nk-tb-actions gx-1">
                                    <li>
                                        <button class="btn btn-icon btn-trigger"
                                            onclick="openEditModal({{ $item->id }})">
                                            <em class="icon ni ni-edit"></em>
                                        </button>
                                    </li>
                                    @if($item->status === 'draft')
                                    <li>
                                        <button class="btn btn-icon btn-trigger text-success"
                                            onclick="confirmPublishModal({{ $item->id }})">
                                            <em class="icon ni ni-upload-cloud"></em>
                                        </button>
                                    </li>
                                    @elseif($item->status === 'published')
                                    <li>
                                        <button class="btn btn-icon btn-trigger text-warning"
                                            onclick="confirmUnpublishModal({{ $item->id }})">
                                            <em class="icon ni ni-undo"></em>
                                        </button>
                                    </li>
                                    @endif
                                    <li>
                                        <button class="btn btn-icon btn-trigger text-danger"
                                            onclick="confirmDeleteModal({{ $item->id }})">
                                            <em class="icon ni ni-trash"></em>
                                        </button>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah/Edit Kegiatan -->
    <div class="modal fade" id="adminEventModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                {{-- <form method="POST" action="" id="editForm">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title">Edit Kegiatan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="edit_title" class="form-label">Judul</label>
                                <input type="text" name="title" id="edit_title" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label for="edit_academic_year_id" class="form-label">Tahun Akademik</label>
                                <select name="academic_year_id" id="edit_academic_year_id" class="form-select" required>
                                    @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}">{{ $year->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="edit_faculty_id" class="form-label">Fakultas</label>
                                <select name="faculty_id" id="edit_faculty_id" class="form-select" required>
                                    @foreach($faculties as $faculty)
                                    <option value="{{ $faculty->id }}">{{ $faculty->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="edit_department_id" class="form-label">Program Studi (Opsional)</label>
                                <select name="department_id" id="edit_department_id" class="form-select">
                                    <option value="">-</option>
                                    @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="edit_start_date" class="form-label">Tanggal Mulai</label>
                                <input type="datetime-local" name="start_date" id="edit_start_date" class="form-control"
                                    required>
                            </div>

                            <div class="col-md-6">
                                <label for="edit_end_date" class="form-label">Tanggal Selesai</label>
                                <input type="datetime-local" name="end_date" id="edit_end_date" class="form-control"
                                    required>
                            </div>

                            <div class="col-md-6">
                                <label for="edit_visibility" class="form-label">Visibilitas</label>
                                <select name="visibility" id="edit_visibility" class="form-select" required>
                                    <option value="public">Publik</option>
                                    <option value="faculty">Fakultas</option>
                                    <option value="department">Program Studi</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="edit_status" class="form-label">Status</label>
                                <select name="status" id="edit_status" class="form-select" required>
                                    <option value="draft">Draft</option>
                                    <option value="published">Published</option>
                                    <option value="cancelled">Dibatalkan</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label for="edit_url" class="form-label">URL (Link Acara)</label>
                                <input type="url" name="url" id="edit_url" class="form-control">
                            </div>

                            <div class="col-12">
                                <label for="edit_description" class="form-label">Deskripsi</label>
                                <textarea name="description" id="edit_description" class="form-control"
                                    rows="4"></textarea>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form> --}}
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" tabindex="-1" role="dialog" id="editModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form method="POST" action="" id="editForm">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title">Edit Kegiatan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="edit_title" class="form-label">Judul</label>
                                <input type="text" name="title" id="edit_title" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label for="edit_academic_year_id" class="form-label">Tahun Akademik</label>
                                <select name="academic_year_id" id="edit_academic_year_id" class="form-select" required>
                                    @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}">{{ $year->ta }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="edit_faculty_id" class="form-label">Fakultas</label>
                                <select name="faculty_id" id="edit_faculty_id" class="form-select" required>
                                    @foreach($faculties as $faculty)
                                    <option value="{{ $faculty->id }}">{{ $faculty->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="edit_department_id" class="form-label">Program Studi (Opsional)</label>
                                <select name="department_id" id="edit_department_id" class="form-select">
                                    <option value="">-</option>
                                    @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="edit_start_date" class="form-label">Tanggal Mulai</label>
                                <input type="datetime-local" name="start_date" id="edit_start_date" class="form-control"
                                    required>
                            </div>

                            <div class="col-md-6">
                                <label for="edit_end_date" class="form-label">Tanggal Selesai</label>
                                <input type="datetime-local" name="end_date" id="edit_end_date" class="form-control"
                                    required>
                            </div>

                            <div class="col-md-6">
                                <label for="edit_visibility" class="form-label">Visibilitas</label>
                                <select name="visibility" id="edit_visibility" class="form-select" required>
                                    <option value="public">Publik</option>
                                    <option value="faculty">Fakultas</option>
                                    <option value="department">Program Studi</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="edit_status" class="form-label">Status</label>
                                <select name="status" id="edit_status" class="form-select" required>
                                    <option value="draft">Draft</option>
                                    <option value="published">Published</option>
                                    <option value="cancelled">Dibatalkan</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label for="edit_url" class="form-label">URL (Link Acara)</label>
                                <input type="url" name="url" id="edit_url" class="form-control">
                            </div>

                            <div class="col-12">
                                <label for="edit_description" class="form-label">Deskripsi</label>
                                <textarea name="description" id="edit_description" class="form-control"
                                    rows="4"></textarea>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Publish -->
    <div class="modal fade" id="publishModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="publishForm" method="POST">
                @csrf
                @method('POST')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Konfirmasi Publish</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Anda yakin ingin mem-publish kegiatan ini?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Ya, Publish</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Unpublish -->
    <div class="modal fade" id="unpublishModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="unpublishForm" method="POST">
                @csrf
                @method('POST')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Konfirmasi Unpublish</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Anda yakin ingin membatalkan publish kegiatan ini?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning">Ya, Unpublish</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Hapus -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Konfirmasi Hapus</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Anda yakin ingin menghapus kegiatan ini?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
        // Inisialisasi modal menggunakan kelas Bootstrap 5
        const editModalElement = document.getElementById('editModal');
        const editModal = new bootstrap.Modal(editModalElement);

        // Modal lainnya
        const createModal = new bootstrap.Modal(document.getElementById('adminEventModal'));
        const publishModal = new bootstrap.Modal(document.getElementById('publishModal'));
        const unpublishModal = new bootstrap.Modal(document.getElementById('unpublishModal'));
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

        window.openCreateModal = function() {
            // ... (kode Anda untuk modal buat) ...
            createModal.show();
        };

        // Fungsi yang diperbaiki untuk membuka modal edit
        window.openEditModal = function(id) {
            axios.get(`/calendar/${id}/edit`) // Ganti dengan endpoint yang mengambil data untuk edit
                .then(response => {
                    const data = response.data;
                    const form = document.getElementById('editForm');

                    // Isi form dengan data yang diterima
                    document.getElementById('edit_title').value = data.title;
                    document.getElementById('edit_academic_year_id').value = data.academic_year_id;
                    document.getElementById('edit_faculty_id').value = data.faculty_id;
                    document.getElementById('edit_department_id').value = data.department_id;

                    // Format tanggal agar sesuai dengan input datetime-local
                    document.getElementById('edit_start_date').value = new Date(data.start_date).toISOString().slice(0, 16);
                    document.getElementById('edit_end_date').value = new Date(data.end_date).toISOString().slice(0, 16);

                    document.getElementById('edit_visibility').value = data.visibility;
                    document.getElementById('edit_status').value = data.status;
                    document.getElementById('edit_url').value = data.url;
                    document.getElementById('edit_description').value = data.description;

                    // Atur action form untuk update
                    form.action = `/calendar/${id}`;

                    // Tampilkan modal edit
                    editModal.show();
                })
                .catch(error => {
                    console.error('Ada kesalahan saat mengambil data:', error);
                    alert('Gagal memuat data. Mohon coba lagi.');
                });
        };

        window.confirmPublishModal = function(id) {
            document.getElementById('publishForm').action = `/calendar/${id}/publish`;
            publishModal.show();
        };

        window.confirmUnpublishModal = function(id) {
            document.getElementById('unpublishForm').action = `/calendar/${id}/unpublish`;
            unpublishModal.show();
        };

        window.confirmDeleteModal = function(id) {
            document.getElementById('deleteForm').action = `/calendar/${id}`;
            deleteModal.show();
        };

        // Tambahkan event listener terpisah untuk form edit
        document.getElementById('editForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const formData = new FormData(form);

            // Karena kita pakai @method('PUT'), data perlu diubah
            formData.append('_method', 'PUT');

            axios.post(form.action, formData) // Menggunakan POST karena method PUT di-spoof
                .then(() => location.reload())
                .catch(error => {
                    console.error('Ada kesalahan saat menyimpan perubahan:', error);
                    alert('Gagal menyimpan perubahan. Periksa kembali input Anda.');
                });
        });
    });
    </script>
    @endpush
</x-main-layout>
