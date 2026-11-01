<?php

namespace Database\Seeders\Transactional;

use App\Helpers\TransactionHelper;
use App\Models\DaftarPasien;
use App\Models\DiagnosaPasien;
use App\Models\ICDX;
use App\Models\Patient;
use App\Models\RuangPraktek;
use App\Models\Tindakan;
use App\Models\TindakanPasien;
use App\Models\User;
use Illuminate\Database\Seeder;

class PatientVisitDemoSeeder extends Seeder
{
    /**
     * Beberapa kunjungan contoh melewati tahapan alur pasien yang berbeda-beda
     * (lihat "Patient flow" di `CLAUDE.md`) — Dalam Antrian, Di Periksa,
     * Selesai-tapi-belum-bayar, dan Selesai-dan-dibayar-penuh — supaya daftar
     * ruang tunggu, Billing, dan laporan finansial sama-sama ada datanya
     * begitu DB baru di-seed, bukan cuma master kosong.
     */
    public function run(): void
    {
        if (DaftarPasien::where('noregistrasi', 'like', 'DEMO-%')->exists()) {
            return;
        }

        $patients = Patient::where('klienfk', 1)->orderBy('id')->take(5)->get();
        if ($patients->count() < 5) {
            return;
        }

        $dokterId = User::where('klienfk', 1)->value('id') ?? 1;
        $ruanganId = RuangPraktek::where('klienfk', 1)->value('id') ?? 1;
        $tindakan = Tindakan::where('klienfk', 1)->orderBy('id')->first();
        $diagnosa = ICDX::where('code', 'K02.1')->first();

        // 1) Baru daftar, belum dipanggil masuk.
        $this->makeVisit($patients[0], $dokterId, $ruanganId, 1, [
            'status' => 'Dalam Antrian',
            'tgldiperiksa' => null,
        ]);

        // 2) Sedang di dalam ruang periksa.
        $this->makeVisit($patients[1], $dokterId, $ruanganId, 2, [
            'status' => 'Di Periksa',
            'tgldiperiksa' => now(),
        ]);

        // 3) Pemeriksaan selesai tapi belum bayar sama sekali — harus tetap
        // muncul di daftar tagihan Billing (statuspembayaran, bukan status,
        // yang dipakai filter "belum bayar" — lihat CLAUDE.md).
        $visit3 = $this->makeVisit($patients[2], $dokterId, $ruanganId, 3, [
            'status' => 'Selesai',
            'tgldiperiksa' => now()->subHour(),
        ]);
        $this->addTindakanAndDiagnosa($visit3, $tindakan, $diagnosa, $dokterId);

        // 4) Pemeriksaan selesai & dibayar penuh lewat ledger — ini mendarat
        // di statuspembayaran "Verifikasi", BUKAN "Lunas": "Lunas" di app
        // ini cuma dicapai lewat konfirmasi staf yang eksplisit lewat
        // BillingController::processPaid(), bukan otomatis begitu jumlah
        // bayar sudah cukup (lihat BillingStatusHelper::resolve() — nilai
        // lama cuma dipertahankan "Lunas" kalau memang sudah "Lunas"
        // sebelumnya, tidak pernah di-set baru lewat jalur ini).
        $visit4 = $this->makeVisit($patients[3], $dokterId, $ruanganId, 4, [
            'status' => 'Selesai',
            'tgldiperiksa' => now()->subHours(2),
        ]);
        $this->addTindakanAndDiagnosa($visit4, $tindakan, $diagnosa, $dokterId);
        $totalTagihan = $visit4->biaya_registrasi
            + (float) ($tindakan->amount ?? 0)
            + 100000; // sama dengan amount diagnosa yang ditambahkan di bawah

        app(TransactionHelper::class)->createTransaction([
            'transaction_type' => 'in',
            'typecode' => 'BIL',
            'amount' => $totalTagihan,
            'daftarpasien_id' => $visit4->id,
            'fk_payment' => 1,
            'notes' => 'Pembayaran kunjungan (data demo)',
        ]);

        // 5) Dibatalkan.
        $this->makeVisit($patients[4], $dokterId, $ruanganId, 5, [
            'status' => 'Dibatalkan',
            'tgldiperiksa' => null,
        ]);
    }

    private function makeVisit(Patient $patient, int $dokterId, int $ruanganId, int $seq, array $overrides): DaftarPasien
    {
        return DaftarPasien::create(array_merge([
            'klienfk' => 1,
            'noregistrasi' => 'DEMO-'.now()->format('Ymd').'-'.str_pad((string) $seq, 3, '0', STR_PAD_LEFT),
            'dokterfk' => $dokterId,
            'ruanganfk' => $ruanganId,
            'pasienfk' => $patient->id,
            'jenispembayaran' => 'UMUM',
            'tglregistrasi' => now(),
            'statuspasien' => 'LAMA',
            'noantrian' => 'A'.$seq,
            'statuspembayaran' => 'Belum Verifikasi',
            'biaya_registrasi' => 20000,
        ], $overrides));
    }

    private function addTindakanAndDiagnosa(DaftarPasien $visit, ?Tindakan $tindakan, ?ICDX $diagnosa, int $dokterId): void
    {
        $dokterName = User::find($dokterId)?->display_name ?? User::find($dokterId)?->username ?? 'Dokter';

        if ($tindakan) {
            TindakanPasien::create([
                'klienfk' => 1,
                'fk_daftarpasien' => $visit->id,
                'fk_tindakan' => $tindakan->id,
                'fk_dokter' => $dokterId,
                'fk_userhandler' => $dokterId,
                'tindakan' => $tindakan->name,
                'dokter' => $dokterName,
                'qty' => 1,
                'price' => $tindakan->amount,
                'amount' => $tindakan->amount,
            ]);
        }

        if ($diagnosa) {
            DiagnosaPasien::create([
                'klienfk' => 1,
                'fk_daftarpasien' => $visit->id,
                'fk_pasien' => $visit->pasienfk,
                'tipe_diagnosa' => 'X',
                'fk_diagnosa' => $diagnosa->id,
                'fk_dokter' => $dokterId,
                'fk_userhandler' => $dokterId,
                'diagnosa' => $diagnosa->name,
                'dokter' => $dokterName,
                'qty' => 1,
                'price' => 100000,
                'amount' => 100000,
            ]);
        }
    }
}
