<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\DaftarPasien;
use Illuminate\Http\Request;

class PrintController extends Controller
{
    public function antrianPasien($id)
    {
        $data = DaftarPasien::select([
            't_daftarpasien.id',
            't_daftarpasien.noregistrasi',
            't_daftarpasien.noantrian',
            'b.name as ruangan',
            'c.display_name as dokter',
        ])
            ->join('m_ruangpraktek as b', 'b.id', 't_daftarpasien.ruanganfk')
            ->join('m_users as c', 'c.id', 't_daftarpasien.dokterfk')
            ->where('t_daftarpasien.id', base64_decode(base64_decode($id)))->first();

        return view('print/antrian/antrian', compact('data'));
    }
}
