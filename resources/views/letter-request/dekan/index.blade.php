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
                    @forelse ($letterRequests as $letter)
                    <tr class="tb-tnx-item">
                        <td class="tb-tnx-id"><a href="#"><span>{{ $letter->id }}</span></a></td>
                        <td class="tb-tnx-info"><span class="title">{{ $letter->user->name }}</span></td>
                        <td>{{ $letter->letterType->name }}</td>
                        <td class="tb-tnx-amount">
                            <div class="tb-tnx-total">
                                <span class="amount badge {{ match($letter->status) {
                                    'submitted' => 'bg-warning',
                                    'processing' => 'bg-info',
                                    'approved' => 'bg-success',
                                    'rejected' => 'bg-danger',
                                    default => 'bg-secondary'} }}">
                                    {{ ucfirst($letter->status) }}
                                </span>
                            </div>
                        </td>
                        <td>
                            @php
                            $canAct = $letter->status === 'processing' &&
                            isset($letter->approval_flow['review']) &&
                            $letter->approval_flow['review'] === 'approved' &&
                            isset($letter->approval_flow['approve']) &&
                            $letter->approval_flow['approve'] === 'pending';
                            @endphp
                            @if ($canAct)
                            <div class="btn-group" role="group">
                                <a href="{{ route('dekan.request.surat-masuk.show', $letter) }}"
                                    class="btn btn-success btn-sm">Approve</a>
                                <a href="{{ route('dekan.request.surat-masuk.show', $letter) }}"
                                    class="btn btn-danger btn-sm">Reject</a>
                            </div>
                            @else
                            <a href="{{ route('dekan.request.surat-masuk.show', $letter) }}"
                                class="btn btn-primary btn-sm"><em class="icon ni ni-eye me-1"></em> Detail</a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td>Data belum ada.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{ $letterRequests->links() }}

</x-main-layout>