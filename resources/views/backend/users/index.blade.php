<x-main-layout>
    @section('title', 'Data Pengguna')
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
                                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                                    <span>Add</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <table class="datatable-init nowrap table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Nama</th>
                                <th>User</th>
                                <th>Peran</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $item)
                            <tr>
                                <td>{{ $loop->iteration }}.</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->email }}</td>
                                <td>
                                    @foreach ($item->roles as $role)
                                    <span style="text-transform:capitalize">{{ $role->name }}<br></span>
                                    @endforeach
                                </td>
                                <td>
                                    <form action="{{ route('admin.users.destroy', $item->id) }}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <a class="btn btn-sm btn-warning"
                                            href="{{ route('admin.users.edit', $item->id) }}"><em
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
    <x-custom.sweet-alert />
</x-main-layout>
