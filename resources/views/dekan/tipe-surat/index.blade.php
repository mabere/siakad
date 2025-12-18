<x-main-layout>
    @section('title', 'Manajemen Jenis Surat')

    <div class="nk-content">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">Jenis Surat</h3>
                            </div>
                            <div class="nk-block-head-content">
                                <div class="toggle-wrap nk-block-tools-toggle">
                                    <a href="{{ route('dekan.letter-types.create') }}" class="btn btn-primary">
                                        <em class="icon ni ni-plus"></em>
                                        <span>Tambah Jenis Surat</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="nk-block">
                        <div class="card">
                            <div class="card-inner">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Nama</th>
                                            <th>Kode</th>
                                            <th>Perlu Persetujuan</th>
                                            <th>Otoritas</th>
                                            <th>Field yang Diperlukan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($tipeSurats as $type)
                                        <tr>
                                            <td>{{ $type->name }}</td>
                                            <td>{{ $type->code }}</td>
                                            <td>
                                                @if($type->needs_approval)
                                                <span class="badge bg-success">Ya</span>
                                                @else
                                                <span class="badge bg-warning">Tidak</span>
                                                @endif
                                            </td>
                                            <td>{{ $type->letterTypeAssignments->first()->signer_role ?? 'Tidak Ada
                                                Penandatangan' }}</td>
                                            <td>
                                                @if($type->required_fields)
                                                <span class="badge bg-primary" data-bs-toggle="tooltip"
                                                    data-bs-html="true"
                                                    title="<ul class='p-0 m-0'>@foreach($type->required_fields as $field)<li>{{ $field }}</li>@endforeach</ul>">
                                                    <em class="icon ni ni-info"></em> Lihat Field
                                                </span>
                                                @else
                                                -
                                                @endif
                                            </td>

                                            <td>
                                                <div class="dropdown">
                                                    <a class="dropdown-toggle btn btn-icon btn-trigger"
                                                        data-bs-toggle="dropdown">
                                                        <em class="icon ni ni-more-h"></em>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <ul class="link-list-opt">
                                                            <li>
                                                                <a
                                                                    href="{{ route('dekan.letter-types.edit', $type->id) }}">
                                                                    <em class="icon ni ni-edit"></em>
                                                                    <span>Edit</span>
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href="#"
                                                                    onclick="event.preventDefault(); document.getElementById('delete-form-{{ $type->id }}').submit();">
                                                                    <em class="icon ni ni-trash"></em>
                                                                    <span>Hapus</span>
                                                                </a>
                                                                <form id="delete-form-{{ $type->id }}"
                                                                    action="{{ route('dekan.letter-types.destroy', $type->id) }}"
                                                                    method="POST" class="d-none">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                </form>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Belum ada jenis surat</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                {{ $tipeSurats->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-custom.sweet-alert />

    @stack('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Inisialisasi tooltip
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Inisialisasi dropdown (opsional, jika Bootstrap JS sudah dimuat)
            var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
            dropdownElementList.map(function (dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl);
            });
        });
    </script>

</x-main-layout>
