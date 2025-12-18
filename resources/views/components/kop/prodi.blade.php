@props([
'fakultas',
'prodi',
'nomorTelepon',
'website',
'email',
'logoUri',
'logoUri2',
'alamat',
'kab',
])

<div class="header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <table class="kop" style="width: 100%;">
        <tr>
            <td class="logo1" style="width: 14%; text-align: left;">
                <img src="data:image/png;base64,{{ $logoUri }}" alt="Logo Kiri" class="logo" width="80%" />
            </td>
            <td class="info" style="width: 70%; text-align: center;">
                <p style="line-height: 1.7rem">
                    <span style="font-size: 1.9rem"><b>{{ $yayasan }}</b></span><br>
                    <span style="font-size: 1.8rem"><b>{{ $universitas }}</b></span><br>
                    <span style="font-size: 1.5rem;text-transform: uppercase;"><b>{{ $fakultas
                            }}</b></span><br>
                    <span style="font-size: 1.4rem;text-transform: uppercase;"><b>{{ $prodi
                            }}</b></span><br>
                </p>
                <span>Website: {{ $website }}, Telp: {{ $nomorTelepon }} | Email: {{ $email }}</span><br>
                <span>Alamat: {{ $alamat }}, {{ $kab }}</span>
            </td>
            <td class="logo2" style="width: 14%; text-align: right;">
                <img src="data:image/png;base64,{{ $logoUri2 }}" alt="Logo Kanan" class="logo" width="80%" />
            </td>
        </tr>
    </table>
</div>
<hr style="margin-top:-1.35rem;border-bottom: 2px solid black;">