<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SupplierController extends Controller
{
    public function index()
    {
        return view('pages/supplier/index');
    }

    public function getData(Request $request)
    {
        $query = DB::table('m_suppliers')->whereNull('deleted_at')->where('klienfk', Auth::user()->klienfk ?? 1);

        if ($request->search_name) {
            $query->where('name', 'like', '%'.$request->search_name.'%');
        }

        $total = (clone $query)->count();
        $search = $request->input('search.value', $request->search_name);
        if ($search) {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }
        $length = min(max((int) $request->input('length', 10), 1), 100);
        $page = $query->orderBy('name')->paginate($length, ['*'], 'page', (int) floor((int) $request->input('start', 0) / $length) + 1);

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $total,
            'recordsFiltered' => $page->total(),
            'data' => $page->items(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
        ]);

        try {
            DB::table('m_suppliers')->insert([
                // Sebelumnya hardcode 1 untuk semua cabang — supplier baru dari
                // cabang mana pun selalu tercatat sebagai milik cabang 1.
                'klienfk' => Auth::user()->klienfk ?? 1,
                'name' => $request->name,
                'address' => $request->address,
                'phone' => $request->phone,
                'email' => $request->email,
                'contact_person' => $request->contact_person,
                'is_active' => $request->is_active ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json(['status' => 'success', 'message' => 'Supplier berhasil ditambahkan.']);
        } catch (\Exception $e) {
            Log::error('Error Store Supplier: '.$e->getMessage());

            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan sistem.'], 500);
        }
    }

    public function show($id)
    {
        $data = DB::table('m_suppliers')->where('id', $id)->where('klienfk', Auth::user()->klienfk ?? 1)->first();
        if ($data) {
            return response()->json(['status' => 'success', 'data' => $data]);
        }

        return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan.'], 404);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            $updated = DB::table('m_suppliers')->where('id', $id)->where('klienfk', Auth::user()->klienfk ?? 1)->update([
                'name' => $request->name,
                'address' => $request->address,
                'phone' => $request->phone,
                'email' => $request->email,
                'contact_person' => $request->contact_person,
                'is_active' => $request->is_active ?? 1,
                'updated_at' => now(),
            ]);

            if (! $updated) {
                return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan.'], 404);
            }

            return response()->json(['status' => 'success', 'message' => 'Supplier berhasil diperbarui.']);
        } catch (\Exception $e) {
            Log::error('Error Update Supplier: '.$e->getMessage());

            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan sistem.'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $updated = DB::table('m_suppliers')->where('id', $id)->where('klienfk', Auth::user()->klienfk ?? 1)->update(['deleted_at' => now()]);
            if (! $updated) {
                return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan.'], 404);
            }

            return response()->json(['status' => 'success', 'message' => 'Supplier berhasil dihapus.']);
        } catch (\Exception $e) {
            Log::error('Error Delete Supplier: '.$e->getMessage());

            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan sistem.'], 500);
        }
    }
}
