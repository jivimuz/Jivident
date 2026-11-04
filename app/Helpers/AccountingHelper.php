<?php

namespace App\Helpers;

use App\Models\AccountingAccount;
use App\Models\AccountingJournal;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class AccountingHelper
{
    public static function createJournal(array $data, int $klienfk, ?int $userId = null): AccountingJournal
    {
        $lines = $data['lines'] ?? [];
        if (count($lines) < 2) {
            throw new InvalidArgumentException('Jurnal harus memiliki minimal dua baris.');
        }

        $debit = 0.0;
        $credit = 0.0;
        $accountIds = collect($lines)->pluck('account_id')->filter()->unique();
        $accounts = AccountingAccount::query()
            ->where('klienfk', $klienfk)
            ->whereIn('id', $accountIds)
            ->get()
            ->keyBy('id');

        if ($accounts->count() !== $accountIds->count()) {
            throw new InvalidArgumentException('Akun jurnal tidak valid untuk cabang ini.');
        }

        foreach ($lines as $line) {
            $lineDebit = (float) ($line['debit'] ?? 0);
            $lineCredit = (float) ($line['credit'] ?? 0);
            if (($lineDebit > 0) === ($lineCredit > 0)) {
                throw new InvalidArgumentException('Setiap baris jurnal harus memiliki debit atau kredit saja.');
            }
            $debit += $lineDebit;
            $credit += $lineCredit;
        }

        if (abs($debit - $credit) > 0.005) {
            throw new InvalidArgumentException('Total debit dan kredit harus seimbang.');
        }

        return DB::transaction(function () use ($data, $klienfk, $userId, $lines) {
            $journal = AccountingJournal::create([
                'klienfk' => $klienfk,
                'journal_no' => $data['journal_no'] ?? 'JU-'.now()->format('YmdHisv'),
                'journal_date' => $data['journal_date'] ?? now()->toDateString(),
                'description' => $data['description'],
                'source_type' => $data['source_type'] ?? null,
                'source_id' => $data['source_id'] ?? null,
                'posted_by' => $userId,
            ]);

            foreach ($lines as $line) {
                $journal->lines()->create([
                    'account_id' => $line['account_id'],
                    'debit' => $line['debit'] ?? 0,
                    'credit' => $line['credit'] ?? 0,
                    'description' => $line['description'] ?? null,
                ]);
            }

            return $journal->load('lines.account');
        });
    }
}
