<?php
// app/Http/Middleware/CheckModuleAccess.php

namespace App\Http\Middleware;

use Closure;
use App\Models\Module;
use Illuminate\Support\Facades\Auth;

class CheckModuleAccess
{
    public function handle($request, Closure $next, ...$modules)
    {
        $AccessList = explode(',', Auth::user()->access);
        $menuList = Module::whereIn('code', $modules)->whereIn('id', $AccessList)->first();
        if (!$menuList) {
            return redirect()->route('error');
        }

        return $next($request);
    }
}
