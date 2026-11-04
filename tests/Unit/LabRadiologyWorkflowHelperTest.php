<?php

namespace Tests\Unit;

use App\Helpers\LabRadiologyWorkflowHelper;
use App\Models\LabPasien;
use App\Models\RadiologiPasien;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class LabRadiologyWorkflowHelperTest extends TestCase
{
    use RefreshDatabase;

    public function test_lab_specimen_collection_generates_a_barcode(): void
    {
        $order = LabPasien::create([
            'klienfk' => 1,
            'fk_daftarpasien' => 1,
            'fk_pasien' => 1,
            'jenis_nama' => 'Hemoglobin',
            'tgl_diminta' => now(),
            'status' => 'diminta',
        ]);

        $collected = LabRadiologyWorkflowHelper::collectSpecimen($order, 7);

        $this->assertStringStartsWith('SP-', $collected->specimen_code);
        $this->assertSame(7, $collected->specimen_collected_by);
    }

    public function test_lab_result_must_exist_before_verification(): void
    {
        $order = LabPasien::create([
            'klienfk' => 1,
            'fk_daftarpasien' => 1,
            'fk_pasien' => 1,
            'jenis_nama' => 'Glukosa',
            'tgl_diminta' => now(),
            'status' => 'diminta',
        ]);

        $this->expectException(InvalidArgumentException::class);
        LabRadiologyWorkflowHelper::verifyLabResult($order, 7);
    }

    public function test_radiology_report_can_be_approved_after_result(): void
    {
        $order = RadiologiPasien::create([
            'klienfk' => 1,
            'fk_daftarpasien' => 1,
            'fk_pasien' => 1,
            'jenis_nama' => 'Thorax',
            'tgl_diminta' => now(),
            'status' => 'selesai',
            'hasil_interpretasi' => 'Tidak tampak kelainan.',
        ]);

        $approved = LabRadiologyWorkflowHelper::approveRadiologyReport($order, 7);

        $this->assertSame('approved', $approved->report_status);
        $this->assertSame(7, $approved->report_approved_by);
    }
}
