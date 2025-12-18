<x-main-layout>
    @section('title', 'Halaman Pengajuan Surat')

    <div class="nk-content">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">@yield('title')</h3>
                            </div>
                            <div class="nk-block-head-content">
                                <div class="toggle-wrap nk-block-tools-toggle">
                                    <a href="{{ route('student.request.letter.index') }}"
                                        class="btn btn-outline-secondary">
                                        <em class="icon ni ni-arrow-left"></em>
                                        <span>Kembali</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                    @endif

                    @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                    <div class="nk-block">
                        <div class="card">
                            <div class="card-inner">
                                <form action="{{ route('student.request.letter.store') }}" method="POST"
                                    class="form-validate" id="letter-form">
                                    @csrf
                                    <div class="row g-gs">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="tipe_surat_id">Jenis Surat</label>
                                                <div class="form-control-wrap">
                                                    <select
                                                        class="form-select @error('tipe_surat_id') is-invalid @enderror"
                                                        id="tipe_surat_id" name="tipe_surat_id" required>
                                                        <option value="">Pilih Jenis Surat</option>
                                                        @foreach($letterTypes as $type)
                                                        <option value="{{ $type->id }}" {{ old('tipe_surat_id')==$type->
                                                            id ? 'selected' : '' }}
                                                            data-fields='@json($type->required_fields)'>
                                                            {{ $type->name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                    @error('tipe_surat_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="dynamic-form-fields" class="row g-gs mt-3">
                                        <!-- Dynamic fields will be inserted here -->
                                    </div>

                                    <div class="row g-gs mt-3">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary" name="status"
                                                    value="submitted" id="submit-btn">
                                                    <span class="text">Ajukan Surat</span>
                                                    <span class="loading d-none">
                                                        <em class="icon ni ni-loader"></em>
                                                        <span>Memproses...</span>
                                                    </span>
                                                </button>
                                                <button type="submit" class="btn btn-primary" name="status"
                                                    value="draft" id="draft-btn">
                                                    <span class="text">Simpan Draft</span>
                                                    <span class="loading d-none">
                                                        <em class="icon ni ni-loader"></em>
                                                        <span>Memproses...</span>
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-custom.sweet-alert />

    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @endpush

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        (function($) {
            "use strict";

            $(document).ready(function() {
                // Initialize select2
                $('.js-select2').select2({
                    dropdownParent: $('.form-control-wrap')
                });

                // Handle letter type change
                $('#tipe_surat_id').on('change', function() {
                    const selectedOption = $(this).find('option:selected');
                    const fields = selectedOption.data('fields');

                    console.log('Selected fields:', fields);
                    $('#dynamic-form-fields').empty();

                    if (!fields || !fields.length) {
                        return;
                    }

                    let html = '';
                    fields.forEach(field => {
                        const fieldName = typeof field === 'string' ? field : field.name;
                        const fieldLabel = typeof field === 'string' ? field : (field.label || field.name);
                        const fieldType = typeof field === 'string' ? 'text' : (field.type || 'text');

                        html += `
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="form_data_${fieldName}">${fieldLabel}</label>
                                    <div class="form-control-wrap">
                                        <input type="${fieldType}"
                                               class="form-control @error('form_data.${fieldName}') is-invalid @enderror"
                                               id="form_data_${fieldName}"
                                               name="form_data[${fieldName}]"
                                               value="{{ old('form_data.${fieldName}') }}"
                                               required>
                                        @error('form_data.${fieldName}')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        `;
                    });

                    $('#dynamic-form-fields').html(html);
                });

                // Form submission handling
                $('#letter-form button[name="status"]').on('click', function() {
                    $('#letter-form').data('status', $(this).val());
                });

                $('#letter-form').on('submit', function(e) {
                    e.preventDefault();

                    console.log('Form submitted');

                    const selectedType = $('#tipe_surat_id').val();
                    if (!selectedType) {
                        Swal.fire('Peringatan', 'Silakan pilih jenis surat terlebih dahulu', 'warning');
                        return false;
                    }

                    const status = $(this).data('status') || 'submitted';

                    let isValid = true;
                    $(this).find('[required]').each(function() {
                        if (!$(this).val()) {
                            isValid = false;
                            $(this).addClass('is-invalid');
                        } else {
                            $(this).removeClass('is-invalid');
                        }
                    });

                    if (!isValid) {
                        Swal.fire('Peringatan', 'Silakan lengkapi semua field yang diperlukan', 'warning');
                        return false;
                    }

                    const submitBtn = $('#submit-btn');
                    submitBtn.prop('disabled', true);
                    submitBtn.find('.text').addClass('d-none');
                    submitBtn.find('.loading').removeClass('d-none');

                    const draftBtn = $('#draft-btn');
                    draftBtn.prop('disabled', true);
                    draftBtn.find('.text').addClass('d-none');
                    draftBtn.find('.loading').removeClass('d-none');

                    const formData = new FormData(this);
                    formData.append('status', status);
                    formData.append('_token', '{{ csrf_token() }}');

                    $.ajax({
                        url: $(this).attr('action'),
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            console.log('Success:', response);
                            if (response.success) {
                                Swal.fire({
                                    title: 'Berhasil',
                                    text: response.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = response.redirect || "{{ route('student.request.letter.index') }}";
                                    }
                                });
                            } else {
                                Swal.fire('Gagal', response.message, 'error').then(() => {
                                    resetButtonState();
                                });
                            }
                        },
                        error: function(xhr) {
                            console.log('Error:', xhr);
                            let errorMessage = 'Terjadi kesalahan saat mengajukan surat';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            Swal.fire('Error', errorMessage, 'error').then(() => {
                                resetButtonState();
                            });
                        }
                    });

                    function resetButtonState() {
                        submitBtn.prop('disabled', false);
                        submitBtn.find('.text').removeClass('d-none');
                        submitBtn.find('.loading').addClass('d-none');

                        draftBtn.prop('disabled', false);
                        draftBtn.find('.text').removeClass('d-none');
                        draftBtn.find('.loading').addClass('d-none');
                    }
                });
            });
        })(jQuery);
    </script>

    @endpush
</x-main-layout>
