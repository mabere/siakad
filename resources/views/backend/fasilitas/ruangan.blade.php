<x-main-layout>
    @section('title', 'Data Ruangan')
    <div class="components-preview wide-lg mx-auto">
        <div class="nk-block nk-block-lg">
            <div class="nk-block-head">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h4 class="nk-block-title">Daftar Ruangan</h4>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <div class="toggle-expand-content">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#createModal">
                                    <span>Add</span>
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
                                <th>Nama Ruangan</th>
                                <th>Gedung</th>
                                <th>Nomor</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($ruangan as $item)
                            <tr>
                                <td>{{ $loop->iteration }}.</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->building->nama }}</td>
                                <td>{{ $item->nomor }}</td>
                                <td>
                                    <form action="{{ route('admin.ruangan.destroy', $item->id) }}" method="post"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal"
                                            data-bs-target="#showModal" data-name="{{ $item->name }}"
                                            data-building="{{ $item->building->nama }}" data-nomor="{{ $item->nomor }}">
                                            <em class="icon ni ni-eye"></em>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                            data-bs-target="#editModal" data-id="{{ $item->id }}"
                                            data-name="{{ $item->name }}" data-building-id="{{ $item->building_id }}"
                                            data-nomor="{{ $item->nomor }}">
                                            <em class="icon ni ni-edit"></em>
                                        </button>
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Are you sure?')">
                                            <em class="icon ni ni-trash-fill"></em>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5">Data kosong.</td>
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
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">Tambah Data Ruangan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.ruangan.store') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Ruangan</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                                required>
                            @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="building_id" class="form-label">Gedung</label>
                            <select class="form-control form-select @error('building_id') is-invalid @enderror"
                                name="building_id" required>
                                <option value="">Pilih Gedung</option>
                                @foreach ($building as $item)
                                <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                @endforeach
                            </select>
                            @error('building_id') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="nomor" class="form-label">Nomor</label>
                            <input type="text" class="form-control @error('nomor') is-invalid @enderror" name="nomor"
                                required>
                            @error('nomor') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Data Ruangan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Nama Ruangan</label>
                            <input type="text" id="edit_name" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_building_id" class="form-label">Gedung</label>
                            <select class="form-control form-select" id="edit_building_id" name="building_id" required>
                                <option value="">Pilih Gedung</option>
                                @foreach ($building as $item)
                                <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_nomor" class="form-label">Nomor</label>
                            <input type="text" id="edit_nomor" class="form-control" name="nomor" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Show Modal -->
    <div class="modal fade" id="showModal" tabindex="-1" aria-labelledby="showModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="showModalLabel">Detail Ruangan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Ruangan</label>
                        <p class="form-control-static" id="show_name"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gedung</label>
                        <p class="form-control-static" id="show_building"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nomor</label>
                        <p class="form-control-static" id="show_nomor"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
            var name = button.getAttribute('data-name');
            var buildingId = button.getAttribute('data-building-id');
            var nomor = button.getAttribute('data-nomor');

            var modal = this;
            modal.querySelector('#edit_name').value = name;
            modal.querySelector('#edit_building_id').value = buildingId;
            modal.querySelector('#edit_nomor').value = nomor;

            // Update form action with correct route
            var form = modal.querySelector('#editForm');
            form.action = '{{ route("admin.ruangan.update", "") }}/' + id;
        });

        // Show Modal Handler
        document.getElementById('showModal').addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var name = button.getAttribute('data-name');
            var building = button.getAttribute('data-building');
            var nomor = button.getAttribute('data-nomor');

            var modal = this;
            modal.querySelector('#show_name').textContent = name;
            modal.querySelector('#show_building').textContent = building;
            modal.querySelector('#show_nomor').textContent = nomor;
        });
    </script>
    @endpush
</x-main-layout>