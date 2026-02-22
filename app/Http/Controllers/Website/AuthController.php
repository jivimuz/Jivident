<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Serial;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
        $DataUser = User::where('username', $request->username)->orWhere('email', $request->username)->first();
        if (!$DataUser) {
            return response()->json(['error' => 'Unknown Username / Email'], 401);
        }
        $credentials = [
            'email' => $DataUser->email,
            'password' => $request->password
        ];
        if (Auth::attempt($credentials)) {
            return response()->json(['message' => 'Login Success'], 200);
        }

        return response()->json(['error' => 'Login Failed'], 401);
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }

    public function error()
    {
        return view('layout/error');
    }
    public function license()
    {
        return view('layout/license');
    }
    public function validateLicense(Request $request)
    {
        // 1. Validasi input dari frontend
        $request->validate([
            'license_key' => 'required|string'
        ]);

        $key = $request->license_key;

        try {
            // 2. Hit API Eksternal
            $endpoint = env('AUTH_SERVER', 'https://management.antariks.id/api/license/validate');

            $response = Http::timeout(15)->post($endpoint, [
                'license_key'  => $key,
                'product_code' => "ANX20260222AL01012",
            ]);

            $data = $response->json();


            // 3. Cek apakah response API sukses dan lisensi valid
            if ($response->successful() && isset($data['valid']) && $data['valid'] === true) {

                // Jika valid, update tabel serial.
                // Karena hanya ada 1 record di tabel, update langsung tanpa klausa "where"
                // akan menggantikan record yang lama secara otomatis.
                DB::table('serial')->update([
                    'serial_code' => $key,
                    'valid_until' => $data['expires']
                ]);
                Cache::flush();

                // Kirim response sukses ke frontend
                return response()->json([
                    'status' => 'success',
                    'message' => 'Lisensi atas nama ' . ($data['owner'] ?? 'Anda') . ' berhasil diaktifkan. Berlaku hingga: ' . date('d F Y', strtotime($data['expires'])),
                ], 200);
            } else {
                // Jika return API false atau invalid
                return response()->json([
                    'status' => 'error',
                    'message' => 'Lisensi tidak valid, salah, atau sudah kedaluwarsa.'
                ], 400);
            }
        } catch (\Exception $e) {
            // Tangkap error jika server pusat down atau tidak ada internet
            Log::error('License Validation Error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal terhubung ke server, ' . $e->getMessage()
            ], 500);
        }
    }

    function authLicense()
    {
        $serial = Serial::first();
        return view('layout.auth-license', compact('serial'));
    }
}
