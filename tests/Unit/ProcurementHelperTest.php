<?php

namespace Tests\Unit;

use App\Helpers\ProcurementHelper;
use App\Models\ProcurementRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class ProcurementHelperTest extends TestCase
{
    use RefreshDatabase;

    public function test_request_can_be_submitted_approved_and_ordered(): void
    {
        $helper = new ProcurementHelper;
        $request = $helper->createRequest([
            'request_no' => 'PR-001',
            'requesting_unit' => 'IGD',
            'items' => [['item_name' => 'Sarung tangan', 'unit' => 'box', 'qty' => 10]],
        ], 1, 5);

        $helper->transitionRequest($request, 'submitted', 5);
        $helper->transitionRequest($request->refresh(), 'approved', 5);
        $ordered = $helper->transitionRequest($request->refresh(), 'ordered', 5);

        $this->assertSame('ordered', $ordered->status);
        $this->assertNotNull($ordered->approved_at);
    }

    public function test_invalid_request_transition_is_rejected(): void
    {
        $request = ProcurementRequest::create([
            'klienfk' => 1,
            'request_no' => 'PR-002',
            'requesting_unit' => 'Lab',
            'status' => 'draft',
        ]);

        $this->expectException(InvalidArgumentException::class);
        (new ProcurementHelper)->transitionRequest($request, 'approved', 5);
    }

    public function test_request_requires_items(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new ProcurementHelper)->createRequest([
            'request_no' => 'PR-003',
            'requesting_unit' => 'Farmasi',
            'items' => [],
        ], 1, 5);
    }
}
