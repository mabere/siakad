<x-main-layout>
    @section('title', 'Verifikasi Pengajuan Ujian Skripsi')
    <div class="components-preview wide-lg mx-auto">
        <div class="nk-block nk-block-lg">
            <div class="nk-block-head">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h4 class="nk-block-title p-2">Daftar Pengajuan Ujian</h4>
                    </div>
                </div>
            </div>
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <table class="datatable-init nowrap table">
                        <thead>
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Mahasiswa</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    NIM</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Judul Skripsi</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Jenis Ujian</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pendingExams as $exam)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $exam->thesis->student->nama_mhs }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $exam->thesis->student->nim }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $exam->thesis->title }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ ucfirst($exam->exam_type) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap"><span class="badge bg-warning">{{ $exam->status
                                        }}</span></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('ktu.thesis.exam.verify.exam', $exam->id)  }}"
                                        class="text-indigo-600 hover:text-indigo-900">Lihat & Verifikasi</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <div class="mt-4">
                            {{ $pendingExams->links() }}
                        </div>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <x-custom.sweet-alert />
</x-main-layout>
