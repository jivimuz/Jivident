<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Models\DaftarPasien;
use App\Models\Kamar;
use App\Models\RawatInap;
use App\Models\RawatInapCatatan;
use App\Models\RawatInapPindah;
use App\Models\RuanganInap;
use Carbon\Carbon;
use Illuminate\Support\Collection;
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

            // Pulang = kunjungan dasarnya juga selesai secara klinis, sama
            // seperti PemeriksaanController::updateStatus() menandai visit
            // rawat jalan biasa — independen dari statuspembayaran, supaya
            // tagihan rawat inap yang belum lunas tetap konsisten muncul di
            // Billing (yang membaca statuspembayaran, bukan status ini).
            DaftarPasien::where('id', $admission->fk_daftarpasien)->update(['status' => 'Selesai']);

            return $admission->refresh();
        });
    }

    /**
     * Moves an active admission to a different (empty) bed and logs the
     * move — e.g. 3 days in a regular room then moved to VIP 2 days before
     * discharge. calculateBilling() reads this log to split the room charge
     * across however many beds were actually occupied, instead of billing
     * the whole stay at whatever bed the patient currently happens to be in.
     */
    public function pindahRuangan(int $rawatInapId, array $data): RawatInapPindah
    {
        $klienfk = $data['klienfk'] ?? 1;

        $admission = RawatInap::where('klienfk', $klienfk)->find($rawatInapId);
        if (! $admission) {
            throw new InvalidArgumentException('Admisi tidak ditemukan');
        }
        if ($admission->status !== 'dirawat') {
            throw new InvalidArgumentException('Pasien ini sudah tidak dirawat, tidak bisa dipindahkan ruangannya');
        }
        if (empty($data['fk_kamar_tujuan'])) {
            throw new InvalidArgumentException('Kamar tujuan wajib diisi');
        }
        if ((int) $data['fk_kamar_tujuan'] === (int) $admission->fk_kamar) {
            throw new InvalidArgumentException('Kamar tujuan sama dengan kamar saat ini');
        }

        $kamarTujuan = Kamar::where('klienfk', $klienfk)->find($data['fk_kamar_tujuan']);
        if (! $kamarTujuan) {
            throw new InvalidArgumentException('Kamar tujuan tidak ditemukan');
        }
        if ($kamarTujuan->status !== 'kosong') {
            throw new InvalidArgumentException('Kamar tujuan sedang terisi');
        }

        return DB::transaction(function () use ($admission, $kamarTujuan, $data) {
            $kamarAsalId = $admission->fk_kamar;

            Kamar::where('id', $kamarAsalId)->update(['status' => 'kosong']);
            $kamarTujuan->update(['status' => 'terisi']);
            $admission->update(['fk_kamar' => $kamarTujuan->id]);

            return RawatInapPindah::create([
                'fk_rawatinap' => $admission->id,
                'fk_kamar_asal' => $kamarAsalId,
                'fk_kamar_tujuan' => $kamarTujuan->id,
                'waktu_pindah' => $data['waktu_pindah'] ?? now(),
                'catatan' => $data['catatan'] ?? null,
                'fk_userhandler' => $data['fk_userhandler'] ?? null,
            ]);
        });
    }

    /**
     * @return Collection<int, RawatInapPindah>
     */
    public function getPindahLog(int $rawatInapId)
    {
        return RawatInapPindah::with(['kamarAsal', 'kamarTujuan'])
            ->where('fk_rawatinap', $rawatInapId)
            ->orderByDesc('waktu_pindah')
            ->get();
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
     * Akumulasi otomatis: tarif kamar per hari — dipecah per ruangan yang
     * ditempati kalau pasien pernah dipindahkan (lihat buildKamarSegments())
     * — ditambah semua tindakan/diagnosa/lab/radiologi/resep yang terjadi
     * selama kunjungan yang sama (fk_daftarpasien), dikurangi yang sudah
     * dibayar dari ledger transaksi.
     *
     * @return array{jumlah_hari: int, biaya_registrasi: float, biaya_kamar: float, rincian_kamar: array<int, array{kamar_nama: string, hari: int, tarif_per_hari: float, subtotal: float}>, biaya_tindakan: float, biaya_diagnosa: float, biaya_lab: float, biaya_radiologi: float, biaya_resep: float, total_tagihan: float, total_paid: float, sisa: float}
     */
    public function calculateBilling(int $rawatInapId): array
    {
        $admission = RawatInap::findOrFail($rawatInapId);
        $daftarPasien = DaftarPasien::findOrFail($admission->fk_daftarpasien);

        $segments = $this->buildKamarSegments($admission);
        $biayaKamar = 0.0;
        $rincianKamar = [];
        foreach ($segments as $segment) {
            $subtotal = $segment['hari'] * (float) $segment['kamar']->tarif_per_hari;
            $biayaKamar += $subtotal;
            $rincianKamar[] = [
                'kamar_nama' => $segment['kamar']->nama,
                'hari' => $segment['hari'],
                'tarif_per_hari' => (float) $segment['kamar']->tarif_per_hari,
                'subtotal' => $subtotal,
            ];
        }
        $jumlahHari = array_sum(array_column($segments, 'hari'));

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
            'rincian_kamar' => $rincianKamar,
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

    /**
     * Splits an admission's stay into (kamar, hari) segments from its
     * pindahRuangan() log — e.g. [{kamar: Reguler, hari: 3}, {kamar: VIP,
     * hari: 2}] for a patient moved to VIP 2 days before a 5-day discharge.
     * With no transfers at all this reduces to exactly the old single-room
     * whole-stay calculation (same "minimum 1 day" rule), so it's a pure
     * generalization — existing no-transfer billing is unaffected.
     *
     * A transfer's calendar day is counted entirely toward the room moved
     * INTO (the room occupied by the end of that day) — picked because this
     * system already bills in whole calendar days, not fractions, and this
     * is the simplest rule that can't double- or zero-count a day.
     *
     * @return array<int, array{kamar: Kamar, hari: int}>
     */
    private function buildKamarSegments(RawatInap $admission): array
    {
        $pindahList = RawatInapPindah::where('fk_rawatinap', $admission->id)->orderBy('waktu_pindah')->get();

        $masukTanggal = Carbon::parse($admission->tgl_masuk)->startOfDay();
        $keluarTanggal = ($admission->tgl_keluar ? Carbon::parse($admission->tgl_keluar) : now())->startOfDay();
        $batasAkhirEksklusif = $keluarTanggal->copy()->addDay();

        $kamarIdUrutan = [];
        $batas = [$masukTanggal->copy()];

        if ($pindahList->isEmpty()) {
            $kamarIdUrutan[] = $admission->fk_kamar;
        } else {
            $kamarIdUrutan[] = $pindahList->first()->fk_kamar_asal;
            foreach ($pindahList as $pindah) {
                $batas[] = Carbon::parse($pindah->waktu_pindah)->startOfDay();
                $kamarIdUrutan[] = $pindah->fk_kamar_tujuan;
            }
        }
        $batas[] = $batasAkhirEksklusif;

        $mentah = [];
        $totalHari = 0;
        for ($i = 0; $i < count($kamarIdUrutan); $i++) {
            $hari = (int) floor($batas[$i]->diffInDays($batas[$i + 1]));
            if ($hari > 0) {
                $mentah[] = ['kamar_id' => $kamarIdUrutan[$i], 'hari' => $hari];
                $totalHari += $hari;
            }
        }

        if ($totalHari === 0) {
            // Masuk & keluar (atau masuk & pindah) di tanggal kalender yang
            // sama -> minimal 1 hari, sama seperti aturan lama, dibebankan ke
            // kamar terakhir yang ditempati.
            $mentah = [['kamar_id' => end($kamarIdUrutan), 'hari' => 1]];
        }

        $kamarById = Kamar::whereIn('id', array_unique(array_column($mentah, 'kamar_id')))->get()->keyBy('id');

        return array_map(fn ($s) => ['kamar' => $kamarById[$s['kamar_id']], 'hari' => $s['hari']], $mentah);
    }

    /**
     * Rooms with their beds nested underneath, each bed carrying the active
     * admission's patient name/entry date when occupied — shared by
     * RawatInapController's room-management page and PemeriksaanController's
     * Rawat Inap tab (the bed-grid picker used for admission AND transfers
     * is the exact same data, just rendered in two different places).
     *
     * @return array<int, array{id: int, nama: string, kelas: ?string, lantai: ?string, tarif_per_hari: float, beds: array<int, array{id: int, nomor_ranjang: ?int, status: string, pasien_nama: ?string, tgl_masuk: mixed}>}>
     */
    public function getRuanganListData(int $klienfk): array
    {
        $ruangan = RuanganInap::where('klienfk', $klienfk)
            ->with(['beds' => fn ($q) => $q->orderBy('nomor_ranjang')])
            ->orderBy('nama')
            ->get();

        $activeAdmissions = RawatInap::select(['t_rawatinap.fk_kamar', 'm_pasien.nama as pasien_nama', 't_rawatinap.tgl_masuk'])
            ->join('m_pasien', 'm_pasien.id', 't_rawatinap.fk_pasien')
            ->where('t_rawatinap.klienfk', $klienfk)
            ->where('t_rawatinap.status', 'dirawat')
            ->get()
            ->keyBy('fk_kamar');

        return $ruangan->map(fn ($r) => [
            'id' => $r->id,
            'nama' => $r->nama,
            'kelas' => $r->kelas,
            'lantai' => $r->lantai,
            'tarif_per_hari' => $r->tarif_per_hari,
            'beds' => $r->beds->map(function ($bed) use ($activeAdmissions) {
                $admission = $activeAdmissions->get($bed->id);

                return [
                    'id' => $bed->id,
                    'nomor_ranjang' => $bed->nomor_ranjang,
                    'status' => $bed->status,
                    'pasien_nama' => $admission->pasien_nama ?? null,
                    'tgl_masuk' => $admission->tgl_masuk ?? null,
                ];
            }),
        ])->values()->all();
    }

    /**
     * Creates a room (RuanganInap) plus `jumlah_ranjang` bed rows (Kamar) in
     * one go. Rate/class are stored on the room but mirrored onto every bed
     * it owns, so calculateBilling() (which reads $kamar->tarif_per_hari
     * directly) and the rest of the admit/discharge flow need no changes.
     */
    public function createRuangan(array $data): RuanganInap
    {
        foreach (['nama', 'jumlah_ranjang'] as $field) {
            if (empty($data[$field])) {
                throw new InvalidArgumentException("{$field} wajib diisi");
            }
        }

        $jumlahRanjang = (int) $data['jumlah_ranjang'];
        if ($jumlahRanjang < 1 || $jumlahRanjang > 50) {
            throw new InvalidArgumentException('Jumlah ranjang harus antara 1 dan 50');
        }

        $klienfk = $data['klienfk'] ?? 1;

        return DB::transaction(function () use ($data, $klienfk, $jumlahRanjang) {
            $ruangan = RuanganInap::create([
                'klienfk' => $klienfk,
                'nama' => $data['nama'],
                'lantai' => $data['lantai'] ?? null,
                'kelas' => $data['kelas'] ?? null,
                'tarif_per_hari' => $data['tarif_per_hari'] ?? 0,
            ]);

            for ($i = 1; $i <= $jumlahRanjang; $i++) {
                Kamar::create([
                    'klienfk' => $klienfk,
                    'fk_ruangan' => $ruangan->id,
                    'nomor_ranjang' => $i,
                    'nama' => "{$ruangan->nama} - Ranjang {$i}",
                    'kelas' => $ruangan->kelas,
                    'tarif_per_hari' => $ruangan->tarif_per_hari,
                    'status' => 'kosong',
                ]);
            }

            return $ruangan;
        });
    }

    /**
     * Updates a room's own fields (cascaded to every bed it owns, so they
     * never drift from it) and, if `jumlah_ranjang` changed, adds new empty
     * beds or removes the highest-numbered ones — rejecting the resize if
     * any bed that would need removing is currently `terisi`.
     */
    public function updateRuangan(int $ruanganId, array $data): RuanganInap
    {
        $klienfk = $data['klienfk'] ?? 1;
        $ruangan = RuanganInap::where('klienfk', $klienfk)->find($ruanganId);
        if (! $ruangan) {
            throw new InvalidArgumentException('Ruangan tidak ditemukan');
        }

        $jumlahRanjangBaru = isset($data['jumlah_ranjang']) ? (int) $data['jumlah_ranjang'] : null;
        if ($jumlahRanjangBaru !== null && ($jumlahRanjangBaru < 1 || $jumlahRanjangBaru > 50)) {
            throw new InvalidArgumentException('Jumlah ranjang harus antara 1 dan 50');
        }

        return DB::transaction(function () use ($ruangan, $data, $jumlahRanjangBaru) {
            $ruangan->update([
                'nama' => $data['nama'] ?? $ruangan->nama,
                'lantai' => $data['lantai'] ?? $ruangan->lantai,
                'kelas' => $data['kelas'] ?? $ruangan->kelas,
                'tarif_per_hari' => $data['tarif_per_hari'] ?? $ruangan->tarif_per_hari,
            ]);

            $existingBeds = $ruangan->beds()->get();
            foreach ($existingBeds as $bed) {
                $bed->update([
                    'nama' => "{$ruangan->nama} - Ranjang {$bed->nomor_ranjang}",
                    'kelas' => $ruangan->kelas,
                    'tarif_per_hari' => $ruangan->tarif_per_hari,
                ]);
            }

            if ($jumlahRanjangBaru !== null) {
                $jumlahSekarang = $existingBeds->count();

                if ($jumlahRanjangBaru > $jumlahSekarang) {
                    for ($i = $jumlahSekarang + 1; $i <= $jumlahRanjangBaru; $i++) {
                        Kamar::create([
                            'klienfk' => $ruangan->klienfk,
                            'fk_ruangan' => $ruangan->id,
                            'nomor_ranjang' => $i,
                            'nama' => "{$ruangan->nama} - Ranjang {$i}",
                            'kelas' => $ruangan->kelas,
                            'tarif_per_hari' => $ruangan->tarif_per_hari,
                            'status' => 'kosong',
                        ]);
                    }
                } elseif ($jumlahRanjangBaru < $jumlahSekarang) {
                    $toRemove = $existingBeds->sortByDesc('nomor_ranjang')->take($jumlahSekarang - $jumlahRanjangBaru);

                    foreach ($toRemove as $bed) {
                        if ($bed->status !== 'kosong') {
                            throw new InvalidArgumentException("Tidak bisa mengurangi ranjang: {$bed->nama} sedang terisi");
                        }
                    }
                    foreach ($toRemove as $bed) {
                        $bed->delete();
                    }
                }
            }

            return $ruangan->refresh();
        });
    }

    /**
     * Deletes a room and every bed it owns — rejected if any of its beds is
     * currently `terisi`, same guard as the existing single-bed delete.
     */
    public function deleteRuangan(int $ruanganId, int $klienfk = 1): void
    {
        $ruangan = RuanganInap::where('klienfk', $klienfk)->find($ruanganId);
        if (! $ruangan) {
            throw new InvalidArgumentException('Ruangan tidak ditemukan');
        }

        $beds = $ruangan->beds()->get();
        if ($beds->contains(fn ($bed) => $bed->status !== 'kosong')) {
            throw new InvalidArgumentException('Tidak bisa menghapus ruangan: masih ada ranjang yang terisi');
        }

        DB::transaction(function () use ($ruangan, $beds) {
            foreach ($beds as $bed) {
                $bed->delete();
            }
            $ruangan->delete();
        });
    }
}
