<?php

namespace Database\Seeders\Master;

use App\Helpers\RawatInapHelper;
use App\Models\AccountingAccount;
use App\Models\RuanganInap;
use App\Models\RuanganOperasi;
use Illuminate\Database\Seeder;

class ExtendedMasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedAccountingAccounts();
        $this->seedOperatingRooms();
        $this->seedInpatientRooms();
    }

    private function seedAccountingAccounts(): void
    {
        if (! class_exists(AccountingAccount::class)) {
            return;
        }

        foreach ([
            ['code' => '1101', 'name' => 'Kas', 'type' => 'asset'],
            ['code' => '1102', 'name' => 'Bank', 'type' => 'asset'],
            ['code' => '1201', 'name' => 'Piutang Pasien', 'type' => 'asset'],
            ['code' => '2101', 'name' => 'Utang Supplier', 'type' => 'liability'],
            ['code' => '4101', 'name' => 'Pendapatan Pelayanan', 'type' => 'income'],
            ['code' => '5101', 'name' => 'Beban Operasional', 'type' => 'expense'],
        ] as $account) {
            AccountingAccount::firstOrCreate(
                ['klienfk' => 1, 'code' => $account['code']],
                $account + ['is_active' => true]
            );
        }
    }

    private function seedOperatingRooms(): void
    {
        if (! class_exists(RuanganOperasi::class)) {
            return;
        }

        RuanganOperasi::firstOrCreate(
            ['klienfk' => 1, 'kode' => 'OK-01'],
            [
                'nama' => 'Kamar Operasi Utama',
                'lantai' => '1',
                'status' => 'tersedia',
                'keterangan' => 'Ruang operasi demo',
            ]
        );
    }

    private function seedInpatientRooms(): void
    {
        if (! class_exists(RuanganInap::class) || RuanganInap::where('klienfk', 1)->exists()) {
            return;
        }

        app(RawatInapHelper::class)->createRuangan([
            'klienfk' => 1,
            'nama' => 'Ruang Rawat Inap Demo',
            'lantai' => '1',
            'kelas' => 'Kelas I',
            'tarif_per_hari' => 350000,
            'jumlah_ranjang' => 3,
        ]);
    }
}
