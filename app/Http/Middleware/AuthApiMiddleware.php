<?php

namespace App\Http\Middleware;

use App\Models\Serial;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AuthApiMiddleware
{
    public function handle($request, Closure $next)
    {
        if ($request->is('license') || $request->is('license/*') || $request->routeIs('license.*') || $request->is('serialActivation')) {
            return $next($request);
        }

        $type = app()->environment('testing')
            ? (int) env('TYPE_LOCAL_SERVER', config('license.type_local_server', 0))
            : (int) config('license.type_local_server', 0);
        if ($type === 2) {
            return $next($request);
        }

        $serial = Serial::first();
        if (! $serial) {
            return redirect()->route('license', ['expired' => 1]);
        }
        if (empty($serial->serial_code)) {
            return redirect()->route('license');
        }

        $twoWeeksLater = now()->addWeeks(2);
        if ($type === 0) {
            $lastCheck = $serial->last_check ? Carbon::parse($serial->last_check) : null;
            $shouldCheckRemote = ! $lastCheck || $lastCheck->lte(now()->subHours(3));
            if ($shouldCheckRemote) {
                try {
                    $response = Http::timeout(8)->get(config('license.auth_server'), [
                        'license_key' => $serial->serial_code,
                        'product_code' => 'ANX20260222AL01012',
                    ]);
                    $serial->last_check = now();
                    if ($response->successful()) {
                        $data = $response->json();
                        if (! empty($data['expires'])) {
                            $serial->valid_until = Carbon::parse($data['expires'])->toDateString();
                        }
                    } else {
                        Log::warning('License validate HTTP failed', [
                            'status' => $response->status(),
                            'body' => $response->body(),
                        ]);
                    }
                    $serial->save();
                } catch (\Throwable $e) {
                    $serial->last_check = now();
                    $serial->save();
                    Log::warning('License validation exception: '.$e->getMessage());
                }
            }
        }

        if (empty($serial->valid_until)) {
            return redirect()->route('license', ['expired' => 1]);
        }
        $expiryDate = Carbon::parse($serial->valid_until);
        if ($expiryDate->lessThanOrEqualTo(Carbon::yesterday())) {
            return redirect()->route('license', ['expired' => 1]);
        }
        if ($expiryDate->lessThanOrEqualTo($twoWeeksLater)) {
            session()->flash('expiredAlert', true);
            session()->flash('expiredDate', $expiryDate->format('Y-m-d'));
        }

        return $next($request);
    }
}
