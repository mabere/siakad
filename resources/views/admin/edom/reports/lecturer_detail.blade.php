<x-main-layout>
    @section('title', 'Detail Kinerja Dosen')

    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h4 class="nk-block-title">@yield('title'): {{ $lecturer->nama_dosen }}</h4>
                <div class="nk-block-des text-soft">
                    <p><strong>Tahun Akademik: {{ $currentAcademicYear->ta }} - {{
                            $currentAcademicYear->semester}}<br>Program
                            Studi: {{ $lecturer->department->nama }}</strong></p>
                </div>
            </div>
        </div>
    </div>

    <div class="nk-block">
        <!-- Rata-rata Per Kategori -->
        <div class="card card-bordered card-preview">
            <div class="card-inner">
                <div class="card-title-group align-start mb-3">
                    <div class="card-title">
                        <h6 class="title">Rata-rata Per Kategori</h6>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Kategori</th>
                                <th>Rata-rata</th>
                                <th>Jumlah Responden</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categoryAverages as $category)
                            <tr>
                                <td>{{ $loop->iteration }}.</td>
                                <td>{{ $category->category }}</td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar" role="progressbar"
                                            style="width: {{ ($category->average/5)*100 }}%">
                                            {{ number_format($category->average, 2) }}
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $category->respondent_count }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Detail Per Mata Kuliah -->
        <div class="card card-bordered card-preview mt-4">
            <div class="card-inner">
                <div class="card-title-group align-start mb-3">
                    <div class="card-title">
                        <h6 class="title">Detail Per Mata Kuliah</h6>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Mata Kuliah</th>
                                <th>Responden</th>
                                @foreach($categoryAverages as $category)
                                <th>{{ $category->category }}</th>
                                @endforeach
                                <th>Rata-rata</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($courseStats as $stat)
                            <tr>
                                <td>{{ $stat['course']->name }}</td>
                                <td>{{ $stat['respondents'] }}</td>
                                @foreach($categoryAverages as $category)
                                <td>{{ $stat['categories'][$category->category] ?? '-' }}</td>
                                @endforeach
                                <td>{{ $stat['overall'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-main-layout>
