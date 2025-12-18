<x-main-layout>
    @section('title', 'Detail Data Lembaga')
    <div class="components-preview wide-lg mx-auto">
        <div class="nk-block nk-block-lg">
            <div class="nk-block-head">
                <div class="nk-block-between-md g-4">
                    <div class="nk-block-head-content">
                        <h4 class="nk-block-title">@yield('title')</h4>
                        <div class="nk-block-des">
                            <p>Informasi @yield('title')</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-bordered">
                <div class="card-inner">
                    <div class="nk-block">
                        <div class="nk-block-head">
                            <span class="title">
                                <h5>@yield('title')</h5>
                            </span>
                        </div>

                        <div class="row gy-3">
                            <div class="col-md-4 text-center">
                                <div class="profile-ud-item">
                                    <div class="profile-ud">
                                        <img id="signature-preview"
                                            src="{{ asset('images/staff/' . $unit->signature_path) }}" alt="Signature"
                                            class="img-fluid rounded mb-2" style="max-width: 150px; height: auto;">

                                        <a href="#" data-bs-toggle="modal" data-bs-target="#editSignatureModal"
                                            class="d-inline-flex flex-column align-items-center text-decoration-none text-primary mt-2">
                                            <em class="icon ni ni-camera" style="font-size: 1.2rem;"></em>
                                            <span style="font-size: 0.9rem;">Edit Foto</span>
                                        </a>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-8">
                                <div class="profile-ud-list">
                                    <div class="profile-ud-item">
                                        <div class="profile-ud">
                                            <span class="profile-ud-label">
                                                <h6><em class="icon ni ni-home-fill text-primary"></em> Nama Lembaga
                                                </h6>
                                            </span>
                                            <span class="profile-ud-value">{{ $unit->nama }}</span>
                                        </div>
                                    </div>
                                    <div class="profile-ud-item mb-3">
                                        <div class="profile-ud">
                                            <span class="profile-ud-label">
                                                <h6><em class="icon ni ni-view-panel-fill text-primary"></em> Level</h6>
                                            </span>
                                            <span class="profile-ud-value">
                                                <span class="badge badge-dim bg-primary">{{ Str::ucfirst($unit->level)
                                                    }}</span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="profile-ud-item mb-3">
                                        <div class="profile-ud">
                                            <span class="profile-ud-label">
                                                <h6><em class="icon ni ni-user-list-fill text-primary"></em> Pimpinan
                                                </h6>
                                            </span>
                                            <span class="profile-ud-value">
                                                <span>{{ $unit->kepala_unit }}</span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="profile-ud-item mb-3">
                                        <div class="profile-ud">
                                            <span class="profile-ud-label">
                                                <h6><em class="icon ni ni-call-fill text-primary"></em> Phone</h6>
                                            </span>
                                            <span class="profile-ud-value">
                                                <span>{{ $unit->phone }}</span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="profile-ud-item">
                                        <div class="profile-ud">
                                            <span class="profile-ud-label">
                                                <h6><em class="icon ni ni-clock-fill text-primary"></em> Dibuat Pada
                                                </h6>
                                            </span>
                                            <span class="profile-ud-value">{{ $unit->created_at->format('d M Y, H:i')
                                                }}</span>
                                        </div>
                                    </div>
                                    <div class="profile-ud-item">
                                        <div class="profile-ud">
                                            <span class="profile-ud-label">
                                                <h6><em class="icon ni ni-card-view text-primary"></em> NIP</h6>
                                            </span>
                                            <span class="profile-ud-value">{{ $unit->nip_kepala }}</span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="nk-block mt-4">
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.units.index') }}" class="btn btn-secondary me-1">
                                <em class="icon ni ni-reply"></em> Kembali
                            </a>
                            <a href="{{ route('admin.units.edit', $unit->id) }}" class="btn btn-warning me-1">
                                <em class="icon ni ni-edit"></em> Edit
                            </a>
                            <a href="#"
                                onclick="event.preventDefault(); if(confirm('Apakah Anda yakin ingin menghapus data ini?')) document.getElementById('delete-form').submit();"
                                class="btn btn-danger">
                                <em class="icon ni ni-trash"></em> Hapus
                            </a>
                        </div>
                        <form id="delete-form" action="{{ route('admin.units.destroy', $unit->id) }}" method="post"
                            style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="editSignatureModal" tabindex="-1" aria-labelledby="editSignatureModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSignatureModalLabel">Edit Tanda Tangan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editSignatureModalForm" action="{{ route('admin.units.signature.update', $unit->id) }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="file" name="signature_path" id="signature-upload-modal" class="form-control"
                            accept="image/*">
                        <img id="signature-preview-modal" src="#" alt="Preview Tanda Tangan"
                            class="img-fluid rounded mt-2" style="max-width: 150px; display: none;">
                        <div class="mt-2">
                            <small class="text-muted">Pastikan ukuran file tidak lebih dari 500kb</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" form="editSignatureModalForm" class="btn btn-primary">Simpan
                        Perubahan</button>
                </div>
            </div>
        </div>
    </div>

    @push('script')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Preview gambar di modal
        document.getElementById('signature-upload-modal').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('signature-preview-modal').src = e.target.result;
                    document.getElementById('signature-preview-modal').style.display = 'block'; // Tampilkan preview
                }
                reader.readAsDataURL(file);
            } else {
                document.getElementById('signature-preview-modal').src = '#';
                document.getElementById('signature-preview-modal').style.display = 'none'; // Sembunyikan preview jika tidak ada file
            }
        });
        
        // Memastikan preview gambar utama di update ketika modal ditutup
        $('#editSignatureModal').on('hidden.bs.modal', function () {
            const modalPreviewSrc = $('#signature-preview-modal').attr('src');
            $('#signature-preview').attr('src', modalPreviewSrc);
        })
    </script>
    @endpush
</x-main-layout>