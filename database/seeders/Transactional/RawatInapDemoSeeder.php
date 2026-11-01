<?php

namespace Database\Seeders\Transactional;

use App\Helpers\RawatInapHelper;
use App\Models\DaftarPasien;
use App\Models\Kamar;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;

class RawatInapDemoSeeder extends Seeder
{
    /**
     * Satu siklus admit→discharge penuh lewat `RawatInapHelper` sungguhan
     * (bukan insert `t_rawatinap` mentah), supaya status ranjang, log, dan
     * billing-nya konsisten dengan yang dihasilkan alur asli. Butuh satu
     * kunjungan (`DaftarPasien`) tersendiri dulu — rawat inap selalu
     * menunjuk kunjungan yang sudah ada, bukan alur pendaftaran baru
     * (lihat catatan Rawat Inap di `CLAUDE.md`).
     */
    public function run(): void
    {
        if (DaftarPasien::where('noregistrasi', 'DEMO-RWI-001')->exists()) {
            return;
        }

        $kamar = Kamar::where('klienfk', 1)->where('status', 'kosong')->first();
        $patient = Patient::where('klienfk', 1)->orderByDesc('id')->first();
        $dokterId = User::where('klienfk', 1)->value('fk_employee') ?? 1;

        if (! $kamar || ! $patient) {
            return;
        }

        $visit = DaftarPasien::create([
            'klienfk' => 1,
            'noregistrasi' => 'DEMO-RWI-001',
            'dokterfk' => User::where('klienfk', 1)->value('id') ?? 1,
            'ruanganfk' => 1,
            'pasienfk' => $patient->id,
            'jenispembayaran' => 'UMUM',
            'tglregistrasi' => now()->subDays(3),
            'statuspasien' => 'LAMA',
            'status' => 'Di Periksa',
            'tgldiperiksa' => now()->subDays(3),
            'noantrian' => 'RWI1',
            'statuspembayaran' => 'Belum Verifikasi',
            'biaya_registrasi' => 50000,
        ]);

        $helper = app(RawatInapHelper::class);

        $admission = $helper->admit([
            'klienfk' => 1,
            'fk_daftarpasien' => $visit->id,
            'fk_kamar' => $kamar->id,
            'fk_dokter_penanggungjawab' => $dokterId,
            'tgl_masuk' => now()->subDays(3),
            'catatan_masuk' => 'Observasi demam berdarah (data demo)',
        ]);

        $helper->discharge($admission->id, [
            'tgl_keluar' => now()->subDay(),
            'ringkasan_pulang' => 'Kondisi stabil, pulang dengan obat jalan (data demo)',
        ]);
    }
}
