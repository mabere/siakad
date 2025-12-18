<?php

namespace App\Http\Controllers\Backend;

use App\Models\Room;
use App\Models\Building;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BuildingAndRoomController extends Controller
{
    public function index()
    {
        $gedung = Building::all();
        return view('backend.fasilitas.gedung', compact('gedung'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'lokasi' => 'required',
        ]);

        $building = new Building;
        $building->nama = $request->nama;
        $building->lokasi = $request->lokasi;
        $building->save();

        return redirect()->route('admin.gedung.index')->with('success', 'Data Gedung berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required',
            'lokasi' => 'required',
        ]);
        $building = Building::findOrFail($id);

        $building->nama = $request->nama;
        $building->lokasi = $request->lokasi;
        $building->save();

        return redirect()->route('admin.gedung.index')->with('update', 'Data Gedung berhasil diperbaharui.');
    }

    public function destroy($id)
    {
        $building = Building::findOrFail($id);
        $building->delete();
        return redirect()->route('admin.gedung.index')->with('delete', 'Data Gedung berhasil dihapus.');
    }

    public function indexRoom()
    {
        $ruangan = Room::with('building')->get();
        $building = Building::all();
        return view('backend.fasilitas.ruangan', compact('ruangan', 'building'));
    }

    public function storeRoom(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'building_id' => 'required',
            'nomor' => 'required',
        ]);

        $room = new Room;
        $room->name = $request->name;
        $room->building_id = $request->building_id;
        $room->nomor = $request->nomor;
        $room->save();

        return redirect()->route('admin.ruangan.index')->with('success', 'Data Ruangan berhasil ditambahkan');
    }

    public function updateRoom(Request $request, Room $room)
    {
        $request->validate([
            'name' => 'required',
            'building_id' => 'required',
            'nomor' => 'required',
        ]);

        $room->update([
            'name' => $request->name,
            'building_id' => $request->building_id,
            'nomor' => $request->nomor
        ]);

        return redirect()->route('admin.ruangan.index')
            ->with('success', 'Data Ruangan berhasil diperbaharui.');
    }

    public function destroyRoom($id)
    {
        $room = Room::findOrFail($id);
        $room->delete();
        return redirect()->route('admin.ruangan.index')->with('success', 'Data Ruangan berhasil dihapus.');

    }

}