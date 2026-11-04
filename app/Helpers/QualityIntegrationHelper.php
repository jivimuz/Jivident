<?php

namespace App\Helpers;

use App\Models\IntegrationLog;
use App\Models\QualityIncident;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class QualityIntegrationHelper
{
    public function createIncident(array $data, int $klienfk, int $userId): QualityIncident
    {
        return QualityIncident::create([
            'klienfk' => $klienfk,
            'incident_no' => $data['incident_no'],
            'occurred_at' => $data['occurred_at'],
            'category' => $data['category'],
            'severity' => $data['severity'],
            'description' => $data['description'],
            'immediate_action' => $data['immediate_action'] ?? null,
            'status' => 'reported',
            'reported_by' => $userId,
        ]);
    }

    public function updateIncident(QualityIncident $incident, array $data, int $userId): QualityIncident
    {
        if ($incident->status === 'closed') {
            throw new InvalidArgumentException('Insiden yang sudah ditutup tidak dapat diubah.');
        }

        $incident->fill([
            'root_cause' => $data['root_cause'] ?? $incident->root_cause,
            'corrective_action' => $data['corrective_action'] ?? $incident->corrective_action,
            'status' => $data['status'] ?? $incident->status,
        ]);
        if ($incident->status === 'closed') {
            $incident->closed_by = $userId;
        }
        $incident->save();

        return $incident->refresh();
    }

    public function queueIntegration(array $data, int $klienfk): IntegrationLog
    {
        if (! in_array($data['system'], ['satusehat', 'sisrute', 'rl'], true)) {
            throw new InvalidArgumentException('Sistem integrasi tidak didukung.');
        }

        return DB::transaction(fn () => IntegrationLog::create([
            'klienfk' => $klienfk,
            'system' => $data['system'],
            'resource' => $data['resource'],
            'operation' => $data['operation'],
            'reference_type' => $data['reference_type'] ?? null,
            'reference_id' => $data['reference_id'] ?? null,
            'status' => 'queued',
            'request_payload' => $data['request_payload'] ?? null,
        ]));
    }
}
