<x-main-layout>
    @section('title', 'Tambah Pertanyaan')
    <div class="container">
        <h3>@yield('title')</h3>

        <form action="{{ route('admin.question.store') }}" method="POST">
            @csrf
            <input type="hidden" name="questionnaire_id" value="{{ $questionnaire->id }}">

            <div class="form-group">
                <label for="question_text">Pertanyaan</label>
                <input type="text" name="question_text" id="question_text" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="options">Skala</label>
                <input type="text" name="options" id="options" class="form-control" value="Likert" readonly>
            </div>
            <div class="form-group">
                <label for="type">Kategori</label>
                <input type="text" name="type" id="type" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-sm btn-primary mt-3">Tambah</button>
            <a href="{{ route('admin.question.show', $questionnaire->id) }}"
                class="btn btn-sm btn-secondary mt-3">Kembali</a>
        </form>
    </div>

</x-main-layout>