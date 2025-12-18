@props(['signatures'])

<!--<p style="text-align: right; margin-right: 6rem">Unaaha, {{ now()->format('d F Y') }}</p>-->
<!--<table style="width: 100%; margin-top: 30px;">-->
<!--    <tr>-->
<!--        @foreach($signatures as $signature)-->
<!--        <td style="width: {{ 100 / count($signatures) }}%; text-align: left;">-->
<!--            <p>{{ $signature['jabatan'] }},</p>-->
<!--            <br><br><br>-->
<!--            <p><u><b>{{ $signature['nama'] }}</b></u><br>-->
<!--                @if(isset($signature['nip']))-->
<!--                NIDN/NIP. {{ $signature['nip'] }}</p>-->
<!--            @endif-->
<!--        </td>-->
<!--        @endforeach-->
<!--    </tr>-->

<p style="text-align: right; margin-right: 6rem">Unaaha, {{ now()->format('d F Y') }}</p>
<table style="width: 100%; margin-top: 0px;">
    <tr>
        @foreach($signatures as $signature)
        <td style="width: {{ 100 / count($signatures) }}%; text-align: left;">
            <p>{{ $signature['jabatan'] }},</p>
            @if(isset($signature['qrcode']))
            <img src="{{ $signature['qrcode'] }}" alt="QR Code" style="margin-top:-15px; width:90px;">
            @endif
            <p style="margin-top:-7px"><u><b>{{ $signature['nama'] }}</b></u><br>
                @if(isset($signature['nip']))
                NIDN/NIP. {{ $signature['nip'] }}
                @endif
            </p>

        </td>
        @endforeach
    </tr>
</table>
