<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\RuangPraktek;
use App\Models\User;
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
        $data = RuangPraktek::select([
            'm_ruangpraktek.*',
            'a.display_name as dokter'
        ])->where('klienfk', Auth::user()->klienfk ?? 1)
            ->leftJoin('m_users as a', 'a.id', 'm_ruangpraktek.fk_dokter')->get();
        return response()->json(['message' => 'Get data sukses', 'data' => $data], 200);
    }

    public function addRoom()
    {
        try {
            RuangPraktek::insert([
                'klienfk' => Auth::user()->klienfk ?? 1,
                'name' => 'Ruang - ' . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 2) .
                    rand(0, 10),
                'code' => substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 2),
                'fk_dokter' => null,
                'created_at' => Carbon::now(),
            ]);
            return response()->json(['message' => 'Tambah ruangan sukses'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal menyimpan data: ' . $e->getMessage(),
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 400);
        }
    }

    function getDokterList(Request $request)
    {
        $userList = User::select([
            'm_users.id',
            'm_employee.fullname',
            'm_employee.nik',
        ])
            ->join('m_employee', 'm_employee.id', '=', 'm_users.fk_employee')
            ->where('m_users.id', '!=', Auth::user()->id)
            ->where('m_users.id', '!=', 1)
            ->where('m_employee.is_pemeriksapasien', 1)
            ->where(function ($query) use ($request) {
                $query->where('m_employee.fullname', 'like', '%' . $request->params . '%')
                    ->orWhere('m_employee.nik', 'like', '%' . $request->params . '%')
                    ->orWhere('m_users.id', $request->currentDr);
            })
            ->limit(10)
            ->get();
        return response()->json(["message" => 'success', "data" => $userList], 200);
    }

    public function updateRoom(Request $request)
    {
        $request->validate([
            'roomsid' => 'required|integer',
            'roomsname' => 'required|string|max:50',
            'roomsdr' => 'required|integer',
            'roomscd' => 'required|string|max:5',
        ]);

        $room = RuangPraktek::find($request->roomsid);

        if (!$room) {
            return response()->json(['error' => 'Room not found'], 404);
        }

        $room->name = $request->roomsname;
        $room->fk_dokter = $request->roomsdr;
        $room->code = $request->roomscd;
        $room->save();

        return response()->json([
            'success' => true,
            'message' => 'Room updated successfully',
            'data' => $room
        ]);
    }

    public function deleteRoom(Request $request)
    {
        $room = RuangPraktek::find($request->roomsid);
        if ($room) {
            $room->delete();
            return response()->json(["message" => 'Delete success'], 200);
        }
        return response()->json(["message" => 'Tidak ada data', "error" => 'Tidak ada data'], 400);
    }
}
