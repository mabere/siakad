<x-main-layout>
    @section('title', 'Edit Pertanyaan EDOM')

    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">@yield('title')</h3>
                <div class="nk-block-des text-soft">
                    <p>Edit pertanyaan untuk kuesioner EDOM</p>
                </div>
            </div>
        </div>
    </div>

    <div class="nk-block">
        <div class="card card-bordered">
            <div class="card-inner">
                <form action="{{ route('admin.edom.questions.update', $question) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label class="form-label" for="category">Kategori</label>
                        <div class="form-control-wrap">
                            <select class="form-control" id="category" name="category" required>
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $id => $value)
                                <option value="{{ $id }}" {{ $question->category == $id ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="question_text">Pertanyaan</label>
                        <div class="form-control-wrap">
                            <textarea class="form-control" id="question_text" name="question_text" rows="3"
                                required>{{ $question->question_text }}</textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="weight">Bobot</label>
                        <div class="form-control-wrap">
                            <input type="number" class="form-control" id="weight" name="weight" min="1" max="5"
                                value="{{ $question->weight }}" required>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">Update Pertanyaan</button>
                        <a href="{{ route('admin.edom.questions.index', $question->questionnaire) }}"
                            class="btn btn-outline-secondary">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Check for success/error messages in session
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 1500
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '{{ session('error') }}'
            });
        @endif
    </script>
    @endpush
</x-main-layout>