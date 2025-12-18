<x-main-layout>
    @section('title', 'Data Unit Pelaksana Teknis')
    <div class="components-preview wide-lg mx-auto">
        <div class="nk-block nk-block-lg">
            <div class="nk-block-head">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h4 class="nk-block-title">@yield('title')</h4>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <div class="toggle-expand-content">
                                <a href="/admin/units/create" class="btn btn-primary">
                                    <span>Add</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-bordered card-preview">
                @if(@session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif
                <div class="card-inner">
                    <table class="datatable-init nowrap table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Nama Lembaga</th>
                                <th>Pimpinan</th>
                                <th>NIP</th>
                                <th>Level</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($units as $item)
                            <tr>
                                <td>{{ $loop->iteration }}.</td>
                                <td>{{ $item->nama }}</td>
                                <td>
                                    @if($item->lecturer)
                                    {{ $item->lecturer->nama_dosen }}
                                    @else
                                    {{ $item->kepala_unit }}
                                    @endif
                                </td>
                                <td>@if($item->lecturer)
                                    {{ $item->lecturer->nidn }}
                                    @else
                                    {{ $item->nip_kepala }}
                                    @endif</td>
                                <td>{{ Str::ucfirst($item->level) }}</td>
                                <td>
                                    <form action="{{ route('admin.units.destroy', $item->id) }}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <a class="btn btn-sm btn-info"
                                            href="{{ route('admin.units.show', $item->id) }}"><em
                                                class="icon ni ni-eye"></em></a>
                                        <a class="btn btn-sm btn-warning"
                                            href="{{ route('admin.units.edit', $item->id) }}"><em
                                                class="icon ni ni-edit"></em></a>
                                        <button type="submit" class="btn btn-sm btn-danger"><em
                                                class="icon ni ni-trash-fill"></em></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td>Data kosong.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-main-layout>