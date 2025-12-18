<x-main-layout>
    @section('title', 'Detail Kelas')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Data @yield('title') {{$item->nama}}</h4>
                </div>
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                    @endif
                    <div class="row">
                        <div class="col-md-4">
                            <p>Nama Kelas: {{$item->name}}</p>
                            <p>Program Studi: {{$item->department->nama}}</p>
                        </div>
                        <div class="col-md-4">
                            <p>Dosen Wali: {{$item->lecturer->nama_dosen}}</p>
                            <p>Tahun Angkatan: {{$item->angkatan}}</p>
                        </div>
                        <div class="col-md-4">
                            <p>Jumlah Mahasiswa: {{$jumlahMahasiswa}}</p>
                        </div>
                    </div><br>
                    <div class="row mt-3">
                        <div class="card-header">
                            <div class="ms-auto">
                                <a href="{{ route('admin.kelas.index') }}" class="btn btn-success pull-left">
                                    <i class="fa fa-arrow-left"></i> Back </a>
                                <a type="submit" href="{{ url('admin/kelas/'. $item->id . '/create') }}"
                                    class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Tambah </a>
                            </div>
                        </div>
                    </div><br>
                    <div class="table-responsive">
                        <table id="zero_config" class="table table-striped table-bordered">
                            <thead class="bg-success">
                                <th>No</th>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th>Program Studi</th>
                                <th>Angkatan</th>
                                <th>Action</th>
                            </thead>
                            <tbody>
                                @forelse ($mahasiswa as $index => $items)
                                <tr>
                                    <td class="text-center">{{ $index+1 }}</td>
                                    <td class="text-center">{{ $items->nim }}</td>
                                    <td class="text-left">{{ $items->nama_mhs }}</td>
                                    <td>{{ $items->department->nama }}</td>
                                    <td class="text-center">{{ $items->kelas->angkatan }}</td>
                                    <td class="text-center">
                                        <form action="{{ route('admin.kelas.destroy', $items->id) }}" method="post"
                                            class="d-inline">
                                            <input type="hidden" value="{{ $items->id }}" name="id">
                                            <input type="hidden" value="{{$idKelas}}" name="idKelas">
                                            <input type="hidden" value="{{$jumlahMahasiswa}}" name="jumlahMahasiswa">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger text-white btn-sm">
                                                <i class="icon ni ni-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-danger text-center">Data tidak tersedia</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-main-layout>