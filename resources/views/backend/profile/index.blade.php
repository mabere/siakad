<x-main-layout>
    @section('title', 'Profil Pengguna')

    <!-- Header -->
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between g-3">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">@yield('title')</h3>
            </div>
            <div class="nk-block-head-content">
                <a href="{{ route($backRoute) }}" class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
                    <em class="icon ni ni-arrow-left"></em>
                    <span>Kembali</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Notifikasi -->
    <x-custom.sweet-alert />

    <!-- Konten Utama -->
    <div class="nk-block">
        <div class="row g-gs">

            <!-- Sidebar Kiri: Foto dan Info Ringkas -->
            <div class="col-lg-4 col-xl-4 col-xxl-3">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="user-card user-card-s2 text-center">
                            <div class="user-avatar lg bg-primary">
                                <img src="{{ asset('storage/images/' . $folder . '/' . $photo) }}" alt="Foto Pengguna">
                            </div>
                            <div class="user-info mt-2">
                                <h5>{{ $titleName }}</h5>
                                @foreach ($badges as $badge)
                                <span class="badge my-1 bg-{{ $badge['class'] }}">{{ $badge['label'] }}</span><br>
                                <span class="badge text-white bg-primary sub-text">{{ $badge['value'] }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel Tab Konten -->
            <div class="col-lg-8 col-xl-8 col-xxl-9">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <!-- Navigasi Tab -->
                        <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#tab-detail">
                                    <em class="icon ni ni-user"></em>
                                    <span>Detail</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#tab-photo">
                                    <em class="icon ni ni-camera"></em>
                                    <span>Ubah Foto</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#tab-password">
                                    <em class="icon ni ni-lock-alt"></em>
                                    <span>Ubah Password</span>
                                </a>
                            </li>
                        </ul>

                        <!-- Isi Tab -->
                        <div class="tab-content" id="profileTabContent">

                            <!-- Detail Profile (dari komponen blade) -->
                            <x-profile.tab-section :active="'detail'" :fields="$fields" />

                            <!-- Tab Ubah Foto -->
                            <div class="tab-pane fade" id="tab-photo" role="tabpanel">
                                @include('backend.profile.partials.update-photo', [
                                'photoRoute' => $photoRoute,
                                'folder' => $folder,
                                'photo' => $photo
                                ])
                            </div>

                            <!-- Tab Ubah Password -->
                            <div class="tab-pane fade" id="tab-password" role="tabpanel">
                                @include('backend.profile.partials.update-password', ['passwordRoute' =>
                                $passwordRoute])
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Script Preview -->
    @include('backend.profile.partials.preview-script')
</x-main-layout>
