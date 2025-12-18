<x-main-layout>
    @section('title', 'Daftar Survei')
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
                                <a href="{{ route('admin.questions.create') }}" class="btn btn-primary">
                                    <em class="icon ni ni-plus"></em>
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
                                <th scope="col">No.</th>
                                <th scope="col">Daftar Survey</th>
                                <th scope="col">Tahun Akademik</th>
                                <th scope="col">Mulai</th>
                                <th scope="col">Berakhir</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($questionnaires as $item)
                            <tr>
                                <td>{{ $loop->iteration }}.</td>
                                <td class="nk-tb-col">{{ $item->title }}</td>
                                <td class="nk-tb-col">{{ $item->ta->ta }} Semester {{ $item->ta->semester }}</td>
                                <td class="nk-tb-col">{{ $item->start_date }}</td>
                                <td class="nk-tb-col">{{ $item->end_date }}</td>
                                <td>
                                    <form action="{{ route('admin.questions.destroy', $item->id) }}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <a class="btn btn-sm btn-info"
                                            href="{{ route('admin.question.show', $item->id) }}"><em
                                                class="icon ni ni-eye"></em></a>
                                        <a class="btn btn-sm btn-warning"
                                            href="{{ route('admin.question.edit', $item->id) }}"><em
                                                class="icon ni ni-edit"></em></a>
                                        <button type="submit" class="btn btn-sm btn-danger"><em
                                                class="icon ni ni-trash-fill"></em></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td>No data yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-main-layout>