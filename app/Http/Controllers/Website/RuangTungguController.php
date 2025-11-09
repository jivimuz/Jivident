<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RuangTungguController extends Controller
{
    public function index()
    {
        return view('pages/ruang_tunggu/index');
    }
}
