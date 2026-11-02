<?php

namespace App\Http\Controllers\Website;

use App\Helpers\ProductQtyHelper;
use App\Http\Controllers\Controller;
use App\Models\DaftarPasien;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ResepDispensingController extends Controller
{
    /**
     * Sama seperti StockOpnameController — satu-satunya lokasi stok yang
     * dipakai di seluruh app, tidak ada UI pemilih lokasi di mana pun.
     */
    private const LOCATION_ID = 1;

    public function index()
    {
        return view('pages/verifikasi_resep/index');
    }

    /**
     * Satu baris per KUNJUNGAN yang masih punya resep belum diverifikasi,
     * bukan per baris resep — menyerahkan resep ke pasien selalu satu paket
     * per kunjungan, bukan per obat.
     */
    public function getWorklist()
    {
        $klienfk = Auth::user()->klienfk ?? 1;

        $rows = DB::table('t_reseppasien as rp')
            ->join('t_daftarpasien as dp', 'dp.id', 'rp.daftarpasien_id')
            ->join('m_pasien as p', 'p.id', 'rp.pasien_id')
            ->where('rp.klienfk', $klienfk)
            ->where('rp.is_processed', false)
            ->whereNull('rp.deleted_at')
            ->groupBy('rp.daftarpasien_id', 'dp.noregistrasi', 'dp.tglregistrasi', 'p.nama', 'p.norm')
            ->selectRaw('rp.daftarpasien_id, dp.noregistrasi, dp.tglregistrasi, p.nama as pasien_nama, p.norm as pasien_norm, COUNT(*) as jumlah_item, SUM(rp.subtotal) as total_harga')
            ->orderBy('dp.tglregistrasi')
            ->get();

        return response()->json(['message' => 'success', 'data' => $rows], 200);
    }

    /**
     * Detail satu kunjungan sebelum diverifikasi — tiap baris resep beserta
     * stok sistem saat ini per obat, supaya farmasi bisa cek dulu sebelum
     * konfirmasi menyerahkan.
     */
    public function detail($daftarpasienId)
    {
        $klienfk = Auth::user()->klienfk ?? 1;

        if (! DaftarPasien::where('id', $daftarpasienId)->where('klienfk', $klienfk)->exists()) {
            return response()->json(['message' => 'error', 'error' => 'Kunjungan tidak ditemukan'], 404);
        }

        $resepRows = DB::table('t_reseppasien')
            ->where('daftarpasien_id', $daftarpasienId)
            ->where('klienfk', $klienfk)
            ->where('is_processed', false)
            ->whereNull('deleted_at')
            ->orderBy('r_num')
            ->get();

        if ($resepRows->isEmpty()) {
            return response()->json(['message' => 'success', 'data' => []], 200);
        }

        $ingredientRows = DB::table('t_reseppasienracikan')
            ->whereIn('reseppasien_id', $resepRows->pluck('id'))
            ->get()
            ->groupBy('reseppasien_id');

        $helper = app(ProductQtyHelper::class);

        $data = $resepRows->map(function ($row) use ($ingredientRows, $helper) {
            if ($row->type === 'racik') {
                $ingredients = collect($ingredientRows[$row->id] ?? [])->map(function ($ing) use ($helper) {
                    return [
                        'product_id' => $ing->product_id,
                        'product_name' => $ing->product_name,
                        'qty' => (float) $ing->qty,
                        'stok_sistem' => $helper->getStockBalance((int) $ing->product_id, self::LOCATION_ID),
                    ];
                })->values();

                return [
                    'id' => $row->id,
                    'type' => $row->type,
                    'r_num' => (int) $row->r_num,
                    'nama' => $row->nama_racikan,
                    'qty' => (float) $row->qty_buat,
                    'satuan' => $row->unit,
                    'subtotal' => (float) $row->subtotal,
                    'ingredients' => $ingredients,
                ];
            }

            return [
                'id' => $row->id,
                'type' => $row->type,
                'r_num' => (int) $row->r_num,
                'nama' => $row->obat_name,
                'qty' => (float) $row->qty,
                'satuan' => $row->unit,
                'subtotal' => (float) $row->subtotal,
                'stok_sistem' => $row->obat_id ? $helper->getStockBalance((int) $row->obat_id, self::LOCATION_ID) : null,
                'ingredients' => [],
            ];
        })->values();

        return response()->json(['message' => 'success', 'data' => $data], 200);
    }

    /**
     * Verifikasi & serahkan SEMUA resep yang belum diproses untuk satu
     * kunjungan sekaligus, dalam satu transaksi atomik — kalau satu baris
     * saja stoknya kurang, seluruh dispensing kunjungan ini dibatalkan,
     * tidak ada "setengah diserahkan" yang stoknya sudah terpotong sebagian.
     */
    public function dispense($daftarpasienId)
    {
        $klienfk = Auth::user()->klienfk ?? 1;

        if (! DaftarPasien::where('id', $daftarpasienId)->where('klienfk', $klienfk)->exists()) {
            return response()->json(['message' => 'error', 'error' => 'Kunjungan tidak ditemukan'], 404);
        }

        $resepRows = DB::table('t_reseppasien')
            ->where('daftarpasien_id', $daftarpasienId)
            ->where('klienfk', $klienfk)
            ->where('is_processed', false)
            ->whereNull('deleted_at')
            ->get();

        if ($resepRows->isEmpty()) {
            return response()->json([
                'message' => 'error',
                'error' => 'Tidak ada resep yang perlu diverifikasi untuk kunjungan ini',
            ], 400);
        }

        $helper = app(ProductQtyHelper::class);

        try {
            DB::transaction(function () use ($resepRows, $helper) {
                foreach ($resepRows as $row) {
                    if ($row->type === 'racik') {
                        $ingredients = DB::table('t_reseppasienracikan')
                            ->where('reseppasien_id', $row->id)
                            ->get();

                        foreach ($ingredients as $ing) {
                            $helper->stockOutFefo([
                                'product_id' => (int) $ing->product_id,
                                'location_id' => self::LOCATION_ID,
                                'qty' => (float) $ing->qty,
                                'source' => 'transaction',
                            ]);
                        }
                    } elseif ($row->obat_id) {
                        // obat_id bisa null kalau dokter menulis nama obat bebas
                        // (tidak terhubung ke katalog produk) — tidak ada stok
                        // yang bisa dipotong untuk baris seperti ini, tapi tetap
                        // ditandai terverifikasi di bawah.
                        $helper->stockOutFefo([
                            'product_id' => (int) $row->obat_id,
                            'location_id' => self::LOCATION_ID,
                            'qty' => (float) $row->qty,
                            'source' => 'transaction',
                        ]);
                    }
                }

                DB::table('t_reseppasien')
                    ->whereIn('id', $resepRows->pluck('id'))
                    ->update([
                        'is_processed' => true,
                        'tgl_processed' => now(),
                        'updated_at' => now(),
                    ]);
            });

            return response()->json(['message' => 'success'], 200);
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], 400);
        }
    }
}
