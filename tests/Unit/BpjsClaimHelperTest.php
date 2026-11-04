<?php

namespace Tests\Unit;

use App\Helpers\BpjsClaimHelper;
use App\Models\BpjsClaim;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class BpjsClaimHelperTest extends TestCase
{
    use RefreshDatabase;

    public function test_claim_follows_supported_lifecycle(): void
    {
        $claim = BpjsClaim::create([
            'klienfk' => 1,
            'daftarpasien_id' => 1,
            'pasien_id' => 1,
            'status' => 'draft',
            'amount' => 250000,
        ]);

        BpjsClaimHelper::transition($claim, 'submitted', 1);
        BpjsClaimHelper::transition($claim->refresh(), 'verified', 1);
        $paid = BpjsClaimHelper::transition($claim->refresh(), 'paid', 1);

        $this->assertSame('paid', $paid->status);
        $this->assertNotNull($paid->submitted_at);
        $this->assertNotNull($paid->verified_at);
    }

    public function test_invalid_claim_transition_is_rejected(): void
    {
        $claim = BpjsClaim::create([
            'klienfk' => 1,
            'daftarpasien_id' => 2,
            'pasien_id' => 2,
            'status' => 'draft',
            'amount' => 100000,
        ]);

        $this->expectException(InvalidArgumentException::class);
        BpjsClaimHelper::transition($claim, 'paid', 1);
    }

    public function test_terminal_claim_cannot_be_changed(): void
    {
        $claim = BpjsClaim::create([
            'klienfk' => 1,
            'daftarpasien_id' => 3,
            'pasien_id' => 3,
            'status' => 'rejected',
            'amount' => 100000,
        ]);

        $this->expectException(InvalidArgumentException::class);
        BpjsClaimHelper::transition($claim, 'submitted', 1);
    }
}
