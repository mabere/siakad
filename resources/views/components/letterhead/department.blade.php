@props(['department'])

<div class="header">
    <table>
        <tr>
            <td class="logo1"><img src="{{ asset('images/logo_unilaki.png') }}" alt="Logo"></td>
            <td class="info">
                <h2 style="margin: 0;padding:0">YAYASAN LAKIDENDE RAZAK POROSI</h2>
                <h3 style="margin: 0;padding:0">UNIVERSITAS LAKIDENDE UNAAHA</h3>
                <h4 style="margin: 0;padding:0">{{ strtoupper($department->faculty->nama) }}</h4>
                <h5 style="margin: 0;padding:0">{{ strtoupper($department->nama) }}</h5>
                Jl. Sultan Hasanuddin No. 234 | Telp. (0408) 2421-777</br>
                Konawe Sulawesi Tenggara | Email: {{ strtolower(Str::slug($department->nama, '')) }}@fkip-unilaki.ac.id
            </td>
            <td class="logo2"><img src="{{ asset('images/logo-yayasan.png') }}" alt="Logo"></td>
        </tr>
    </table>
</div>
