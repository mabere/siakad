<x-main-layout>
    @section('title', 'Detail Pertanyaan')
    <div class="container">
        <div class="nk-block-between mb-5">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Daftar Pertanyaan untuk: {{ $questionnaire->title }}</h3>
            </div>
            <div class="nk-block-head-content">
                <a href="{{ route('admin.question.create', ['questionnaire_id' => $questionnaire->id]) }}"
                    class="btn btn-primary  data-toggle=" tooltip" data-placement="top" title="Tambah Pertanyaan">
                    <i class="icon ni ni-plus"></i>
                    <span>Add</span>
                </a>
            </div>
        </div>
        <x-custom.sweet-alert />

        <div class="row">
            <div class="col-md-5">
                <div>
                    <form action="{{ url('admin/question/import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="file" class="form-control">
                        <br>

                </div>
            </div>
            <div class="col-md-1">
                <button class="btn btn-success">
                    <i class="fa fa-file"></i> Impor
                </button>
            </div>
            </form>
            <div class="col-md-1">
                <a class="btn btn-warning float-end" href="{{ url('admin/question/export') }}">
                    <i class="fa fa-download"></i> Ekspor
                </a>

            </div>
        </div>
        <table class="table datatable-init table-striped">
            <thead class="bg-primary text-white">
                <tr>
                    <th class="p-2">No.</th>
                    <th class="p-2">Item Pertanyaan</th>
                    <th class="p-2">Kategori</th>
                    <th class="p-2">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($questionnaire->questions as $index => $question)
                <tr>
                    <td>{{ $index + 1 }}.</td>
                    <td width="62%">{{ Str::limit($question->question_text, 80) }}</td>
                    <td>{{ $question->type }}</td>
                    <td>
                        <a href="{{ route('admin.question.edit', $question->id) }}" class="btn btn-sm btn-warning">
                            <i class="icon ni ni-edit"></i>
                        </a>
                        <form action="{{ route('admin.question.destroy', $question->id) }}" method="POST"
                            style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="icon ni ni-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</x-main-layout>