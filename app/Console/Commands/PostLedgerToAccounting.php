<?php

namespace App\Console\Commands;

use App\Helpers\AccountingHelper;
use App\Models\Transactions;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PostLedgerToAccounting extends Command
{
    protected $signature = 'accounting:post-ledger
        {--klienfk=1 : Cabang yang akan diposting}
        {--from= : Tanggal awal transaksi YYYY-MM-DD}
        {--to= : Tanggal akhir transaksi YYYY-MM-DD}
        {--dry-run : Tampilkan jumlah transaksi tanpa membuat jurnal}';

    protected $description = 'Posting transaksi ledger ke jurnal akuntansi secara idempotent';

    public function handle(): int
    {
        $klienfk = (int) $this->option('klienfk');
        $query = Transactions::query()->whereNull('deleted_at')->orderBy('id');

        if ($this->option('from')) {
            $query->whereDate('created_at', '>=', $this->option('from'));
        }
        if ($this->option('to')) {
            $query->whereDate('created_at', '<=', $this->option('to'));
        }

        $total = $query->count();
        $this->info("Ditemukan {$total} transaksi ledger.");
        if ($this->option('dry-run')) {
            return self::SUCCESS;
        }

        $posted = 0;
        $skipped = 0;
        $query->chunkById(100, function ($transactions) use ($klienfk, &$posted, &$skipped) {
            foreach ($transactions as $transaction) {
                $alreadyPosted = DB::table('t_accounting_journals')
                    ->where('klienfk', $klienfk)
                    ->where('source_type', 'ledger_transaction')
                    ->where('source_id', $transaction->id)
                    ->exists();

                if ($alreadyPosted) {
                    $skipped++;

                    continue;
                }

                AccountingHelper::postTransaction($transaction, $klienfk);
                $posted++;
            }
        });

        $this->info("Berhasil membuat {$posted} jurnal, {$skipped} transaksi sudah pernah diposting.");

        return self::SUCCESS;
    }
}
