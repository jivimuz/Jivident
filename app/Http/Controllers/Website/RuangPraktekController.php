<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\RuangPraktek;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RuangPraktekController extends Controller
{
    public function index()
    {
        return view('pages/ruang_praktek/index');
    }

    public function getRooms()
    {
        // No longer joined to m_users for a doctor name — a room isn't
        // assigned to a single doctor anymore, see Jadwal Dokter instead.
        $data = RuangPraktek::where('klienfk', Auth::user()->klienfk ?? 1)->get();

        return response()->json(['message' => 'Get data sukses', 'data' => $data], 200);
    }

    public function addRoom()
    {
        try {
            RuangPraktek::insert([
                'klienfk' => Auth::user()->klienfk ?? 1,
                'name' => 'Ruang - '.substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 2).
                    rand(0, 10),
                'code' => substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 2),
                'created_at' => Carbon::now(),
            ]);

            return response()->json(['message' => 'Tambah ruangan sukses'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal menyimpan data: '.$e->getMessage(),
                'message' => 'Gagal menyimpan data: '.$e->getMessage(),
            ], 400);
        }
    }

    public function updateRoom(Request $request)
    {
        $request->validate([
            'roomsid' => 'required|integer',
            'roomsname' => 'required|string|max:50',
            'roomscd' => 'required|string|max:5',
        ]);

        $room = RuangPraktek::where('klienfk', Auth::user()->klienfk ?? 1)->find($request->roomsid);

        if (! $room) {
            return response()->json(['error' => 'Room not found'], 404);
        }

        $room->name = $request->roomsname;
        $room->code = $request->roomscd;
        $room->save();

        return response()->json([
            'success' => true,
            'message' => 'Room updated successfully',
            'data' => $room,
        ]);
    }

    public function deleteRoom(Request $request)
    {
        $room = RuangPraktek::where('klienfk', Auth::user()->klienfk ?? 1)->find($request->roomsid);
        if ($room) {
            $room->delete();

            return response()->json(['message' => 'Delete success'], 200);
        }

        return response()->json(['message' => 'Tidak ada data', 'error' => 'Tidak ada data'], 400);
    }
}
