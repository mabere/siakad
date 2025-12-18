<x-main-layout>
    @section('title', 'Daftar Surat Masuk')

    @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="nk-block nk-block-lg">
        <div class="nk-block-head">
            <div class="nk-block-head-content">
                <h4 class="nk-block-title">@yield('title')</h4>
                <p>Halaman ini menampilkan permintaan surat masuk beserta statusnya.</p>
            </div>
        </div>
        <div class="card card-bordered card-preview">
            <table class="table table-tranx">
                <thead>
                    <tr class="tb-tnx-head bg-primary">
                        <th class="tb-tnx-id py-3 text-white"><span>ID</span></th>
                        <th class="tb-tnx-info py-3 text-white"><span>Ajuan Dari</span></th>
                        <th class="tb-tnx-info py-3 text-white"><span>Jenis Surat</span></th>
                        <th class="tb-tnx-info py-3 text-white"><span>Status</span></th>
                        <th class="tb-tnx-info py-3 text-white"><span>Action</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ajuanSurat as $surat)
                    <tr class="tb-tnx-item">
                        <td class="tb-tnx-id"><a href="#"><span>{{ $surat->id }}</span></a></td>
                        <td class="tb-tnx-info"><span class="title">{{ $surat->user->name }}</span></td>
                        <td>{{ $surat->letterType->name }}</td>
                        <td class="tb-tnx-amount">
                            <div class="tb-tnx-total">
                                <span class="badge {{ match($surat->status) {
                                    'submitted' => 'bg-warning',
                                    'processing' => 'bg-info',
                                    'approved' => 'bg-success',
                                    'rejected' => 'bg-danger',
                                    default => 'bg-secondary'
                                } }}">{{ ucfirst($surat->status) }}</span>
                            </div>
                        </td>
                        <td>
                            @if ($surat->status === 'submitted')
                            <a href="{{ route('kaprodi.request.surat-masuk.show', $surat) }}"
                                class="btn btn-primary btn-sm">Review</a>
                            @else
                            <a href="{{ route('kaprodi.request.surat-masuk.show', $surat) }}"
                                class="btn btn-info btn-sm">Lihat Detail</a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-warning">Belum ada surat masuk.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{ $ajuanSurat->links() }}

</x-main-layout>