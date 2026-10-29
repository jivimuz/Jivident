<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Models\DaftarPasien;
use App\Models\Kamar;
use App\Models\RawatInap;
use App\Models\RawatInapCatatan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Admisi/pulang rawat inap + akumulasi billing. Tindakan/diagnosa/lab/
 * radiologi/resep selama masa rawat TIDAK diduplikasi di sini — semua tetap
 * tercatat lewat tab Pemeriksaan yang sudah ada, dikunci ke `fk_daftarpasien`
 * yang sama dengan admission ini. Helper ini cuma menjumlahkan semuanya saat
 * pasien keluar.
 */
final class RawatInapHelper
{
    public function admit(array $data): RawatInap
    {
        foreach (['fk_daftarpasien', 'fk_kamar'] as $field) {
            if (empty($data[$field])) {
                throw new InvalidArgumentException("{$field} wajib diisi");
            }
        }

        $klienfk = $data['klienfk'] ?? 1;

        // Discoped ke klienfk pemanggil — tanpa ini, user dari cabang lain bisa
        // admisi pasien ke kunjungan/kamar milik cabang yang bukan miliknya
        // hanya dengan menebak/mengirim ID lewat request langsung.
        $daftarPasien = DaftarPasien::where('klienfk', $klienfk)->find($data['fk_daftarpasien']);
        if (! $daftarPasien) {
            throw new InvalidArgumentException('Daftar Pasien tidak ditemukan');
        }

        if (RawatInap::where('fk_daftarpasien', $data['fk_daftarpasien'])->where('status', 'dirawat')->exists()) {
            throw new InvalidArgumentException('Kunjungan ini sudah punya admisi rawat inap yang aktif');
        }

        $kamar = Kamar::where('klienfk', $klienfk)->find($data['fk_kamar']);
        if (! $kamar) {
            throw new InvalidArgumentException('Kamar tidak ditemukan');
        }
        if ($kamar->status !== 'kosong') {
            throw new InvalidArgumentException('Kamar ini sedang terisi');
        }

        return DB::transaction(function () use ($data, $daftarPasien, $kamar, $klienfk) {
            $admission = RawatInap::create([
                'klienfk' => $klienfk,
                'fk_daftarpasien' => $daftarPasien->id,
                'fk_pasien' => $daftarPasien->pasienfk,
                'fk_kamar' => $kamar->id,
                'fk_dokter_penanggungjawab' => $data['fk_dokter_penanggungjawab'] ?? null,
                'tgl_masuk' => $data['tgl_masuk'] ?? now(),
                'status' => 'dirawat',
                'catatan_masuk' => $data['catatan_masuk'] ?? null,
                'fk_userhandler' => $data['fk_userhandler'] ?? null,
            ]);

            $kamar->update(['status' => 'terisi']);

            return $admission;
        });
    }

    public function discharge(int $rawatInapId, array $data = []): RawatInap
    {
        $admission = RawatInap::findOrFail($rawatInapId);

        if ($admission->status === 'keluar') {
            throw new InvalidArgumentException('Pasien ini sudah dipulangkan sebelumnya');
        }

        return DB::transaction(function () use ($admission, $data) {
            $admission->update([
                'tgl_keluar' => $data['tgl_keluar'] ?? now(),
                'status' => 'keluar',
                'ringkasan_pulang' => $data['ringkasan_pulang'] ?? null,
            ]);

            $admission->kamar?->update(['status' => 'kosong']);

            return $admission->refresh();
        });
    }

    public function tambahCatatan(array $data): RawatInapCatatan
    {
        if (empty($data['fk_rawatinap']) || empty($data['catatan'])) {
            throw new InvalidArgumentException('fk_rawatinap dan catatan wajib diisi');
        }

        if (! RawatInap::where('id', $data['fk_rawatinap'])->where('status', 'dirawat')->exists()) {
            throw new InvalidArgumentException('Admisi tidak ditemukan atau pasien sudah keluar');
        }

        return RawatInapCatatan::create([
            'fk_rawatinap' => $data['fk_rawatinap'],
            'waktu' => $data['waktu'] ?? now(),
            'suhu' => $data['suhu'] ?? null,
            'nadi' => $data['nadi'] ?? null,
            'pernapasan' => $data['pernapasan'] ?? null,
            'tensi' => $data['tensi'] ?? null,
            'catatan' => $data['catatan'],
            'fk_userhandler' => $data['fk_userhandler'] ?? null,
        ]);
    }

