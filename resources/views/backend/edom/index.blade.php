<x-main-layout>
    @section('title', 'Manajemen EDOM')

    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">@yield('title')</h3>
                <div class="nk-block-des text-soft">
                    <p>Kelola kuesioner dan pengaturan EDOM</p>
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
                    <div class="col-md-6">
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
                    <div class="col-md-6">
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
                <table class="datatable-init table">
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
                        @foreach($questionnaires as $questionnaire)
                        <tr>
                            <td>{{ $questionnaire->title }}</td>
                            <td>{{ Str::limit($questionnaire->description, 50) }}</td>
                            <td>{{ $questionnaire->questions_count }}</td>
                            <td>
                                @if($questionnaire->status === 'ACTIVE')
                                <span class="badge bg-success">Aktif</span>
                                @else
                                <span class="badge bg-warning">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <a class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown">
                                        <em class="icon ni ni-more-h"></em>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <ul class="link-list-opt no-bdr">
                                            <li>
                                                <a href="{{ route('admin.edom.questionnaire.edit', $questionnaire) }}">
                                                    <em class="icon ni ni-edit"></em>
                                                    <span>Edit</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#"
                                                    onclick="event.preventDefault(); 
                                                   document.getElementById('toggle-form-{{ $questionnaire->id }}').submit();">
                                                    <em
                                                        class="icon ni ni-{{ $questionnaire->status === 'ACTIVE' ? 'cross' : 'check' }}"></em>
                                                    <span>{{ $questionnaire->status === 'ACTIVE' ? 'Nonaktifkan' :
                                                        'Aktifkan' }}</span>
                                                </a>
                                                <form id="toggle-form-{{ $questionnaire->id }}"
                                                    action="{{ route('admin.edom.questionnaire.toggle', $questionnaire) }}"
                                                    method="POST" class="d-none">
                                                    @csrf
                                                    @method('PUT')
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-main-layout>