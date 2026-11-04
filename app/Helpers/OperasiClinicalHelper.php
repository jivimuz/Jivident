<?php

namespace App\Helpers;

use App\Models\JadwalOperasi;
use App\Models\OperasiMonitoring;
use App\Models\OperasiRecord;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class OperasiClinicalHelper
{
    public function saveRecord(JadwalOperasi $jadwal, array $data, int $userId): OperasiRecord
    {
        if ($jadwal->status === 'dibatalkan') {
            throw new InvalidArgumentException('Jadwal operasi dibatalkan.');
        }

        return DB::transaction(function () use ($jadwal, $data, $userId) {
            return OperasiRecord::updateOrCreate(
                ['klienfk' => $jadwal->klienfk, 'jadwal_operasi_id' => $jadwal->id],
                [
                    'preop_checklist' => $data['preop_checklist'] ?? null,
                    'anesthesia_plan' => $data['anesthesia_plan'] ?? null,
                    'anesthesia_record' => $data['anesthesia_record'] ?? null,
                    'procedure_notes' => $data['procedure_notes'] ?? null,
                    'consumables' => $data['consumables'] ?? null,
                    'implants' => $data['implants'] ?? null,
                    'recovery_notes' => $data['recovery_notes'] ?? null,
                    'recovery_status' => $data['recovery_status'] ?? null,
                    'updated_by' => $userId,
                    'created_by' => $userId,
                ]
            );
        });
    }

    public function addMonitoring(OperasiRecord $record, array $data, int $userId): OperasiMonitoring
    {
        if ($data['pain_scale'] > 10 || $data['spo2'] > 100) {
            throw new InvalidArgumentException('Nilai monitoring di luar batas.');
        }

        return $record->monitoring()->create([
            'klienfk' => $record->klienfk,
            'observed_at' => $data['observed_at'] ?? now(),
            'consciousness' => $data['consciousness'] ?? null,
            'pain_scale' => $data['pain_scale'] ?? null,
            'systolic' => $data['systolic'] ?? null,
            'diastolic' => $data['diastolic'] ?? null,
            'pulse' => $data['pulse'] ?? null,
            'spo2' => $data['spo2'] ?? null,
            'notes' => $data['notes'] ?? null,
            'recorded_by' => $userId,
        ]);
    }
}
