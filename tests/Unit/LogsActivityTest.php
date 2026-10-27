<?php

namespace Tests\Unit;

use App\Models\ActivityLog;
use App\Models\Transactions;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogsActivityTest extends TestCase
{
    use RefreshDatabase;

    public function test_logs_created_event_with_actor(): void
    {
        $user = User::first();
        $this->actingAs($user);

        $transaction = Transactions::create([
            'klienfk' => $user->klienfk,
            'no_transaction' => 'TRX-LOG-1',
            'transaction_type' => 'in',
            'typecode' => 'MISC',
            'amount' => 10000,
        ]);

        $log = ActivityLog::where('model_type', Transactions::class)
            ->where('model_id', $transaction->id)
            ->where('action', 'created')
            ->first();

        $this->assertNotNull($log);
        $this->assertSame($user->id, $log->user_id);
        $this->assertSame('TRX-LOG-1', $log->changes['no_transaction']);
    }

    public function test_logs_updated_event_with_old_and_new_values(): void
    {
        $transaction = Transactions::create([
            'klienfk' => 1,
            'no_transaction' => 'TRX-LOG-2',
            'transaction_type' => 'in',
            'typecode' => 'MISC',
            'amount' => 10000,
        ]);

        $transaction->update(['amount' => 20000]);

        $log = ActivityLog::where('model_type', Transactions::class)
            ->where('model_id', $transaction->id)
            ->where('action', 'updated')
            ->first();

        $this->assertNotNull($log);
        // MySQL (production) stores decimal columns as strings ("10000.00"); SQLite
        // (tests) returns bare numerics — compare numerically so both are satisfied.
        $this->assertEquals(10000, (float) $log->changes['amount']['old']);
        $this->assertEquals(20000, (float) $log->changes['amount']['new']);
    }

    public function test_does_not_log_update_when_only_timestamps_change(): void
    {
        $transaction = Transactions::create([
            'klienfk' => 1,
            'no_transaction' => 'TRX-LOG-3',
            'transaction_type' => 'in',
            'typecode' => 'MISC',
            'amount' => 10000,
        ]);

        $transaction->touch();

        $count = ActivityLog::where('model_type', Transactions::class)
            ->where('model_id', $transaction->id)
            ->where('action', 'updated')
            ->count();

        $this->assertSame(0, $count);
    }

    public function test_logs_deleted_event(): void
    {
        $transaction = Transactions::create([
            'klienfk' => 1,
            'no_transaction' => 'TRX-LOG-4',
            'transaction_type' => 'in',
            'typecode' => 'MISC',
            'amount' => 10000,
        ]);

        $transaction->delete();

        $log = ActivityLog::where('model_type', Transactions::class)
            ->where('model_id', $transaction->id)
            ->where('action', 'deleted')
            ->first();

        $this->assertNotNull($log);
        $this->assertSame('TRX-LOG-4', $log->changes['no_transaction']);
    }
}
