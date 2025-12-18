<table class="kop" style="width: 100%;">
    <tr>
        <td class="logo1" style="width: 14%; text-align: left; border: 0;">
            <img src="data:image/png;base64,{{ $logoUri }}" alt="Logo Kiri" class="logo" width="80%" />
        </td>
        <td class="info" style="width: 70%; text-align: center; border: 0;">
            <p style="font-size: 19px; font-weight: bold; text-transform: uppercase; margin: 0;">
                {{ $yayasan }}
            </p>
            <p style="font-size: 17px; font-weight: bold; text-transform: uppercase; margin: 0;">
                {{ $universitas }}
            </p>
            <p style="font-size: 15px; font-weight: bold; text-transform: uppercase; margin-bottom: -0.2rem;">
                {{ $fakultasNama }}
            </p>
            <p style="font-size: .9rem;margin-bottom:-.3rem;"><span>Website: {{ $fakultasWebsite }}, Telp: {{
                    $telp }} | Email: {{ $fakultasEmail }}</span>
            </p>
            <p style="font-size: .9rem;"><span>{{ $alamat }}, {{ $kab }}</span></p>
        </td>
        <td class="logo2" style="width: 14%; text-align: right; border: 0;">
            <img src="data:image/png;base64,{{ $logoUri2 }}" alt="Logo Kanan" class="logo" width="80%" />
        </td>
    </tr>
</table>