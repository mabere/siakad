<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class LogoServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {

        View::composer('*', function ($view) {
            // $logoUri = base64_encode(file_get_contents(public_path('images/logo_unilaki.png')));
            // $logoUri2 = base64_encode(file_get_contents(public_path('images/tutwuri.png')));
$logoUri = base64_encode(file_get_contents(asset('images/logo_unilaki.png')));
            $logoUri2 = base64_encode(file_get_contents(asset('images/tutwuri.png')));
            // Informasi Institusi
            $yayasan = 'YAYASAN LAKIDENDE RAZAK POROSI';
            $universitas = 'UNIVERSITAS LAKIDENDE UNAAHA';
            $alamat = 'Jl. Sultan Hasanuddin No. 234, Unaaha';
            $kab = 'Kabupaten Konawe-Sulawesi Tenggara';
            $telp = '(0408) 2421-777';

            $view->with([
                'logoUri' => $logoUri,
                'logoUri2' => $logoUri2,
                'yayasan' => $yayasan,
                'universitas' => $universitas,
                'alamat' => $alamat,
                'kab' => $kab,
                'telp' => $telp,
            ]);
        });


        View::composer('*', function ($view) {
            $user = Auth::user();
            $photoMhs = null;

            if ($user) {
                $photoFileName = $user->photo ?? 'student.png';
                $photoPath = asset('storage/images/mhs/' . $photoFileName);
                if (file_exists($photoPath)) {
                    $photoMhs = 'data:image/png;base64,' . base64_encode(file_get_contents($photoPath));
                } else {
                    $photoMhs = 'data:image/png;base64,' . base64_encode(file_get_contents(asset('storage/images/mhs/student.png')));
                }
            } else {
                // $photoMhs = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('storage/images/mhs/student.png')));
                $photoMhs = 'data:image/png;base64,' . base64_encode(file_get_contents(asset('storage/images/mhs/student.png')));
            }
            $view->with('photo_base64', $photoMhs);
        });
    }
}
