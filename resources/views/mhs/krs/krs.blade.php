<x-main-layout>
    @section('title', 'Data Kartu Rencana Studi (KRS)')
    <x-custom.sweet-alert />
    <div class="components-preview wide-lg mx-auto">
        <div class="nk-block nk-block-lg">
            <div class="nk-block-head">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h4 class="nk-block-title">@yield('title')</h4>
                    </div>
                    <div class="nk-block-head-content ">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <div class="toggle-expand-content">
                                <a href="{{ route('dashboard') }}">
                                    <em class="icon ni ni-reply"></em>
                                    <span>Add</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="mahasiswa">
                <div class="container">
                    @if ($mahasiswa->kelas_id === null)
                    <td>Tidak dapat mengisi KRS, karena kelas anda belum ditentukan</td>
                    @else
                    <div class="row">
                        <div class="col-12">
                            <h5>Tahun Akademik: {{$ta->ta}}/{{$ta->semester}}</h5>
                        </div>
                    </div>
                    <div class="row bio">
                        <div class="col-md-2">
                            <img src="/image/mhs/{{ $mahasiswa->photo }}" class="photo" alt="">
                        </div>
                        <div class="col-md-5">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td>Nama</td>
                                        <td>:</td>
                                        <td>{{$mahasiswa->nama_mhs}}</td>
                                    </tr>
                                    <tr>
                                        <td>NIM</td>
                                        <td>:</td>
                                        <td>{{$mahasiswa->nim}}</td>
                                    </tr>
                                    <tr>
                                        <td>Fakultas</td>
                                        <td>:</td>
                                        <td>{{$fakultas->faculty->nama}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-5">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td>Program Studi</td>
                                        <td>:</td>
                                        <td>{{$mahasiswa->department->nama}}</td>
                                    </tr>
                                    <tr>
                                        <td>Kelas</td>
                                        <td>:</td>
                                        <td>{{$mahasiswa->kelas->name}}</td>
                                    </tr>
                                    <tr>
                                        <td>Dosen PA</td>
                                        <td>:</td>
                                        <td>{{$dosen->lecturer->nama_dosen}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <a type="submit" href="{{ route('student.krs.create') }}" class="btn btn-primary mt-3 mb-3"
                                data-toggle="tooltip" data-placement="top" title="Tambah KRS">
                                <em class="icon ni ni-note-add-fill"></em>
                            </a>
                            <a type="submit" href="{{ route('student.krs.show', $mahasiswa->id) }}" target="_blank"
                                class="btn btn-success mt-3 mb-3" data-toggle="tooltip" data-placement="top"
                                title="Cetak KRS">
                                <i class="icon ni ni-printer-fill"></i>
                            </a>
                        </div>
                        <div class="col-md-8">
                            @if (session('status'))
                            <div class="alert alert-success mt-3">
                                {{ session('status') }}
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 daftarKrs">
                            <table class="table table-striped">
                                <thead class="thead-dark">
                                    <th>No</th>
                                    <th>Semester</th>
                                    <th>Kode</th>
                                    <th>Mata Kuliah</th>
                                    <th>SKS</th>
                                    <th>Dosen</th>
                                    <th>Kelas</th>
                                    <th>Hari</th>
                                    <th>Waktu</th>
                                    <th>Ruangan</th>
                                    <th>Hapus</th>
                                </thead>
                                <tbody>
                                    @forelse ($items as $index => $item)
                                    <tr>
                                        <td>{{ $index+1 }}</td>
                                        <td width="9%">{{ $item->schedule['course']['smt'] }} ({{
                                            $item->schedule['course']['semester'] }})</td>
                                        <td>{{ $item->schedule['course']['code'] }}</td>
                                        <td>{{ $item->schedule['course']['name'] }}</td>
                                        <td>{{ $item->schedule['course']['sks'] }}</td>
                                        <td>
                                            @if($item->schedule->lecturersInSchedule->first())
                                            {{ $item->schedule->lecturersInSchedule->first()->nama_dosen }}
                                            @else
                                            Belum ditentukan
                                            @endif
                                        </td>
                                        <td>{{ $item->schedule['kelas']['name'] }}</td>
                                        <td>{{ $item->schedule['hari'] }}</td>
                                        <td>{{ $item->schedule['waktu'] }}</td>
                                        <td>{{ $item->schedule['room']['name'] }}</td>
                                        <td>
                                            <form action="{{ route('student.krs.destroy', $item->id) }}" method="post"
                                                class="d-inline">
                                                @csrf
                                                @method('delete')
                                                <button class="btn btn-danger btn-sm" data-toggle="tooltip"
                                                    data-placement="top" title="Hapus">
                                                    <i class="icon ni ni-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td>Belum mengambil KRS.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <table border="0">
                                <tbody>
                                    <tr>
                                        <td>Total SKS yang diambil</td>
                                        <td>:</td>
                                        <td>{{ $totalSks }}</td>
                                    </tr>
                                    <tr>
                                        <td>Maksimal SKS yang diambil</td>
                                        <td>:</td>
                                        <td>{{ $maxSks }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
</x-main-layout>
