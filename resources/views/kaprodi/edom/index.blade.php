<x-main-layout>
    @section('title', 'Edom Program Studi')
    <div class="container-fluid px-4">
        <div class="header bg-gradient-primary-to-secondary p-2 rounded-top-3 mb-4">
            <h3 class="text-white mb-3 fw-bold">
                <i class="icon ni ni-growth me-2"></i>Dashboard EDOM
            </h3>
            <h5 class="text-black mb-0">Program Studi {{ $department->nama }}</h5>
        </div>

        <!-- Statistics Cards Grid -->
        @if(isset($totalSchedules))
        <div class="row g-4 mb-5">
            <!-- Total Courses Card -->
            <div class="col-12 col-xl-4 col-md-6">
                <div class="card bg-gradient-primary-to-info shadow-lg h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-white bg-opacity-25 p-2 rounded-circle me-3">
                            <i class="icon ni ni-book-read text-white"></i>
                        </div>
                        <div>
                            <h5 class="card-title text-white mb-1">Total Mata Kuliah</h5>
                            <p class="card-text display-6 text-white mb-0">{{ $totalSchedules }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Evaluated Courses Card -->
            <div class="col-12 col-xl-4 col-md-6">
                <div class="card bg-gradient-success-to-teal shadow-lg h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-white bg-opacity-25 p-2 rounded-circle me-3">
                            <i class="icon ni ni-check-circle fa-2x text-white"></i>
                        </div>
                        <div>
                            <h5 class="card-title text-white mb-1">Sudah Dievaluasi</h5>
                            <p class="card-text display-6 text-white mb-0">{{ $evaluatedSchedules }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Evaluation Card -->
            <div class="col-12 col-xl-4 col-md-6">
                <div class="card bg-gradient-warning-to-danger shadow-lg h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-white bg-opacity-25 p-2 rounded-circle me-3">
                            <i class="icon ni ni-alert fa-2x text-white"></i>
                        </div>
                        <div>
                            <h5 class="card-title text-white mb-1">Belum Dievaluasi</h5>
                            <p class="card-text display-6 text-white mb-0">{{ $nonEvaluatedSchedules }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Enhanced Data Table -->
        <div class="card border-0 shadow-lg">
            <div class="card-header py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-black text-left"><i class="icon ni ni-bar-chart me-2"></i>Daftar Evaluasi Mata
                        Kuliah
                    </h5>
                    <div>
                        <a href="{{ route('kaprodi.edom.export', ['type' => 'pdf']) }}" class="btn btn-outline-warning">
                            <i class="icon ni ni-file-pdf me-1"></i>Export</a>
                        <a href="{{ route('kaprodi.edom.export', ['type' => 'excel']) }}"
                            class="btn btn-outline-success"><i class="icon ni ni-file-xls me-1"></i>
                            Excel</a>
                    </div>
                </div>
            </div>

            <!-- Search and Filter Form -->
            <div class="card-body pt-2">
                <form id="searchForm" method="GET" action="{{ url()->current() }}">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="icon ni ni-search"></i></span>
                                <input type="text" class="form-control" name="search" id="liveSearch"
                                    placeholder="Cari mata kuliah..." value="{{ request('search') }}"
                                    autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="icon ni ni-calendar"></i></span>
                                <select class="form-select" name="year" id="filterYear">
                                    <option value="">Semua Tahun</option>
                                    @for($y = date('Y'); $y >= 2020; $y--)
                                    <option value="{{ $y }}" {{ request('year')==$y ? 'selected' : '' }}>
                                        {{ $y }}/{{ $y+1 }}
                                    </option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="icon ni ni-grid-alt"></i></span>
                                <select class="form-select" name="semester" id="filterSemester">
                                    <option value="">Semua Semester</option>
                                    <option value="Ganjil" {{ request('semester')=='Ganjil' ? 'selected' : '' }}>Ganjil
                                    </option>
                                    <option value="Genap" {{ request('semester')=='Genap' ? 'selected' : '' }}>Genap
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Table Content -->
                <div class="card-body">
                    <div class="table-responsive rounded-3">
                        <table class="table table-hover table-striped align-middle mb-0">
                            <thead class="bg-light-primary">
                                <tr>
                                    <th class="ps-4 d-none d-md-table-cell">No.</th>
                                    <th>Mata Kuliah</th>
                                    <th class="d-none d-md-table-cell">Dosen Pengampu</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center d-none d-md-table-cell">Respons</th>
                                    <th class="pe-4 text-end">Skor Rata-rata</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($schedules as $index => $schedule)
                                <tr class="transition-scale">
                                    <td class="ps-4 fw-medium d-none d-md-table-cell">{{ $index + 1 }}.</td>
                                    <td class="fw-semibold">
                                        <div class="d-block d-md-none small text-muted">#{{ $loop->iteration }}</div>{{
                                        $schedule->schedulable->name }}
                                    </td>
                                    <td class="d-none d-lg-table-cell">
                                        @foreach($schedule->lecturersInSchedule as $index => $lecturer)
                                        <span style="text-align: left" class="d-block badge bg-light text-dark mb-1">
                                            {{ $index + 1 }}. {{ $lecturer->nama_dosen }}
                                        </span>
                                        @endforeach
                                    </td>
                                    <td class="text-center">
                                        @if($schedule->responses->count() > 0)
                                        <span class="badge bg-success-gradient rounded-pill py-2 px-3">
                                            <i class="icon ni ni-check-circle me-2"></i>Sudah
                                        </span>
                                        @else
                                        <span class="badge bg-danger-gradient rounded-pill py-2 px-3">
                                            <i class="icon ni ni-clock me-2"></i>Belum
                                        </span>
                                        @endif
                                    </td>
                                    <td class="text-center fw-bold">
                                        {{ $schedule->responses->count() }}
                                    </td>
                                    <td class="pe-4 text-end">
                                        @if($schedule->responses->count() > 0)
                                        @foreach($scheduleAverages[$schedule->id] as $category => $avg)
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="me-3">{{ $category }}:</span>
                                            <span class="badge bg-primary rounded-pill px-3">{{ $avg }}</span>
                                        </div>
                                        @endforeach
                                        @else
                                        <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-4">
                        <nav aria-label="Page navigation">
                            {{ $schedules->links('pagination::bootstrap-5', [
                            'list_classes' => 'pagination flex-wrap justify-content-center',
                            'disabled_class' => 'page-item disabled',
                            'active_class' => 'page-item active',
                            'link_class' => 'page-link',
                            'disabled_previous_next' => true,
                            'previous_label' => '<i class="fas fa-chevron-left"></i>',
                            'next_label' => '<i class="fas fa-chevron-right"></i>'
                            ]) }}
                        </nav>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('kaprodi.edom.reports') }}"
                            class="btn btn-md btn-gradient-primary-to-secondary text-white py-3">
                            <i class="icon ni ni-eye me-1"></i> Selengkapnya
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('kaprodi.edom.style')

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const liveSearch = document.getElementById('liveSearch');
            const filterYear = document.getElementById('filterYear');
            const filterSemester = document.getElementById('filterSemester');
            const searchForm = document.getElementById('searchForm');
            let timer;

            // Live Search Function
            function handleFilter() {
                document.getElementById('loadingIndicator').style.display = 'block';
                clearTimeout(timer);
                timer = setTimeout(() => {
                    searchForm.submit();
                }, 500);
            }

            // Event Listeners
            liveSearch.addEventListener('input', handleFilter);
            filterYear.addEventListener('change', handleFilter);
            filterSemester.addEventListener('change', handleFilter);

        });
    </script>
    @endpush
</x-main-layout>
