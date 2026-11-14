<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Serial;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return redirect('/');
        }

        return view('auth/login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ]);

        $DataUser = User::where('username', $validated['username'])->orWhere('email', $validated['username'])->first();
        if (! $DataUser) {
            return response()->json(['error' => 'Unknown Username / Email'], 401);
        }
        $credentials = [
            'email' => $DataUser->email,
            'password' => $validated['password'],
        ];
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return response()->json(['message' => 'Login Success'], 200);
        }

        return response()->json(['error' => 'Login Failed'], 401);
    }

    /**
     * Log the user out of the application.
     *
     * @return Response
     */
    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    }

    public function error()
    {
        return view('layout/error');
    }

    public function license()
    {
        // If type_local_server is 2 (bypass license), redirect away from license page to home
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

        if ($type === 2) {
            return redirect('/');
        }

        return view('layout/license');
    }

    public function validateLicense(Request $request)
    {
        // 1. Validasi input dari frontend
        $request->validate([
            'license_key' => 'required|string',
        ]);

        $key = $request->license_key;

        try {
            // 2. Hit API Eksternal
            $endpoint = env('AUTH_SERVER', 'https://management.antariks.id/api/license/validate');

            $response = Http::timeout(15)->post($endpoint, [
                'license_key' => $key,
                'product_code' => 'ANX20260222AL01012',
            ]);

            $data = $response->json();

            // 3. Cek apakah response API sukses dan lisensi valid
            if ($response->successful() && isset($data['valid']) && $data['valid'] === true) {

                // Jika valid, update tabel serial.
                // Karena hanya ada 1 record di tabel, update langsung tanpa klausa "where"
                // akan menggantikan record yang lama secara otomatis.
                DB::table('serial')->update([
                    'serial_code' => $key,
                    'valid_until' => $data['expires'],
                ]);
                Cache::flush();

                // Kirim response sukses ke frontend
                return response()->json([
                    'status' => 'success',
                    'message' => 'Lisensi atas nama '.($data['owner'] ?? 'Anda').' berhasil diaktifkan. Berlaku hingga: '.date('d F Y', strtotime($data['expires'])),
                ], 200);
            } else {
                // Jika return API false atau invalid
                return response()->json([
                    'status' => 'error',
                    'message' => 'Lisensi tidak valid, salah, atau sudah kedaluwarsa.',
                ], 400);
            }
        } catch (\Exception $e) {
            // Tangkap error jika server pusat down atau tidak ada internet
            Log::error('License Validation Error: '.$e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal terhubung ke server, '.$e->getMessage(),
            ], 500);
        }
    }

    public function authLicense()
    {
        $serial = Serial::first();

        return view('layout.auth-license', compact('serial'));
    }
}
