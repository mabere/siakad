<x-main-layout>
    @section('title', 'Edit Pertanyaan')
    <div class="container">
        <h3>Edit Pertanyaan untuk {{ $questionnaire->title }}</h3>

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('admin.question.update', $question->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="question_text">Pertanyaan</label>
                <input type="text" name="question_text" id="question_text" class="form-control @error('question_text') is-invalid @enderror"
                    value="{{ old('question_text', $question->question_text) }}" required>
                @error('question_text')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="type">Kategory</label>
                <input type="text" name="type" id="type" class="form-control @error('type') is-invalid @enderror"
                    value="{{ old('type', $question->type) }}" required>
                @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="options">Rating</label>
                <select name="options" id="options" class="form-control @error('options') is-invalid @enderror" required>
                    <option value="likert" {{ old('options', $question->options) === 'likert' ? 'selected' : '' }}>Likert</option>
                    <option value="multiple_choice" {{ old('options', $question->options) === 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                    <option value="text" {{ old('options', $question->options) === 'text' ? 'selected' : '' }}>Text</option>
                </select>
                @error('options')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary mt-3">Update</button>
            <a href="{{ route('admin.question.show', $questionnaire->id) }}" class="btn btn-secondary mt-3">Kembali</a>
        </form>
    </div>
</x-main-layout>