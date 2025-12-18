<x-main-layout>
    @section('title', 'Manajemen EDOM')
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">@yield('title')</h3>
                <div class="nk-block-des text-soft">
                    <p>Kelola kuesioner dan pengaturan EDOM</p>
                </div>

                <div class="toggle-wrap nk-block-tools-toggle">
                    <a href="{{ route('admin.edom.categories.index') }}" class="btn btn-outline-primary me-2">
                        <em class="icon ni ni-list"></em>
                        <span>Kelola Kategori</span>
                    </a>
                </div>
            </div>
            <div class="nk-block-head-content">
                <div class="toggle-wrap nk-block-tools-toggle">
                    <a href="{{ route('admin.edom.questionnaire.create') }}" class="btn btn-primary">
                        <em class="icon ni ni-plus"></em>
                        <span>Buat Kuesioner Baru</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="nk-block">
        <!-- Status EDOM -->
        <div class="card card-bordered card-preview">
            <div class="card-inner">
                <div class="row g-gs">
                    <div class="col-md-7">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Kuesioner Aktif</h6>
                                    </div>
                                </div>
                                <div class="align-end flex-sm-wrap g-4 flex-md-nowrap">
                                    @if($activeQuestionnaire)
                                    <div class="nk-sale-data">
                                        <span class="amount text-success">
                                            {{ $activeQuestionnaire->title }}
                                        </span>
                                        <span class="sub-title">
                                            {{ $activeQuestionnaire->questions_count }} Pertanyaan
                                        </span>
                                    </div>
                                    @else
                                    <div class="nk-sale-data">
                                        <span class="amount text-warning">
                                            Tidak ada kuesioner aktif
                                        </span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Tahun Akademik Aktif</h6>
                                    </div>
                                </div>
                                <div class="align-end flex-sm-wrap g-4 flex-md-nowrap">
                                    <div class="nk-sale-data">
                                        <span class="amount">
                                            {{ $academicYear->ta }} - {{ $academicYear->semester }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Kuesioner -->
        <div class="card card-bordered card-preview mt-4">
            <div class="card-inner">
                <table class="datatable-init table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Judul</th>
                            <th>Deskripsi</th>
                            <th>Jumlah Pertanyaan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($questionnaires as $questionnaire)
                        <tr @if($questionnaire->status === 'ACTIVE') class="table-success" @endif>
                            <td>
                                <strong>{{ $questionnaire->title }}</strong>
                            </td>
                            <td>
                                <span title="{{ $questionnaire->description }}">
                                    {{ Str::limit($questionnaire->description, 50) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info">{{ $questionnaire->questions_count }}</span>
                            </td>
                            <td>
                                <span
                                    class="badge {{ $questionnaire->status === 'ACTIVE' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $questionnaire->status === 'ACTIVE' ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.edom.questionnaire.edit', $questionnaire) }}"
                                    class="btn btn-sm btn-outline-primary" title="Edit">
                                    <em class="icon ni ni-edit"></em>
                                </a>
                                <form action="{{ route('admin.edom.questionnaire.toggle', $questionnaire) }}"
                                    method="POST" class="d-inline">
                                    @csrf @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-outline-warning"
                                        title="{{ $questionnaire->status === 'ACTIVE' ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <em
                                            class="icon ni ni-{{ $questionnaire->status === 'ACTIVE' ? 'cross' : 'check' }}"></em>
                                    </button>
                                </form>
                                <form action="{{ route('admin.edom.questionnaire.destroy', $questionnaire) }}"
                                    method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                        <em class="icon ni ni-trash"></em>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                <em>Tidak ada kuesioner. <a href="{{ route('admin.edom.questionnaire.create') }}">Buat
                                        kuesioner baru</a></em>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')

    @endpush
</x-main-layout>