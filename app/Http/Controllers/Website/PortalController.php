<?php

namespace App\Http\Controllers\Website;

use App\Helpers\AppointmentHelper;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\DaftarPasien;
use App\Models\Patient;
use App\Models\ResepPasien;
use App\Models\RuangPraktek;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Self-service patient portal. No m_users account involved — identity is
 * verified with No. RM + tanggal lahir (no password, no third-party OTP
 * provider needed) and tracked via session, guarded by PortalAuth.
 */
class PortalController extends Controller
{
    public function showLogin()
    {
        return view('public/portal/login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'norm' => 'required|string',
            'tanggal_lahir' => 'required|date',
        ]);

        $patient = Patient::where('norm', $request->norm)
            ->whereDate('tgllahir', $request->tanggal_lahir)
            ->first();

        if (! $patient) {
            return back()->withErrors(['norm' => 'No. RM atau tanggal lahir tidak sesuai dengan data kami.'])->withInput();
        }

        $request->session()->put('portal_patient_id', $patient->id);
        $request->session()->regenerate();

        return redirect()->route('portal.dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('portal_patient_id');

        return redirect()->route('portal.login');
    }

    public function dashboard(Request $request)
    {
        return view('public/portal/dashboard', ['patient' => $request->attributes->get('portalPatient')]);
    }

    public function riwayat(Request $request)
    {
        $patient = $request->attributes->get('portalPatient');

        $visits = DaftarPasien::select([
            't_daftarpasien.id',
            't_daftarpasien.noregistrasi',
            't_daftarpasien.tglregistrasi',
            't_daftarpasien.keluhan',
            'm_employee.fullname as dokter_name',
        ])
            ->leftJoin('m_employee', 'm_employee.id', 't_daftarpasien.dokterfk')
            ->where('t_daftarpasien.pasienfk', $patient->id)
            ->orderByDesc('t_daftarpasien.tglregistrasi')
            ->get();

        foreach ($visits as $visit) {
            $visit->tindakan = DB::table('t_tindakanpasien')
                ->where('fk_daftarpasien', $visit->id)
                ->whereNull('deleted_at')
                ->pluck('tindakan');

            $visit->diagnosa = DB::table('t_diagnosapasien')
                ->where('fk_daftarpasien', $visit->id)
                ->whereNull('deleted_at')
                ->pluck('diagnosa');
        }

        return view('public/portal/riwayat', ['patient' => $patient, 'visits' => $visits]);
    }

    public function resep(Request $request)
    {
        $patient = $request->attributes->get('portalPatient');

        $resep = ResepPasien::select([
            't_reseppasien.*',
            't_daftarpasien.tglregistrasi',
        ])
            ->join('t_daftarpasien', 't_daftarpasien.id', 't_reseppasien.daftarpasien_id')
            ->where('t_reseppasien.pasien_id', $patient->id)
            ->orderByDesc('t_daftarpasien.tglregistrasi')
            ->get()
            ->groupBy(fn ($r) => $r->daftarpasien_id);

        return view('public/portal/resep', ['patient' => $patient, 'reseps' => $resep]);
    }

