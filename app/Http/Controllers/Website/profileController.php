<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;

class profileController extends Controller
{
    public function changePassword(Request $request)
    {
        User::where('id', Auth::user()->id)->update([
            'password' => Hash::make($request->np),
        ]);

        return response()->json(['message' => 'success', 'data' => true], 200);
    }

    public function getSignature()
    {
        try {
            $employee = $this->currentEmployee();

            return response()->json(['message' => 'success', 'data' => [
                'signature_path' => $employee->signature_path,
            ]], 200);
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], 400);
        }
    }

    /**
     * Tanda tangan elektronik sederhana (canvas, bukan tersertifikasi BSrE/Privy) —
     * disimpan sekali per dokter/karyawan, dipakai ulang di semua surat yang dia
     * terbitkan (lihat PrintController::printSuratSakitPasien dkk).
     */
    public function saveSignature(Request $request)
    {
        $request->validate([
            'signature' => 'required|string',
        ]);

        try {
            $employee = $this->currentEmployee();

            if (! str_starts_with($request->signature, 'data:image/png;base64,')) {
                return response()->json(['message' => 'error', 'error' => 'Format tanda tangan tidak valid'], 400);
            }

            $base64 = substr($request->signature, strlen('data:image/png;base64,'));
            $binary = base64_decode($base64, true);
            if ($binary === false) {
                return response()->json(['message' => 'error', 'error' => 'Format tanda tangan tidak valid'], 400);
            }

            if ($employee->signature_path && file_exists(public_path($employee->signature_path))) {
                unlink(public_path($employee->signature_path));
            }

            $uploadDir = 'images/signatures';
            if (! is_dir(public_path($uploadDir))) {
                mkdir(public_path($uploadDir), 0755, true);
            }

            $filePath = $uploadDir.'/employee-'.$employee->id.'-'.uniqid().'.png';
            file_put_contents(public_path($filePath), $binary);

            $employee->update(['signature_path' => $filePath]);

            return response()->json(['message' => 'success', 'data' => ['signature_path' => $filePath]], 200);
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], 400);
        }
    }

    public function deleteSignature()
    {
        try {
            $employee = $this->currentEmployee();

            if ($employee->signature_path && file_exists(public_path($employee->signature_path))) {
                unlink(public_path($employee->signature_path));
            }

            $employee->update(['signature_path' => null]);

            return response()->json(['message' => 'success'], 200);
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], 400);
        }
    }

    private function currentEmployee(): Employee
    {
        $employee = Employee::find(Auth::user()->fk_employee);

        if (! $employee) {
            throw new InvalidArgumentException('Akun ini tidak terhubung ke data karyawan');
        }

        return $employee;
    }
}
