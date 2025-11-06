<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        return view('menu');
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
