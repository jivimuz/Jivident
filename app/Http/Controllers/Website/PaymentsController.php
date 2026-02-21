<?php

namespace App\Http\Controllers\Website;

use App\Helper\DebtHelper;
use App\Helper\NotificationHelper;
use App\Helper\TransactionHelper;
use App\Http\Controllers\Controller;
use App\Mail\SendMail;
use App\Models\Agents;
use App\Models\FinanceSetting;
use App\Models\PaidRegistration;
use App\Models\Payments;
use App\Models\Setting;
use App\Models\VoucherKey;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PaymentsController extends Controller
{
    public function index()
    {
        return view('pages/payments/index');
    }
    public function getList()
    {
        $data = Payments::get();
        return response()->json(['data' => $data], 200);
    }


    public function getPaymentsById(Request $request)
    {
        $data = Payments::where('id', $request->id)->first();
        return response()->json(['data' => $data], 200);
    }
    public function deletePayment(Request $request)
    {
        $cek = Payments::where('id', base64_decode($request->id))->first();
        if ($cek) {
            $id = Auth::user()->id;
            Log::channel('user_actions')->info("User {$id} meng-hapus pembayaran {$cek->title}");
            Payments::where('id', base64_decode($request->id))->delete();

            return response()->json(['message' => 'Delete Success'], 200);
        }
        return response()->json(['error' => 'This Data Not Found'], 400);
    }

    public function insertOrUpdatePayments(Request $request)
    {
        if ($request->payment_id == 0) {

            try {
                $data = Payments::insert([
                    'image_url' => $request->image_url,
                    'title' => $request->title,
                    'is_qr' => $request->is_qr ?? false,
                    'accountname' => $request->accountname ?? null,
                    'accountnumber' => $request->accountnumber ?? null,
                ]);
                return response()->json(['data' => $data, 'message' => 'Data inserted'], 200);
            } catch (\Throwable $e) {
                return response()->json(["error" => $e->getMessage()], 400);
            }
        } else {

            try {
                $data = Payments::where('id', $request->payment_id)->update([
                    'image_url' => $request->image_url,
                    'title' => $request->title,
                    'is_qr' => $request->is_qr ?? false,
                    'accountname' => $request->accountname ?? null,
                    'accountnumber' => $request->accountnumber ?? null,
                ]);
                return response()->json(['data' => $data, 'message' => 'Data Updated'], 200);
            } catch (\Throwable $e) {
                return response()->json(["error" => $e->getMessage()], 400);
            }
        }
    }
}
