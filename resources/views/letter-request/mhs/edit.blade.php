<x-main-layout>
    <div class="container">
        <h1>Edit Letter Request</h1>
        <a class="btn btn-warning" href="/mhs/surat">Back</a>

        <form action="{{ route('student.request.surat.update', $letterRequest->id) }}" method="POST" id="letter-form">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="letter_type_id">Jenis Permohonan Surat</label>
                <input type="text" class="form-control" value="{{ $letterRequest->letterType->name }}" disabled>
                <input type="hidden" name="letter_type_id" value="{{ $letterRequest->letter_type_id }}">
            </div>

            <div id="dynamic-form-fields" class="row g-gs mt-3">
                <!-- Dynamic fields will be inserted here -->
            </div>

            <!-- Hidden input for status -->
            <input type="hidden" name="status" id="status-input" value="draft">

            <div class="row g-gs mt-3">
                <div class="col-12">
                    <div class="form-group">
                        <button type="submit" class="btn btn-secondary" id="save-draft-btn">
                            <span class="text">Save as Draft</span>
                            <span class="loading d-none">
                                <em class="icon ni ni-loader"></em>
                                <span>Saving...</span>
                            </span>
                        </button>
                        <button type="submit" class="btn btn-primary" id="submit-btn">
                            <span class="text">Submit Surat</span>
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
            console.log('Document ready');

            // Initialize select2
            $('.js-select2').select2({
                dropdownParent: $('.form-control-wrap')
            });

            // Populate dynamic form fields with existing data
            const fields = @json($letterRequest->letterType->required_fields);
            const formData = @json($letterRequest->form_data);
            let html = '';

            fields.forEach(field => {
                const fieldName = typeof field === 'string' ? field : field.name;
                const fieldLabel = typeof field === 'string' ? field : (field.label || field.name);
                const fieldType = typeof field === 'string' ? 'text' : (field.type || 'text');
                const fieldValue = formData[fieldName] || '';
                
                html += `
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label" for="form_data_${fieldName}">${fieldLabel}</label>
                            <div class="form-control-wrap">
                                <input type="${fieldType}" 
                                       class="form-control @error('form_data.${fieldName}') is-invalid @enderror" 
                                       id="form_data_${fieldName}"
                                       name="form_data[${fieldName}]"
                                       value="${fieldValue}"
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

            // Handle button clicks to set status
            $('#save-draft-btn').on('click', function(e) {
                console.log('Save as Draft clicked');
                $('#status-input').val('draft');
            });

            $('#submit-btn').on('click', function(e) {
                console.log('Submit clicked');
                $('#status-input').val('submitted');
            });

            // Form submission handling
            $('#letter-form').on('submit', function(e) {
                e.preventDefault();
                console.log('Form submission triggered');

                // Validate required fields (only for submit)
                let isValid = true;
                if ($('#status-input').val() === 'submitted') {
                    $(this).find('[required]').each(function() {
                        if (!$(this).val()) {
                            isValid = false;
                            $(this).addClass('is-invalid');
                        } else {
                            $(this).removeClass('is-invalid');
                        }
                    });

                    if (!isValid) {
                        console.log('Validation failed');
                        Swal.fire('Peringatan', 'Silakan lengkapi semua field yang diperlukan untuk mengajukan surat', 'warning');
                        return false;
                    }
                }

                // Show loading state
                const clickedBtn = $(document.activeElement).attr('id') === 'submit-btn' ? $('#submit-btn') : $('#save-draft-btn');
                console.log('Clicked button:', clickedBtn.attr('id'));
                clickedBtn.prop('disabled', true);
                clickedBtn.find('.text').addClass('d-none');
                clickedBtn.find('.loading').removeClass('d-none');

                // Get form data and add CSRF token
                const formData = new FormData(this);
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('_method', 'PUT'); // Karena update biasanya PUT, tapi form HTML biasa POST

                // Submit form via AJAX
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log('AJAX Success:', response);
                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = response.redirect || "{{ route('student.request.surat.index') }}";
                                }
                            });
                        } else {
                            Swal.fire('Gagal', response.message, 'error').then(() => {
                                resetButtonState(clickedBtn);
                            });
                        }
                    },
                    error: function(xhr) {
                        console.log('AJAX Error:', xhr);
                        let errorMessage = 'Terjadi kesalahan saat memproses';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            errorMessage = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        }
                        Swal.fire('Error', errorMessage, 'error').then(() => {
                            resetButtonState(clickedBtn);
                        });
                    }
                });

                // Function to reset button state
                function resetButtonState(btn) {
                    btn.prop('disabled', false);
                    btn.find('.text').removeClass('d-none');
                    btn.find('.loading').addClass('d-none');
                }
            });
        });
    })(jQuery);
    </script>
    @endpush
</x-main-layout>