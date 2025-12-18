<x-main-layout>
    @section('title', 'Buat Kuesioner EDOM')

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
                <form action="{{ route('admin.edom.questionnaire.store') }}" method="POST" id="questionnaireForm">
                    @csrf

                    <!-- Informasi Dasar -->
                    <div class="form-group">
                        <label class="form-label" for="title">Judul Kuesioner</label>
                        <div class="form-control-wrap">
                            <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}"
                                required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="description">Deskripsi</label>
                        <div class="form-control-wrap">
                            <textarea class="form-control" id="description" name="description" rows="3"
                                required>{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <!-- Daftar Pertanyaan -->
                    <div class="form-group" id="questionsContainer">
                        <label class="form-label">Daftar Pertanyaan</label>
                        <div id="questionsList">
                            <!-- Template pertanyaan akan ditambahkan di sini -->
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
                                <input type="hidden" name="status" id="status" value="DRAFT">
                                <input type="checkbox" class="custom-control-input" id="activeStatus"
                                    onchange="document.getElementById('status').value = this.checked ? 'ACTIVE' : 'DRAFT'">
                                <label class="custom-control-label" for="activeStatus">
                                    Aktifkan kuesioner ini
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">Simpan Kuesioner</button>
                        <a href="{{ route('admin.edom.questionnaire.index') }}" class="btn btn-outline-secondary">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Kategori -->
    <div class="modal fade" id="newCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kategori Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="newCategoryKey">Kode Kategori</label>
                        <input type="text" class="form-control" id="newCategoryKey" placeholder="Contoh: PEDAGOGIK">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="newCategoryValue">Nama Kategori</label>
                        <input type="text" class="form-control" id="newCategoryValue"
                            placeholder="Contoh: Kemampuan Pedagogik">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="saveNewCategory()">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let questionCount = 0;

        function addQuestion(data = null) {
            const template = `
                <div class="card card-bordered mt-3 question-item" id="question_${questionCount}">
                    <div class="card-inner">
                        <div class="row g-4">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Kategori</label>
                                    <div class="form-control-wrap">
                                        <div class="input-group">
                                            <select class="form-select category-select" name="questions[${questionCount}][category]" required>
                                                <option value="">Pilih Kategori</option>
                                                @foreach($categories as $id => $value)
                                                    <option value="{{ $id }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                            <button type="button" class="btn btn-primary" onclick="showNewCategoryModal()">
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

        // Add initial question
        document.addEventListener('DOMContentLoaded', function() {
            addQuestion();
        });

        // Form validation
        document.getElementById('questionnaireForm').addEventListener('submit', function(e) {
            const questions = document.querySelectorAll('.question-item');
            if (questions.length === 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Minimal harus ada satu pertanyaan!'
                });
            }
        });

        function showNewCategoryModal() {
            $('#newCategoryModal').modal('show');
        }

        function saveNewCategory() {
            const key = document.getElementById('newCategoryKey').value.trim();
            const value = document.getElementById('newCategoryValue').value.trim();

            if (!key || !value) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Kode dan nama kategori harus diisi!'
                });
                return;
            }
            fetch('{{ route('admin.edom.category.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ key, value })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const categorySelects = document.querySelectorAll('.category-select');
                    categorySelects.forEach(select => {
                        const option = new Option(value, key);
                        select.add(option);
                    });
                    $('#newCategoryModal').modal('hide');
                    document.getElementById('newCategoryKey').value = '';
                    document.getElementById('newCategoryValue').value = '';

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Kategori baru berhasil ditambahkan',
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: data.message || 'Gagal menambahkan kategori'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Terjadi kesalahan saat menambahkan kategori'
                });
            });
        }
    </script>
    @endpush
</x-main-layout>