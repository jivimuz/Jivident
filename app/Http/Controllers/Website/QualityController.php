<?php

namespace App\Http\Controllers\Website;

use App\Helpers\QualityIntegrationHelper;
use App\Http\Controllers\Controller;
use App\Models\IntegrationLog;
use App\Models\QualityIncident;
use App\Models\QualityIndicator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class QualityController extends Controller
{
    public function index()
    {
        return view('pages/quality/index');
    }

    public function incidents(Request $request)
    {
        $query = QualityIncident::where('klienfk', Auth::user()->klienfk)->latest('id');
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return response()->json(['data' => $query->paginate(20)]);
    }

    public function storeIncident(Request $request)
    {
        $data = $request->validate([
            'incident_no' => 'required|string|max:40',
            'occurred_at' => 'required|date',
            'category' => 'required|string|max:80',
            'severity' => ['required', Rule::in(['near_miss', 'mild', 'moderate', 'severe', 'death'])],
            'description' => 'required|string|max:10000',
            'immediate_action' => 'nullable|string|max:5000',
        ]);
        $incident = (new QualityIntegrationHelper)->createIncident($data, Auth::user()->klienfk, Auth::id());

        return response()->json(['message' => 'Insiden berhasil dilaporkan.', 'data' => $incident], 201);
    }

    public function updateIncident(Request $request, int $id)
    {
        $incident = QualityIncident::where('klienfk', Auth::user()->klienfk)->findOrFail($id);
        $data = $request->validate([
            'root_cause' => 'nullable|string|max:10000',
            'corrective_action' => 'nullable|string|max:10000',
            'status' => ['required', Rule::in(['reported', 'investigating', 'resolved', 'closed'])],
        ]);
        $incident = (new QualityIntegrationHelper)->updateIncident($incident, $data, Auth::id());

        return response()->json(['message' => 'Insiden diperbarui.', 'data' => $incident]);
    }

    public function indicators()
    {
        return response()->json([
            'data' => QualityIndicator::where('klienfk', Auth::user()->klienfk)->latest('period_start')->paginate(20),
        ]);
    }

    public function storeIndicator(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:40',
            'name' => 'required|string|max:150',
            'unit' => 'required|string|max:30',
            'target' => 'nullable|numeric',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'value' => 'nullable|numeric',
            'notes' => 'nullable|string|max:5000',
        ]);
        $indicator = QualityIndicator::updateOrCreate(
            ['klienfk' => Auth::user()->klienfk, 'code' => $data['code'], 'period_start' => $data['period_start'], 'period_end' => $data['period_end']],
            $data + ['klienfk' => Auth::user()->klienfk, 'recorded_by' => Auth::id()]
        );

        return response()->json(['message' => 'Indikator mutu tersimpan.', 'data' => $indicator]);
    }

    public function queueIntegration(Request $request)
    {
        $data = $request->validate([
            'system' => ['required', Rule::in(['satusehat', 'sisrute', 'rl'])],
            'resource' => 'required|string|max:80',
            'operation' => 'required|in:create,update,send',
            'reference_type' => 'nullable|string|max:255',
            'reference_id' => 'nullable|integer',
            'request_payload' => 'nullable|array',
        ]);
        $log = (new QualityIntegrationHelper)->queueIntegration($data, Auth::user()->klienfk);

        return response()->json(['message' => 'Request integrasi masuk antrean.', 'data' => $log], 201);
    }

    public function integrations()
    {
        return response()->json([
            'data' => IntegrationLog::where('klienfk', Auth::user()->klienfk)->latest('id')->paginate(20),
        ]);
    }
}
