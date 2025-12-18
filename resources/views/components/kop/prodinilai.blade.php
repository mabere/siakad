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

<div class="header"
    style="margin-top: -5rem;display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <table class="kop" style="width: 100%;">
        <tr>
            <td class="logo1" style="width: 14%; text-align: left;">
                <img src="data:image/png;base64,{{ $logoUri }}" alt="Logo Kiri" class="logo" width="80%" />
            </td>
            <td class="info" style="width: 70%; text-align: center;">
                <p style="font-family: 'Times New Roman', Times, serif;">
                    <span style="font-size: 1.37rem"><b>{{ $yayasan }}</b></span><br>
                    <span style="font-size: 1.27rem"><b>{{ $universitas }}</b></span><br>
                    <span style="font-size: 1.14rem;text-transform: uppercase;"><b>{{ $fakultas
                            }}</b></span><br>
                    <span style="font-size: 1rem;text-transform: uppercase;"><b>{{ $prodi
                            }}</b></span><br>
                    <span>Website: {{ $website }}, Telp: {{ $nomorTelepon }} | Email: {{ $email }}</span><br>
                    <span>Alamat: {{ $alamat }}, {{ $kab }}</span>
                </p>
            </td>
            <td class="logo2" style="width: 14%; text-align: right;">
                <img src="data:image/png;base64,{{ $logoUri2 }}" alt="Logo Kanan" class="logo" width="80%" />
            </td>
        </tr>
    </table>
    <hr style="margin-top: -1.45rem;border-bottom: 2px solid black;">
</div>