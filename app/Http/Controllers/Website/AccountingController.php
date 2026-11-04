<?php

namespace App\Http\Controllers\Website;

use App\Helpers\AccountingHelper;
use App\Http\Controllers\Controller;
use App\Models\AccountingAccount;
use App\Models\AccountingJournal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AccountingController extends Controller
{
    public function index()
    {
        return view('pages/accounting/index');
    }

    public function accounts()
    {
        return response()->json([
            'data' => AccountingAccount::where('klienfk', Auth::user()->klienfk)->orderBy('code')->get(),
        ]);
    }

    public function storeAccount(Request $request)
    {
        $klienfk = Auth::user()->klienfk;
        $data = $request->validate([
            'code' => ['required', 'string', 'max:20', Rule::unique('m_accounting_accounts')->where('klienfk', $klienfk)],
            'name' => 'required|string|max:150',
            'type' => 'required|in:asset,liability,equity,revenue,expense',
        ]);
        $account = AccountingAccount::create($data + ['klienfk' => $klienfk]);

        return response()->json(['message' => 'Akun berhasil dibuat.', 'data' => $account]);
    }

    public function journals(Request $request)
    {
        $query = AccountingJournal::with('lines.account')
            ->where('klienfk', Auth::user()->klienfk)
            ->latest('journal_date')
            ->latest('id');

        if ($request->filled('month')) {
            $query->whereBetween('journal_date', [
                $request->month.'-01',
                date('Y-m-t', strtotime($request->month.'-01')),
            ]);
        }

        return response()->json(['data' => $query->paginate(20)]);
    }

    public function storeJournal(Request $request)
    {
        $data = $request->validate([
            'journal_no' => 'nullable|string|max:40',
            'journal_date' => 'required|date',
            'description' => 'required|string|max:255',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|integer',
            'lines.*.debit' => 'nullable|numeric|min:0',
            'lines.*.credit' => 'nullable|numeric|min:0',
            'lines.*.description' => 'nullable|string|max:255',
        ]);

        $journal = AccountingHelper::createJournal($data, Auth::user()->klienfk, Auth::id());

        return response()->json(['message' => 'Jurnal berhasil disimpan.', 'data' => $journal]);
    }
}
