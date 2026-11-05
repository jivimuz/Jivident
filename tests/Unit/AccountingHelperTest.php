<?php

namespace Tests\Unit;

use App\Helpers\AccountingHelper;
use App\Models\AccountingAccount;
use App\Models\Transactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class AccountingHelperTest extends TestCase
{
    use RefreshDatabase;

    public function test_balanced_journal_is_created_with_lines(): void
    {
        $cash = AccountingAccount::create([
            'klienfk' => 1, 'code' => '1101', 'name' => 'Kas', 'type' => 'asset',
        ]);
        $revenue = AccountingAccount::create([
            'klienfk' => 1, 'code' => '4101', 'name' => 'Pendapatan', 'type' => 'revenue',
        ]);

        $journal = AccountingHelper::createJournal([
            'description' => 'Pendapatan layanan',
            'journal_date' => '2026-11-03',
            'lines' => [
                ['account_id' => $cash->id, 'debit' => 100000, 'credit' => 0],
                ['account_id' => $revenue->id, 'debit' => 0, 'credit' => 100000],
            ],
        ], 1, 1);

        $this->assertCount(2, $journal->lines);
        $this->assertDatabaseHas('t_accounting_journals', ['id' => $journal->id, 'klienfk' => 1]);
    }

    public function test_unbalanced_journal_is_rejected(): void
    {
        $account = AccountingAccount::create([
            'klienfk' => 1, 'code' => '1102', 'name' => 'Bank', 'type' => 'asset',
        ]);

        $this->expectException(InvalidArgumentException::class);
        AccountingHelper::createJournal([
            'description' => 'Jurnal tidak balance',
            'lines' => [
                ['account_id' => $account->id, 'debit' => 100000, 'credit' => 0],
                ['account_id' => $account->id, 'debit' => 0, 'credit' => 90000],
            ],
        ], 1);
    }

    public function test_account_from_another_branch_is_rejected(): void
    {
        $account = AccountingAccount::create([
            'klienfk' => 2, 'code' => '1103', 'name' => 'Kas Cabang Lain', 'type' => 'asset',
        ]);

        $this->expectException(InvalidArgumentException::class);
        AccountingHelper::createJournal([
            'description' => 'Percobaan lintas cabang',
            'lines' => [
                ['account_id' => $account->id, 'debit' => 100000, 'credit' => 0],
                ['account_id' => $account->id, 'debit' => 0, 'credit' => 100000],
            ],
        ], 1);
    }

    public function test_ledger_transaction_is_posted_once(): void
    {
        $cash = AccountingAccount::create([
            'klienfk' => 1, 'code' => '1101', 'name' => 'Kas', 'type' => 'asset',
        ]);
        AccountingAccount::create([
            'klienfk' => 1, 'code' => '4101', 'name' => 'Pendapatan', 'type' => 'revenue',
        ]);
        AccountingAccount::create([
            'klienfk' => 1, 'code' => '5101', 'name' => 'Beban', 'type' => 'expense',
        ]);

        $transaction = Transactions::create([
            'no_transaction' => 'TRX-DEMO-001',
            'transaction_type' => 'in',
            'typecode' => 'POS',
            'amount' => 125000,
            'notes' => 'Penjualan demo',
            'created_at' => now(),
        ]);

        $journal = AccountingHelper::postTransaction($transaction, 1);
        $sameJournal = AccountingHelper::postTransaction($transaction, 1);

        $this->assertSame($journal->id, $sameJournal->id);
        $this->assertDatabaseCount('t_accounting_journals', 1);
        $this->assertCount(2, $journal->lines);
        $this->assertSame(125000.0, (float) $journal->lines->sum('debit'));
        $this->assertSame(125000.0, (float) $journal->lines->sum('credit'));
    }
}
