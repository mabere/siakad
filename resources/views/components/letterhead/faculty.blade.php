@props(['faculty', 'logoUri', 'logoUri2'])

<div class="header">
    <table>
        <tr>
            <td class="logo1"><img src="data:image/png;base64,{{ $logoUri }}" alt="Logo"></td>
            <td class="info">
                <h2 style="margin: 0;padding:0">YAYASAN LAKIDENDE RAZAK POROSI</h2>
                <h3 style="margin: 0;padding:0">UNIVERSITAS LAKIDENDE UNAAHA</h3>
                <h4 style="margin: 0;padding:0">FAKULTAS {{ strtoupper($faculty->nama) }}</h4>
                Jl. Sultan Hasanuddin No. 234 | Telp. (0408) 2421-777<br>
                Konawe Sulawesi Tenggara | Email: {{ $faculty->email }}
            </td>
            <td class="logo2"><img src="data:image/png;base64,{{ $logoUri2 }}" alt="Logo"></td>
        </tr>
    </table>
</div>