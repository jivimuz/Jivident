<?php

namespace App\Http\Controllers\Website;

use App\Helpers\PayrollHelper;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\PayrollSlip;
use Illuminate\Http\Request;
use Throwable;

class PayrollController extends Controller
{
    public function calculate(Request $request, $idn)
    {
        $request->validate(['periode' => 'required|date_format:Y-m']);

        try {
            $employee = Employee::findOrFail(base64_decode($idn));
            $gajiPokok = $request->gajipokok ? (float) $request->gajipokok : (float) $employee->gajipokok;

            $helper = app(PayrollHelper::class);
            $bpjs = $helper->calculateBpjs($gajiPokok);
            $pph = $helper->calculatePph21($employee, $gajiPokok);

            $gajiBersih = $gajiPokok
                - $bpjs['bpjs_kesehatan_employee']
                - $bpjs['bpjs_jht_employee']
                - $bpjs['bpjs_jp_employee']
                - $pph['pph21_nominal'];

            return response()->json([
                'message' => 'success',
                'data' => array_merge($bpjs, $pph, [
                    'gaji_pokok' => $gajiPokok,
                    'gaji_bersih' => $gajiBersih,
                    'already_exists' => PayrollSlip::where('fk_employee', $employee->id)
                        ->where('periode', $request->periode)->exists(),
                ]),
            ], 200);
        } catch (Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function generateSlip(Request $request, $idn)
    {
        $request->validate(['periode' => 'required|date_format:Y-m']);

        try {
            $employee = Employee::findOrFail(base64_decode($idn));
            $gajiPokok = $request->gajipokok ? (float) $request->gajipokok : null;

            $slip = app(PayrollHelper::class)->generateSlip($employee, $request->periode, $gajiPokok);

            return response()->json(['message' => 'success', 'data' => $slip], 200);
        } catch (Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getSlips($idn)
    {
        $id = base64_decode($idn);
        $slips = PayrollSlip::where('fk_employee', $id)->orderBy('periode', 'desc')->get();

        return response()->json(['message' => 'success', 'data' => $slips], 200);
    }
}
