<?php

namespace App\Http\Controllers\Website;

use App\Helpers\ProductQtyHelper;
use App\Http\Controllers\Controller;
use App\Models\DaftarPasien;
use App\Models\Module;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        // "/" is reachable without a session (it's not behind the auth
        // middleware group, see routes/web.php) so a guest lands on the
        // landing page instead of Laravel's default guest redirect to
        // /login — only a logged-in user actually gets the dashboard below.
        if (! Auth::check()) {
            return redirect()->route('landingPage');
        }

        $klienfk = Auth::user()->klienfk;

        $patientsToday = DaftarPasien::where('klienfk', $klienfk)
            ->whereDate('tglregistrasi', now()->toDateString())
            ->count();

        $revenueToday = $this->signedRevenueQuery($klienfk)
            ->whereDate('created_at', now()->toDateString())
            ->value('total');

        $revenueChart = $this->last7DaysRevenue($klienfk);

        $productIds = Product::where('klienfk', $klienfk)->pluck('id');
        $balances = app(ProductQtyHelper::class)->getBalancesByProducts($productIds->toArray());
        $outOfStockCount = count(array_filter($balances, fn ($qty) => $qty <= 0));

        return view('menu', [
            'patientsToday' => $patientsToday,
            'revenueToday' => (float) $revenueToday,
            'outOfStockCount' => $outOfStockCount,
            'revenueChartLabels' => $revenueChart->pluck('label'),
            'revenueChartValues' => $revenueChart->pluck('total'),
        ]);
    }

    public function landingPage()
    {
        return view('landing');
    }

    private function signedRevenueQuery(int $klienfk)
    {
        return DB::table('t_transactions')
            ->where('klienfk', $klienfk)
            ->whereNull('deleted_at')
            ->selectRaw("COALESCE(SUM(CASE
                WHEN transaction_type = 'in' THEN amount
                WHEN transaction_type = 'out' THEN -amount
                ELSE 0 END), 0) as total");
    }

    private function last7DaysRevenue(int $klienfk)
    {
        $byDate = DB::table('t_transactions')
            ->where('klienfk', $klienfk)
            ->whereNull('deleted_at')
            ->whereBetween('created_at', [now()->subDays(6)->startOfDay(), now()->endOfDay()])
            ->selectRaw("DATE(created_at) as tgl, COALESCE(SUM(CASE
                WHEN transaction_type = 'in' THEN amount
                WHEN transaction_type = 'out' THEN -amount
                ELSE 0 END), 0) as total")
            ->groupBy('tgl')
            ->pluck('total', 'tgl');

        return collect(range(6, 0))->map(function ($daysAgo) use ($byDate) {
            $date = now()->subDays($daysAgo);
            $key = $date->toDateString();

            return [
                'label' => $date->translatedFormat('d M'),
                'total' => (float) ($byDate[$key] ?? 0),
            ];
        });
    }

    public function menu(Request $request)
    {
        $AccessList = explode(',', Auth::user()->access);
        $menu = $request->menu;
        $menuList = Module::when($menu, function ($query) use ($menu) {
            return $query->where('name', 'like', "%$menu%");
        })->where('isactive', 1)->whereIn('id', $AccessList)->whereNotIn('route', ['', '#'])->orderBy('group_id')->orderBy('list_no')->get();

        // dd($menuList);
        return view('layout/menuList', compact('menuList'));
    }
}