    public function tagihan(Request $request)
    {
        $patient = $request->attributes->get('portalPatient');

        $tindakanSub = DB::table('t_tindakanpasien')
            ->whereNull('deleted_at')
            ->selectRaw('fk_daftarpasien, SUM(amount) as total_tindakan')
            ->groupBy('fk_daftarpasien');

        $diagnosaSub = DB::table('t_diagnosapasien')
            ->whereNull('deleted_at')
            ->selectRaw('fk_daftarpasien, SUM(amount) as total_diagnosa')
            ->groupBy('fk_daftarpasien');

        $paidSub = DB::table('t_transactions')
            ->whereNull('deleted_at')
            ->whereNotNull('daftarpasien_id')
            ->selectRaw("daftarpasien_id, SUM(CASE WHEN transaction_type = 'in' THEN amount WHEN transaction_type = 'out' THEN -amount ELSE 0 END) as total_paid")
            ->groupBy('daftarpasien_id');

        $tagihan = DB::table('t_daftarpasien as dp')
            ->leftJoinSub($tindakanSub, 'ti', 'ti.fk_daftarpasien', '=', 'dp.id')
            ->leftJoinSub($diagnosaSub, 'di', 'di.fk_daftarpasien', '=', 'dp.id')
            ->leftJoinSub($paidSub, 'pay', 'pay.daftarpasien_id', '=', 'dp.id')
            ->where('dp.pasienfk', $patient->id)
            ->selectRaw('dp.id, dp.noregistrasi, dp.tglregistrasi, dp.statuspembayaran,
                (dp.biaya_registrasi + COALESCE(ti.total_tindakan, 0) + COALESCE(di.total_diagnosa, 0)) as total_tagihan,
                COALESCE(pay.total_paid, 0) as total_paid')
            ->orderByDesc('dp.tglregistrasi')
            ->get();

        $tagihan->each(function ($row) {
            $row->sisa = (float) $row->total_tagihan - (float) $row->total_paid;
        });

        return view('public/portal/tagihan', ['patient' => $patient, 'tagihan' => $tagihan]);
    }

    public function booking(Request $request)
    {
        $patient = $request->attributes->get('portalPatient');

        $appointments = Appointment::with(['dokter.employee:id,fullname', 'ruangPraktek:id,name'])
            ->where('fk_pasien', $patient->id)
            ->orderByDesc('tanggal')
            ->orderByDesc('jam_mulai')
            ->get();

        return view('public/portal/booking', ['patient' => $patient, 'appointments' => $appointments]);
    }

    public function bookingDokterList(Request $request)
    {
        $q = $request->params;

        $data = User::select(['m_users.id', 'm_employee.fullname', 'm_employee.nik'])
            ->join('m_employee', 'm_employee.id', '=', 'm_users.fk_employee')
            ->where('m_employee.is_pemeriksapasien', 1)
            ->when($q, function ($query) use ($q) {
                $query->where('m_employee.fullname', 'like', "%{$q}%");
            })
            ->limit(20)
            ->get();

        return response()->json(['message' => 'success', 'data' => $data], 200);
    }

    public function bookingRuangPraktekByDokter($dokterId)
    {
        $data = RuangPraktek::where('fk_dokter', $dokterId)->get(['id', 'name']);

        return response()->json(['message' => 'success', 'data' => $data], 200);
    }

    public function bookingAvailableSlots(Request $request)
    {
        $request->validate([
            'fk_dokter' => 'required|integer',
            'fk_ruangpraktek' => 'required|integer',
            'tanggal' => 'required|date',
        ]);

        $slots = app(AppointmentHelper::class)->getAvailableSlots(
            (int) $request->fk_dokter,
            (int) $request->fk_ruangpraktek,
            $request->tanggal
        );

        return response()->json(['message' => 'success', 'data' => $slots], 200);
    }

    public function bookingStore(Request $request)
    {
        $patient = $request->attributes->get('portalPatient');

        try {
            $appointment = app(AppointmentHelper::class)->book([
                'klienfk' => $patient->klienfk,
                'fk_dokter' => $request->fk_dokter,
                'fk_ruangpraktek' => $request->fk_ruangpraktek,
                // Dipaksa dari pasien yang sedang login, BUKAN dari input client —
                // supaya pasien tidak bisa booking atas nama pasien lain.
                'fk_pasien' => $patient->id,
                'tanggal' => $request->tanggal,
                'jam_mulai' => $request->jam_mulai,
                'notes' => $request->notes,
            ]);

            return response()->json(['message' => 'success', 'data' => $appointment], 200);
        } catch (Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function bookingCancel(Request $request, $id)
    {
        $patient = $request->attributes->get('portalPatient');
        $appointment = Appointment::find($id);

        if (! $appointment || $appointment->fk_pasien != $patient->id) {
            return response()->json(['error' => 'Booking tidak ditemukan'], 404);
        }

        try {
            app(AppointmentHelper::class)->cancel((int) $id);

            return response()->json(['message' => 'success'], 200);
        } catch (Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
