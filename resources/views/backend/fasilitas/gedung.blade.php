<x-main-layout>
    @section('title', 'Data Gedung')
    <div class="nk-block-head-content d-flex justify-content-between">
        <h4 class="nk-block-title">Data Gedung</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">Tambah</button>
    </div>

    <x-custom.sweet-alert />

    <table class="datatable-init nowrap table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Lokasi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($gedung as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->nama }}</td>
                <td>{{ $item->lokasi }}</td>
                <td>
                    <button class="btn btn-sm btn-info" data-bs-toggle="modal"
                        data-bs-target="#showModal{{ $item->id }}">
                        <em class="icon ni ni-eye"></em>
                    </button>
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                        data-bs-target="#editModal{{ $item->id }}">
                        <em class="icon ni ni-edit"></em>
                    </button>
                    <form action="{{ route('admin.gedung.destroy', $item->id) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')">
                            <em class="icon ni ni-trash"></em>
                        </button>
                    </form>
                </td>
            </tr>

            <x-modal id="showModal{{ $item->id }}" title="Detail Gedung">
                @include('backend.fasilitas.partials.detail', ['building' => $item])
            </x-modal>

            <x-modal id="editModal{{ $item->id }}" title="Edit Gedung">
                @include('backend.fasilitas.partials.form', [
                'formAction' => route('admin.gedung.update', $item->id),
                'building' => $item,
                'isEdit' => true
                ])
            </x-modal>
            @endforeach
        </tbody>
    </table>

    <x-modal id="createModal" title="Tambah Gedung">
        @include('backend.fasilitas.partials.form', [
        'formAction' => route('admin.gedung.store'),
        'building' => null,
        'isEdit' => false
        ])
    </x-modal>
</x-main-layout>
