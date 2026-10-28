<?php

namespace App\Http\Middleware;

use App\Models\Patient;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guards the patient self-service portal (/portal/*). Patients have no
 * m_users account — identity is verified once with No. RM + tanggal lahir
 * (see PortalController::login()) and tracked via a plain session key, not
 * a Laravel auth guard.
 */
class PortalAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $patientId = $request->session()->get('portal_patient_id');
        $patient = $patientId ? Patient::find($patientId) : null;

        if (! $patient) {
            return redirect()->route('portal.login');
        }

        $request->attributes->set('portalPatient', $patient);

        return $next($request);
    }
}
