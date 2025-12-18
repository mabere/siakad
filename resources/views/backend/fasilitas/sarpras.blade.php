<x-main-layout>
    @section('title', 'Data Sarana dan Prasaran')
    <div class="components-preview wide-lg mx-auto">
        <div class="nk-block nk-block-lg">
            <div class="nk-block-head">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h4 class="nk-block-title">Daftar Sarana dan Prasarana</h4>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <div class="toggle-expand-content">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#createModal">
                                    <em class="icon ni ni-plus"></em>
                                    <span>Tambah</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    @if (session('success'))
                    <div class="alert alert-icon alert-success" role="alert">
                        <em class="icon ni ni-check-circle"></em>
                        <strong>{{ session('success') }}</strong>
                    </div>
                    @endif
                    <table class="datatable-init nowrap table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Nama Sarpras</th>
                                <th>Kategori</th>
                                <th>Fungsi</th>
                                <th>Kondisi</th>
                                <th>Jumlah</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($sarpras as $item)
                            <tr>
                                <td>{{ $loop->iteration }}.</td>
                                <td>{{ $item->nama }}</td>
                                <td>{{ $item->kategori }}</td>
                                <td>{{ $item->fungsi }}</td>
                                <td>
                                    @switch($item->kondisi)
                                    @case('Baik')
                                    <span class="badge badge-dot bg-success">{{ $item->kondisi }}</span>
                                    @break
                                    @case('Rusak Ringan')
                                    <span class="badge badge-dot bg-warning">{{ $item->kondisi }}</span>
                                    @break
                                    @case('Rusak Berat')
                                    <span class="badge badge-dot bg-danger">{{ $item->kondisi }}</span>
                                    @break
                                    @default
                                    {{ $item->kondisi }}
                                    @endswitch
                                </td>
                                <td>{{ $item->quantity }}</td>
                                <td>
                                    <div class="dropdown">
                                        <a class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown">
                                            <em class="icon ni ni-more-h"></em>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <ul class="link-list-opt no-bdr">
                                                <li>
                                                    <button type="button" class="dropdown-item" data-bs-toggle="modal"
                                                        data-bs-target="#showModal" data-nama="{{ $item->nama }}"
                                                        data-kategori="{{ $item->kategori }}"
                                                        data-fungsi="{{ $item->fungsi }}"
                                                        data-kondisi="{{ $item->kondisi }}"
                                                        data-quantity="{{ $item->quantity }}"
                                                        data-description="{{ $item->description }}"
                                                        data-created_at="{{ $item->created_at->format('d M Y, H:i') }}">
                                                        <em class="icon ni ni-eye"></em><span>View</span>
                                                    </button>
                                                </li>
                                                <li>
                                                    <button type="button" class="dropdown-item" data-bs-toggle="modal"
                                                        data-bs-target="#editModal" data-id="{{ $item->id }}"
                                                        data-nama="{{ $item->nama }}"
                                                        data-kategori="{{ $item->kategori }}"
                                                        data-fungsi="{{ $item->fungsi }}"
                                                        data-kondisi="{{ $item->kondisi }}"
                                                        data-quantity="{{ $item->quantity }}"
                                                        data-description="{{ $item->description }}">
                                                        <em class="icon ni ni-edit"></em><span>Edit</span>
                                                    </button>
                                                </li>
                                                <li>
                                                    <form id="delete-form-{{ $item->id }}"
                                                        action="{{ route('admin.sarpras.destroy', $item->id) }}"
                                                        method="post">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item"
                                                            onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                            <em class="icon ni ni-trash"></em><span>Delete</span>
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">Data kosong</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">Tambah Sarana Prasarana</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.sarpras.store') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="row gy-4">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label" for="nama">Nama Sarana/Prasarana</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" id="nama" name="nama" required>
                                    </div>
                                    @error('nama')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label" for="kategori">Kategori</label>
                                    <div class="form-control-wrap">
                                        <select class="form-control" id="kategori" name="kategori" required>
                                            <option value="">Pilih Kategori</option>
                                            <option value="Sarana">Sarana</option>
                                            <option value="Prasarana">Prasarana</option>
                                        </select>
                                    </div>
                                    @error('kategori')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label" for="fungsi">Fungsi</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" id="fungsi" name="fungsi" required>
                                    </div>
                                    @error('fungsi')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label" for="kondisi">Kondisi</label>
                                    <div class="form-control-wrap">
                                        <select class="form-control" id="kondisi" name="kondisi" required>
                                            <option value="">Pilih Kondisi</option>
                                            <option value="Baik">Baik</option>
                                            <option value="Rusak Ringan">Rusak Ringan</option>
                                            <option value="Rusak Berat">Rusak Berat</option>
                                        </select>
                                    </div>
                                    @error('kondisi')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label" for="quantity">Jumlah</label>
                                    <div class="form-control-wrap">
                                        <input type="number" class="form-control" id="quantity" name="quantity" min="0"
                                            required>
                                    </div>
                                    @error('quantity')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label" for="description">Keterangan</label>
                                    <div class="form-control-wrap">
                                        <input class="form-control" id="description" name="description">
                                    </div>
                                    @error('description')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Sarana Prasarana</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row gy-4">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label" for="edit_nama">Nama Sarana/Prasarana</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" id="edit_nama" name="nama" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label" for="edit_kategori">Kategori</label>
                                    <div class="form-control-wrap">
                                        <select class="form-control form-select" id="edit_kategori" name="kategori"
                                            required>
                                            <option value="">Pilih Kategori</option>
                                            <option value="Sarana">Sarana</option>
                                            <option value="Prasarana">Prasarana</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label" for="edit_fungsi">Fungsi</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" id="edit_fungsi" name="fungsi" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label" for="edit_kondisi">Kondisi</label>
                                    <div class="form-control-wrap">
                                        <select class="form-control form-select" id="edit_kondisi" name="kondisi"
                                            required>
                                            <option value="">Pilih Kondisi</option>
                                            <option value="Baik">Baik</option>
                                            <option value="Rusak Ringan">Rusak Ringan</option>
                                            <option value="Rusak Berat">Rusak Berat</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label" for="edit_quantity">Jumlah</label>
                                    <div class="form-control-wrap">
                                        <input type="number" class="form-control" id="edit_quantity" name="quantity"
                                            min="0" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label" for="edit_description">Keterangan</label>
                                    <div class="form-control-wrap">
                                        <textarea class="form-control" id="edit_description"
                                            name="description"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Show Modal -->
    <div class="modal fade" id="showModal" tabindex="-1" aria-labelledby="showModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="showModalLabel">Detail Sarana Prasarana</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="nk-block">
                        <div class="profile-ud-list">
                            <div class="profile-ud-item">
                                <div class="profile-ud wider">
                                    <span class="profile-ud-label">Nama</span>
                                    <span class="profile-ud-value" id="show_nama"></span>
                                </div>
                            </div>
                            <div class="profile-ud-item">
                                <div class="profile-ud wider">
                                    <span class="profile-ud-label">Kategori</span>
                                    <span class="profile-ud-value" id="show_kategori"></span>
                                </div>
                            </div>
                            <div class="profile-ud-item">
                                <div class="profile-ud wider">
                                    <span class="profile-ud-label">Fungsi</span>
                                    <span class="profile-ud-value" id="show_fungsi"></span>
                                </div>
                            </div>
                            <div class="profile-ud-item">
                                <div class="profile-ud wider">
                                    <span class="profile-ud-label">Kondisi</span>
                                    <span class="profile-ud-value" id="show_kondisi"></span>
                                </div>
                            </div>
                            <div class="profile-ud-item">
                                <div class="profile-ud wider">
                                    <span class="profile-ud-label">Jumlah</span>
                                    <span class="profile-ud-value" id="show_quantity"></span>
                                </div>
                            </div>
                            <div class="profile-ud-item">
                                <div class="profile-ud wider">
                                    <span class="profile-ud-label">Dibuat Pada</span>
                                    <span class="profile-ud-value" id="show_created_at"></span>
                                </div>
                            </div>
                            <div class="profile-ud-item">
                                <div class="profile-ud wider">
                                    <span class="profile-ud-label">Keterangan</span>
                                    <span class="profile-ud-value" id="show_description"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Edit Modal Handler
        document.getElementById('editModal').addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            var nama = button.getAttribute('data-nama');
            var kategori = button.getAttribute('data-kategori');
            var fungsi = button.getAttribute('data-fungsi');
            var kondisi = button.getAttribute('data-kondisi');
            var quantity = button.getAttribute('data-quantity');
            var description = button.getAttribute('data-description');

            var modal = this;
            modal.querySelector('#edit_nama').value = nama;
            modal.querySelector('#edit_kategori').value = kategori;
            modal.querySelector('#edit_fungsi').value = fungsi;
            modal.querySelector('#edit_kondisi').value = kondisi;
            modal.querySelector('#edit_quantity').value = quantity;
            modal.querySelector('#edit_description').value = description;

            // Update form action
            var form = modal.querySelector('#editForm');
            form.action = '{{ route("admin.sarpras.update", "") }}/' + id;
        });

        // Show Modal Handler
        document.getElementById('showModal').addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var nama = button.getAttribute('data-nama');
            var kategori = button.getAttribute('data-kategori');
            var fungsi = button.getAttribute('data-fungsi');
            var kondisi = button.getAttribute('data-kondisi');
            var quantity = button.getAttribute('data-quantity');
            var description = button.getAttribute('data-description');
            var created_at = button.getAttribute('data-created_at');

            var modal = this;
            modal.querySelector('#show_nama').textContent = nama;
            modal.querySelector('#show_kategori').textContent = kategori;
            modal.querySelector('#show_fungsi').textContent = fungsi;
            modal.querySelector('#show_quantity').textContent = quantity;
            modal.querySelector('#show_created_at').textContent = created_at;
            modal.querySelector('#show_description').textContent = description || '-';

            // Format kondisi with badge
            var kondisiBadge = '';
            switch(kondisi) {
                case 'Baik':
                    kondisiBadge = '<span class="badge badge-dot bg-success">Baik</span>';
                    break;
                case 'Rusak Ringan':
                    kondisiBadge = '<span class="badge badge-dot bg-warning">Rusak Ringan</span>';
                    break;
                case 'Rusak Berat':
                    kondisiBadge = '<span class="badge badge-dot bg-danger">Rusak Berat</span>';
                    break;
                default:
                    kondisiBadge = kondisi;
            }
            modal.querySelector('#show_kondisi').innerHTML = kondisiBadge;
        });
    </script>
    @endpush
</x-main-layout>