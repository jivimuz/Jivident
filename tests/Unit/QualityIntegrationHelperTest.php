<?php

namespace Tests\Unit;

use App\Helpers\QualityIntegrationHelper;
use App\Models\QualityIncident;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class QualityIntegrationHelperTest extends TestCase
{
    use RefreshDatabase;

    public function test_incident_can_be_reported_and_closed(): void
    {
        $helper = new QualityIntegrationHelper;
        $incident = $helper->createIncident([
            'incident_no' => 'INC-001',
            'occurred_at' => now(),
            'category' => 'Medication',
            'severity' => 'near_miss',
            'description' => 'Near miss medication.',
        ], 1, 5);

        $closed = $helper->updateIncident($incident, [
            'root_cause' => 'Double check belum dilakukan.',
            'corrective_action' => 'Tambah verifikasi dua petugas.',
            'status' => 'closed',
        ], 5);

        $this->assertSame('closed', $closed->status);
        $this->assertSame(5, $closed->closed_by);
    }

    public function test_closed_incident_cannot_be_modified(): void
    {
        $incident = QualityIncident::create([
            'klienfk' => 1,
            'incident_no' => 'INC-002',
            'occurred_at' => now(),
            'category' => 'Patient Safety',
            'severity' => 'mild',
            'description' => 'Closed incident.',
            'status' => 'closed',
        ]);

        $this->expectException(InvalidArgumentException::class);
        (new QualityIntegrationHelper)->updateIncident($incident, ['status' => 'resolved'], 5);
    }

    public function test_integration_queue_only_accepts_supported_systems(): void
    {
        $helper = new QualityIntegrationHelper;
        $log = $helper->queueIntegration([
            'system' => 'satusehat',
            'resource' => 'Patient',
            'operation' => 'create',
            'request_payload' => ['identifier' => 'RM-1'],
        ], 1);

        $this->assertSame('queued', $log->status);
        $this->assertDatabaseHas('t_integration_logs', ['id' => $log->id, 'system' => 'satusehat']);

        $this->expectException(InvalidArgumentException::class);
        $helper->queueIntegration(['system' => 'unknown', 'resource' => 'Patient', 'operation' => 'create'], 1);
    }
}
