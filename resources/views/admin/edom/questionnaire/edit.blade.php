<x-main-layout>
    @section('title', 'Edit Kuesioner EDOM')

    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">@yield('title')</h3>
            </div>
        </div>
    </div>

    <div class="nk-block">
        <div class="card card-bordered">
            <div class="card-inner">
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('admin.edom.questionnaire.update', $questionnaire->id) }}" method="POST"
                    id="questionnaireForm" onsubmit="return validateForm(event)">
                    @csrf
                    @method('PUT')

                    <!-- Informasi Dasar -->
                    <div class="form-group">
                        <label class="form-label" for="title">Judul Kuesioner</label>
                        <div class="form-control-wrap">
                            <input type="text" class="form-control" id="title" name="title"
                                value="{{ old('title', $questionnaire->title) }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="description">Deskripsi</label>
                        <div class="form-control-wrap">
                            <textarea class="form-control" id="description" name="description" rows="3"
                                required>{{ old('description', $questionnaire->description) }}</textarea>
                        </div>
                    </div>

                    <!-- Daftar Pertanyaan -->
                    <div class="form-group" id="questionsContainer">
                        <label class="form-label">Daftar Pertanyaan</label>
                        <div id="questionsList">
                            @foreach($questions as $index => $question)
                            <div class="card card-bordered mt-3 question-item" id="question_{{ $index }}">
                                <div class="card-inner">
                                    <div class="row g-4">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label">Kategori</label>
                                                <div class="form-control-wrap">
                                                    <div class="input-group">
                                                        <select class="form-select category-select"
                                                            name="questions[{{ $index }}][category]" required>
                                                            <option value="">Pilih Kategori</option>
                                                            @foreach($categories as $category)
                                                            <option value="{{ $category->id }}" {{
                                                                strtoupper(trim($question->category)) ===
                                                                strtoupper(trim($category->id)) ? 'selected' : '' }}>
                                                                {{ $category->value }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                        <button type="button" class="btn btn-primary"
                                                            onclick="showNewCategoryModal()">
                                                            <em class="icon ni ni-plus"></em>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label">Pertanyaan</label>
                                                <div class="form-control-wrap">
                                                    <textarea class="form-control"
                                                        name="questions[{{ $index }}][question_text]" rows="2"
                                                        required>{{ $question->question_text }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Bobot</label>
                                                <div class="form-control-wrap">
                                                    <input type="number" class="form-control"
                                                        name="questions[{{ $index }}][weight]" min="1" max="5"
                                                        value="{{ $question->weight }}" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <button type="button" class="btn btn-danger btn-dim"
                                                    onclick="removeQuestion({{ $index }})">
                                                    <em class="icon ni ni-trash"></em>
                                                    <span>Hapus</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="mt-3">
                            <button type="button" class="btn btn-outline-primary btn-dim" onclick="addQuestion()">
                                <em class="icon ni ni-plus"></em>
                                <span>Tambah Pertanyaan</span>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <div class="form-control-wrap">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="activeStatus" name="status"
                                    value="ACTIVE" {{ $questionnaire->status === 'ACTIVE' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="activeStatus">
                                    Aktifkan kuesioner ini
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">Update Kuesioner</button>
                        <a href="{{ route('admin.edom.questionnaire.index') }}" class="btn btn-outline-secondary">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let questionCount = {{ count($questions) }};
        
        function validateForm(event) {
            event.preventDefault();
            
            console.log('Form validation started');
            
            // Log form data
            const formData = new FormData(event.target);
            const data = {};
            for (let [key, value] of formData.entries()) {
                data[key] = value;
            }
            
            // Validate questions
            const questions = document.querySelectorAll('.question-item');
            if (questions.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Minimal harus ada satu pertanyaan!'
                });
                return false;
            }
            
            // Show loading state
            Swal.fire({
                title: 'Memperbarui Kuesioner',
                text: 'Mohon tunggu...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Submit form
            fetch(event.target.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Kuesioner berhasil diperbarui',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = '{{ route('admin.edom.questionnaire.index') }}';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: data.message || 'Terjadi kesalahan saat memperbarui kuesioner'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Terjadi kesalahan saat memperbarui kuesioner'
                });
            });
            
            return false;
        }

        function addQuestion(data = null) {
            const template = `
                <div class="card card-bordered mt-3 question-item" id="question_${questionCount}">
                    <div class="card-inner">
                        <div class="row g-4">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Kategori</label>
                                    <div class="form-control-wrap">
                                        <select class="form-select" name="questions[${questionCount}][category]" required>
                                            <option value="">Pilih Kategori</option>
                                            @foreach($categories as $category)
                                            <option value="{{ $category->id }}">
                                                {{ $category->value }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Pertanyaan</label>
                                    <div class="form-control-wrap">
                                        <textarea class="form-control" 
                                                name="questions[${questionCount}][question_text]" 
                                                rows="2" required>${data?.question_text || ''}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Bobot</label>
                                    <div class="form-control-wrap">
                                        <input type="number" class="form-control" 
                                               name="questions[${questionCount}][weight]"
                                               min="1" max="5" value="${data?.weight || 1}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <button type="button" class="btn btn-danger btn-dim" 
                                            onclick="removeQuestion(${questionCount})">
                                        <em class="icon ni ni-trash"></em>
                                        <span>Hapus</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            document.getElementById('questionsList').insertAdjacentHTML('beforeend', template);
            questionCount++;
        }

        function removeQuestion(index) {
            document.getElementById(`question_${index}`).remove();
        }
    </script>
    @endpush
</x-main-layout>