    /**
     * Akumulasi otomatis: tarif kamar per hari (minimal 1 hari meskipun pulang
     * di hari yang sama) + semua tindakan/diagnosa/lab/radiologi/resep yang
     * terjadi selama kunjungan yang sama (fk_daftarpasien), dikurangi yang
     * sudah dibayar dari ledger transaksi.
     *
     * @return array{jumlah_hari: int, biaya_registrasi: float, biaya_kamar: float, biaya_tindakan: float, biaya_diagnosa: float, biaya_lab: float, biaya_radiologi: float, biaya_resep: float, total_tagihan: float, total_paid: float, sisa: float}
     */
    public function calculateBilling(int $rawatInapId): array
    {
        $admission = RawatInap::findOrFail($rawatInapId);
        $daftarPasien = DaftarPasien::findOrFail($admission->fk_daftarpasien);
        $kamar = Kamar::findOrFail($admission->fk_kamar);

        // Dihitung per batas tanggal kalender (bukan per 24 jam genap) — masuk
        // dan keluar di hari yang sama tetap kena 1 hari. diffInDays() di
        // Carbon 3 (Laravel 12) mengembalikan float (sisa microsecond bisa
        // bikin hasil 1.0000069 dkk kalau tidak dinormalisasi ke startOfDay
        // dulu), jadi di-cast ke int setelah dibulatkan ke bawah.
        $tglMasuk = Carbon::parse($admission->tgl_masuk)->startOfDay();
        $tglKeluar = ($admission->tgl_keluar ? Carbon::parse($admission->tgl_keluar) : now())->startOfDay();
        $jumlahHari = max(1, (int) floor($tglMasuk->diffInDays($tglKeluar)) + 1);

        $biayaKamar = $jumlahHari * (float) $kamar->tarif_per_hari;

        $biayaTindakan = (float) DB::table('t_tindakanpasien')
            ->where('fk_daftarpasien', $daftarPasien->id)->whereNull('deleted_at')->sum('amount');
        $biayaDiagnosa = (float) DB::table('t_diagnosapasien')
            ->where('fk_daftarpasien', $daftarPasien->id)->whereNull('deleted_at')->sum('amount');
        $biayaLab = (float) DB::table('t_labpasien')
            ->where('fk_daftarpasien', $daftarPasien->id)->whereNull('deleted_at')->sum('harga_snapshot');
        $biayaRadiologi = (float) DB::table('t_radiologipasien')
            ->where('fk_daftarpasien', $daftarPasien->id)->whereNull('deleted_at')->sum('harga_snapshot');
        $biayaResep = (float) DB::table('t_reseppasien')
            ->where('daftarpasien_id', $daftarPasien->id)->whereNull('deleted_at')->sum('subtotal');

        $totalPaid = (float) DB::table('t_transactions')
            ->where('daftarpasien_id', $daftarPasien->id)->whereNull('deleted_at')
            ->selectRaw("COALESCE(SUM(CASE WHEN transaction_type = 'in' THEN amount WHEN transaction_type = 'out' THEN -amount ELSE 0 END), 0) as total_paid")
            ->value('total_paid');

        $totalTagihan = (float) $daftarPasien->biaya_registrasi + $biayaKamar + $biayaTindakan + $biayaDiagnosa + $biayaLab + $biayaRadiologi + $biayaResep;

        return [
            'jumlah_hari' => $jumlahHari,
            'biaya_registrasi' => (float) $daftarPasien->biaya_registrasi,
            'biaya_kamar' => $biayaKamar,
            'biaya_tindakan' => $biayaTindakan,
            'biaya_diagnosa' => $biayaDiagnosa,
            'biaya_lab' => $biayaLab,
            'biaya_radiologi' => $biayaRadiologi,
            'biaya_resep' => $biayaResep,
            'total_tagihan' => $totalTagihan,
            'total_paid' => $totalPaid,
            'sisa' => $totalTagihan - $totalPaid,
        ];
    }
}
