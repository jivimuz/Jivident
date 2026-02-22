public function handle($request, Closure $next)
    {
        // Skip halaman license
        if (
            $request->is('license') ||
            $request->is('license/*') ||
            $request->routeIs('license.*') ||
            $request->is('serialActivation')
        ) {
            return $next($request);
}

        $type = (int) env('TYPE_LOCAL_SERVER', 0);

        // TYPE 2 = bypass
        if ($type === 2) {
            return $next($request);
        }

        $serial = Serial::first();

        if (!$serial) {
            return redirect()->route('license', ['expired' => 1]); // query param fallback (aman untuk global middleware)
        }

        if (empty($serial->serial_code)) {
            return redirect()->route('license');
        }

        $twoWeeksLater = now()->addWeeks(2);

        // ===== ONLINE VALIDATION hanya tiap 1 jam =====
        if ($type === 0) {
            $lastCheck = $serial->last_check ? Carbon::parse($serial->last_check) : null;
            $shouldCheckRemote = !$lastCheck || $lastCheck->lte(now()->subHours(3));
            $shouldCheckRemote = true;

            if ($shouldCheckRemote) {
                try {
                    $endpoint = env('AUTH_SERVER', 'https://management.antariks.id/api/license/validate');

                    $response = Http::timeout(8)->get($endpoint, [
                        'license_key'  => $serial->serial_code,
                        'product_code' => 'ANX20260222AL01012',
                    ]);

                    // update last_check selalu (biar gak hit API tiap request saat server down)
                    $serial->last_check = now();

                    if ($response->successful()) {
                        $data = $response->json();
                        dd($data);
                        if (!empty($data['expires'])) {
                            $serial->valid_until = Carbon::parse($data['expires'])->toDateString();
                        }
                    } else {
                        Log::warning('License validate HTTP failed', [
                            'status' => $response->status(),
                            'body'   => $response->body(),
                        ]);
                    }

                    $serial->save();
                } catch (\Throwable $e) {
                    $serial->last_check = now();
                    $serial->save();

                    Log::warning('License validation exception: ' . $e->getMessage());
                    // lanjut pakai valid_until lokal
                }
            }
        }

        // ===== Local expiry check (setiap request) =====
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
