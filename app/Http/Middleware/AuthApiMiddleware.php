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
        // Read type_local_server: priority 1: env, priority 2: native_config.json, priority 3: m_setting
        $type = (int) env('TYPE_LOCAL_SERVER', 0);
        if ($type === 0) {
            try {
                $configPath = storage_path('app/native_config.json');
                if (file_exists($configPath)) {
                    $nativeConfig = json_decode(file_get_contents($configPath), true);
                    if (isset($nativeConfig['type_local_server'])) {
                        $type = (int) $nativeConfig['type_local_server'];
                    }
                }
            } catch (\Throwable $e) {}
        }

        if ($type === 0) {
            try {
                $setting = \App\Models\Setting::where('parameter', 'type_local_server')->first();
                if ($setting) {
                    $type = (int) $setting->value;
                }
            } catch (\Throwable $e) {}
        }
        
        // If type is 2 (bypass license), allow everything immediately without checking serial
        if ($type === 2) {
            return $next($request);
        }

        // Always allow native-config setup wizard and NativePHP internal paths
        if ($request->is('native-config') || $request->is('native-config/*') || $request->is('_native/*')) {
            return $next($request);
        }

        // Check if database tables are set up yet. If not, MUST go to setup wizard first!
        try {
            $serial = Serial::first();
        } catch (\Throwable $e) {
            return redirect()->route('native-config.index');
        }

        // If type is 0 or 1, user must set up / validate serial first
        if ($request->is('license') || $request->is('license/*') || $request->routeIs('license.*') || $request->is('serialActivation')) {
            return $next($request);
        }

        if (! $serial) {
            return redirect()->route('license', ['expired' => 1]);
        }
        if (empty($serial->serial_code)) {
            return redirect()->route('license');
        }

        $twoWeeksLater = now()->addWeeks(2);
        if ($type === 0) {
            // In mode 0 (offline/local mode), we don't force a remote server check if the server is offline or dead,
            // we trust the stored `valid_until` date unless it's completely empty.
            if (empty($serial->valid_until)) {
                $serial->valid_until = now()->addYears(1)->toDateString();
                $serial->save();
            }
        } else {
            // Mode 1 (online mode) checks remote server with fallback handling
            $lastCheck = $serial->last_check ? Carbon::parse($serial->last_check) : null;
            $shouldCheckRemote = ! $lastCheck || $lastCheck->lte(now()->subHours(3));
            if ($shouldCheckRemote) {
                try {
                    $response = Http::timeout(5)->get(config('license.auth_server'), [
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
                        Log::warning('License validate HTTP failed, keeping offline grace period', [
                            'status' => $response->status(),
                        ]);
                    }
                    $serial->save();
                } catch (\Throwable $e) {
                    $serial->last_check = now();
                    $serial->save();
                    Log::warning('License validation exception (offline grace): '.$e->getMessage());
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
