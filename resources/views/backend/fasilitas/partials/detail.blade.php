<div>
    <h6>Nama Gedung:</h6>
    <p>{{ $building->nama }}</p>
    <h6>Lokasi:</h6>
    <p>{{ $building->lokasi }}</p>
    <h6>Dibuat Pada:</h6>
    <p>{{ $building->created_at->format('d M Y, H:i') }}</p>
</div>
