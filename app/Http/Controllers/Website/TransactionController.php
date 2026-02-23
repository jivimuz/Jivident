<?php

namespace App\Http\Controllers\Website;

use App\Helpers\TransactionHelper;
use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Setting;
use App\Models\Transactions;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    public function index()
    {
        return view('pages/transaction/index');
    }

    public function getList(Request $request)
    {
        $query = Transactions::query();
        // $query->where('is_system', 1);
        // Filter berdasarkan bulan-tahun
        if ($request->monthYear) {
            $query->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$request->monthYear]);
        }

        // Searching
        if ($request->search['value'] ?? false) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('transaction_code', 'like', "%{$search}%")
                    ->orWhere('amount', 'like', "%{$search}%");
            });
        }

        $recordsTotal = $query->count();

        // Paging (start & length bawaan DataTables)
        $start  = $request->start ?? 0;
        $length = $request->length ?? 10;

        $data = $query->orderBy('id', 'desc')
            ->skip($start)
            ->take($length)
            ->get();


        $monthly = DB::table('t_transactions')
            ->selectRaw("
           coalesce(  SUM(
                CASE
                    WHEN transaction_type = 'in' THEN amount
                    WHEN transaction_type = 'out' THEN -amount
                    ELSE 0
                END
            ), 0) as total_saldo
        ")->whereNull('deleted_at')
            // ->where('is_system', 1)
            ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$request->monthYear])
            ->first();

        return response()->json([
            "draw"            => intval($request->draw),
            "recordsTotal"    => $recordsTotal,
            "recordsFiltered" => $recordsTotal,
            "data"            => $data,
            "monthly"         => $monthly
        ]);
    }

    public function getOmset()
    {
        $data = DB::table('t_transactions')
            ->selectRaw("
             coalesce(  SUM(
                CASE
                    WHEN transaction_type = 'in' THEN amount
                    WHEN transaction_type = 'out' THEN -amount
                    ELSE 0
                END
            ), 0) as total_saldo
        ")->whereNull('deleted_at')
            // ->where('is_system', 1)
            ->first();

        return response()->json(["message" => 'success', 'data' => $data], 200);
    }

    public function addTransaction()
    {
        return view('pages/transaction/addTransaction');
    }

    public function submitTransaction(Request $request)
    {
        $validated = $request->validate([
            'nominal'           => 'required|numeric|min:1|max:1000000000',
            'transaction_type'  => 'required|in:in,out',
            'notes'             => 'required|string|max:100',

            // ✅ max 1MB = 1024 KB
            'attachment'        => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:1024',
        ], [
            'attachment.mimes' => 'Attachment harus JPG/JPEG/PNG/WEBP/PDF.',
            'attachment.max'   => 'Attachment maksimal 1MB.',
        ]);

        DB::beginTransaction();
        try {
            $transaction = new TransactionHelper();

            // ✅ upload file (optional)
            $attachmentPath = null;
            $attachmentType = null; // image/pdf

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $ext  = strtolower($file->getClientOriginalExtension());

                $attachmentType = ($ext === 'pdf') ? 'pdf' : 'image';

                $filename = 'attachment_' . Auth::id() . '_' . now()->format('YmdHis') . '_' . Str::random(10) . '.' . $ext;
                $attachmentPath = $file->storeAs('transactions/attachments', $filename, 'public');
                // storage/app/public/transactions/attachments/...
            }

            $transactionData = [
                'transaction_type' => $validated['transaction_type'],
                'typecode'         => 'KSR',
                'amount'           => $validated['nominal'],
                'notes'            => $validated['notes'],
                'userhandler_id'   => Auth::id(),
                'payment_method'   => "SYSTEM",

                // ✅ sesuaikan nama kolom tabelmu
                'attachment_path'  => $attachmentPath,
                'attachment_type'  => $attachmentType,
            ];

            $transaction->createTransaction($transactionData);

            Log::channel('user_actions')->info("User " . Auth::id() . " melakukan transaksi pembayaran {$validated['notes']}");

            DB::commit();
            return response()->json([
                'message' => 'Transaksi Berhasil.',
                'attachment_path' => $attachmentPath,
                'attachment_type' => $attachmentType,
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('submitTransaction error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'error' => 'Terjadi kesalahan saat memproses transaksi.',
                'details' => $e->getMessage()
            ], 400);
        }
    }

    public function storeCompressedImageAsWebp($file, $userId)
    {
        $filename = 'transaction_' . $userId . '_' . time() . '.webp';
        $path = 'attachments/transactions';
        $file->storeAs($path, $filename, 'public');
        return $path . '/' . $filename;
    }

    public function storeCompressedPdf($file, $userId)
    {
        $filename = 'transaction_' . $userId . '_' . time() . '.pdf';
        $path = 'attachments/transactions';
        $file->storeAs($path, $filename, 'public');
        return $path . '/' . $filename;
    }


    public function printInvoice($no_transaction, $mode)
    {
        $mode = strtolower($mode);
        if (!in_array($mode, ['thermal', 'a4'])) abort(404);

        $trx = Transactions::select('no_transaction', 'created_at', 'transaction_type', 'amount', 'notes', 'pasien_id', 'userhandler_id')
            ->where('no_transaction', $no_transaction)
            ->first();

        if (!$trx) abort(404);
        $pasien = null;
        if (!empty($trx->pasien_id)) {
            $pj = Patient::select('nama')->where('id', $trx->pasien_id)->first();
            $pasien = $pj ? $pj->nama : null;
        }

        // user manual
        $user_name = null;
        if (!empty($trx->userhandler_id)) {
            $usr = User::select('display_name as name')->where('id', $trx->userhandler_id)->first();
            $user_name = $usr ? $usr->name : null;
        }
        $company_name = Setting::where('parameter', 'company_name')->first();
        $company_phone = Setting::where('parameter', 'company_phone')->first();
        $company_address = Setting::where('parameter', 'company_address')->first();


        $data = [
            'no_transaction'   => $trx->no_transaction,
            'created_at'       => $trx->created_at,
            'transaction_type' => $trx->transaction_type,
            'amount'           => $trx->amount,
            'notes'            => $trx->notes,
            'user_name'        => $user_name,
            'pasien'        => $pasien,

            'company_name'     => $company_name && $company_name->value ? $company_name->value : '-',
            'company_phone'     => $company_phone &&  $company_phone->value ? $company_phone->value : '-',
            'company_address'     => $company_address && $company_address->value ? $company_address->value : '-',

        ];

        if ($mode === 'thermal') {
            return view('print/transaction/print-thermal', $data);
        }
        return view('print/transaction/print-a4', $data);
    }
}
