<?php

namespace Database\Seeders\Transactional;

use App\Helpers\AccountingHelper;
use App\Helpers\BpjsClaimHelper;
use App\Helpers\ProcurementHelper;
use App\Helpers\QualityIntegrationHelper;
use App\Models\AccountingAccount;
use App\Models\AccountingJournal;
use App\Models\BpjsClaim;
use App\Models\DaftarPasien;
use App\Models\IntegrationLog;
use App\Models\JadwalOperasi;
use App\Models\JenisPemeriksaanLab;
use App\Models\JenisPemeriksaanRadiologi;
use App\Models\LabPasien;
use App\Models\OperasiMonitoring;
use App\Models\OperasiRecord;
use App\Models\Patient;
use App\Models\ProcurementRequest;
use App\Models\Product;
use App\Models\QualityIncident;
use App\Models\QualityIndicator;
use App\Models\RadiologiPasien;
use App\Models\RawatInap;
use App\Models\RawatInapCarePlan;
use App\Models\RawatInapMedicalRecord;
use App\Models\RuanganOperasi;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class AllModulesDemoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('klienfk', 1)->first();
        $patient = Patient::where('klienfk', 1)->first();
        $visit = DaftarPasien::where('klienfk', 1)->where('noregistrasi', 'like', 'DEMO-%')->first();

        if (! $user || ! $patient || ! $visit) {
            $this->command?->warn('AllModulesDemoSeeder: data dasar demo belum tersedia, dilewati.');

            return;
        }

        $this->seedInpatientDocuments($user->id);
        $this->seedLabAndRadiology($visit, $user->id);
        $this->seedAccounting($user->id);
        $this->seedBpjsClaim($user->id);
        $this->seedOperatingRoom($patient->id, $visit->id, $user->id);
        $this->seedQualityAndIntegrations($user->id, $visit->id);
        $this->seedProcurement($user->id);
    }

    private function seedInpatientDocuments(int $userId): void
    {
        $admission = RawatInap::where('klienfk', 1)->latest('id')->first();
        if (! $admission) {
            return;
        }

        if (Schema::hasTable('t_rawatinap_careplans') && ! RawatInapCarePlan::where('fk_rawatinap', $admission->id)->exists()) {
            RawatInapCarePlan::create([
                'fk_rawatinap' => $admission->id,
                'diagnosis' => 'Risiko infeksi',
                'goal' => 'Pasien bebas tanda infeksi selama perawatan.',
                'interventions' => 'Pantau suhu, lakukan kebersihan tangan, dan evaluasi luka.',
                'status' => 'aktif',
                'target_date' => now()->addDays(2)->toDateString(),
                'created_by' => $userId,
            ]);
        }

        if (Schema::hasTable('t_rawatinap_medical_records') && ! RawatInapMedicalRecord::where('fk_rawatinap', $admission->id)->exists()) {
            RawatInapMedicalRecord::create([
                'fk_rawatinap' => $admission->id,
                'type' => 'cppt',
                'subjective' => 'Pasien mengeluh lemas.',
                'objective' => 'Kesadaran compos mentis, suhu 37,2 C.',
                'assessment' => 'Kondisi klinis membaik.',
                'plan' => 'Lanjutkan observasi dan terapi.',
                'created_by' => $userId,
            ]);
        }
    }

    private function seedLabAndRadiology(DaftarPasien $visit, int $userId): void
    {
        $lab = JenisPemeriksaanLab::where('klienfk', 1)->first();
        if ($lab && ! LabPasien::where('fk_daftarpasien', $visit->id)->exists()) {
            LabPasien::create([
                'klienfk' => 1,
                'fk_daftarpasien' => $visit->id,
                'fk_pasien' => $visit->pasienfk,
                'fk_jenispemeriksaan' => $lab->id,
                'jenis_nama' => $lab->nama,
                'satuan_snapshot' => $lab->satuan,
                'nilai_rujukan_snapshot' => $lab->nilai_rujukan,
                'harga_snapshot' => $lab->harga,
                'hasil' => 'Normal',
                'status' => 'selesai',
                'fk_dokter_minta' => $visit->dokterfk,
                'fk_petugas_input' => $userId,
                'tgl_diminta' => now()->subDay(),
                'tgl_hasil' => now()->subHours(6),
            ]);
        }

        $radiology = JenisPemeriksaanRadiologi::where('klienfk', 1)->first();
        if ($radiology && ! RadiologiPasien::where('fk_daftarpasien', $visit->id)->exists()) {
            RadiologiPasien::create([
                'klienfk' => 1,
                'fk_daftarpasien' => $visit->id,
                'fk_pasien' => $visit->pasienfk,
                'fk_jenispemeriksaan' => $radiology->id,
                'jenis_nama' => $radiology->nama,
                'harga_snapshot' => $radiology->harga,
                'catatan_klinis' => 'Evaluasi lanjutan (data demo)',
                'status' => 'selesai',
                'fk_dokter_pengirim' => $visit->dokterfk,
                'tgl_diminta' => now()->subDay(),
                'tgl_jadwal' => now()->subHours(12),
                'hasil_interpretasi' => 'Tidak tampak kelainan bermakna.',
                'fk_dokter_radiologi' => $visit->dokterfk,
                'tgl_hasil' => now()->subHours(4),
                'fk_userhandler' => $userId,
            ]);
        }
    }

    private function seedAccounting(int $userId): void
    {
        if (! Schema::hasTable('t_accounting_journals') || AccountingJournal::where('journal_no', 'DEMO-JU-001')->exists()) {
            return;
        }

        $kas = AccountingAccount::where('klienfk', 1)->where('code', '1101')->first();
        $pendapatan = AccountingAccount::where('klienfk', 1)->where('code', '4101')->first();
        if (! $kas || ! $pendapatan) {
            return;
        }

        app(AccountingHelper::class)->createJournal([
            'journal_no' => 'DEMO-JU-001',
            'description' => 'Pendapatan pelayanan demo',
            'source_type' => 'demo',
            'lines' => [
                ['account_id' => $kas->id, 'debit' => 250000, 'credit' => 0],
                ['account_id' => $pendapatan->id, 'debit' => 0, 'credit' => 250000],
            ],
        ], 1, $userId);
    }

    private function seedBpjsClaim(int $userId): void
    {
        if (! Schema::hasTable('t_bpjs_claims') || BpjsClaim::where('claim_no', 'DEMO-BPJS-001')->exists()) {
            return;
        }

        $visit = DaftarPasien::where('klienfk', 1)->where('noregistrasi', 'DEMO-BPJS-001')->first();
        if (! $visit) {
            $visit = DaftarPasien::create([
                'klienfk' => 1,
                'noregistrasi' => 'DEMO-BPJS-001',
                'dokterfk' => User::where('klienfk', 1)->value('id'),
                'ruanganfk' => 1,
                'pasienfk' => Patient::where('klienfk', 1)->value('id'),
                'jenispembayaran' => 'BPJS',
                'tglregistrasi' => now()->subDay(),
                'statuspasien' => 'LAMA',
                'noantrian' => 'BPJS1',
                'status' => 'Selesai',
                'statuspembayaran' => 'Belum Verifikasi',
                'biaya_registrasi' => 0,
            ]);
        }

        $claim = app(BpjsClaimHelper::class)->create([
            'daftarpasien_id' => $visit->id,
            'pasien_id' => $visit->pasienfk,
            'sep_no' => '0001R001DEMO0001',
            'claim_no' => 'DEMO-BPJS-001',
            'amount' => 350000,
            'service_date' => now()->subDay()->toDateString(),
            'notes' => 'Klaim demo',
        ], 1, $userId);
        app(BpjsClaimHelper::class)->transition($claim, 'submitted', $userId);
    }

    private function seedOperatingRoom(int $patientId, int $visitId, int $userId): void
    {
        if (! Schema::hasTable('t_jadwal_operasi') || ! Schema::hasTable('t_operasi_records')) {
            return;
        }

        $room = RuanganOperasi::where('klienfk', 1)->where('status', 'tersedia')->first();
        if (! $room || JadwalOperasi::where('catatan', 'DEMO-OPERASI-001')->exists()) {
            return;
        }

        $schedule = JadwalOperasi::create([
            'klienfk' => 1,
            'fk_ruangan_operasi' => $room->id,
            'fk_pasien' => $patientId,
            'fk_dokter' => User::where('klienfk', 1)->value('id'),
            'fk_daftarpasien' => $visitId,
            'nama_operasi' => 'Ekstraksi gigi kompleks',
            'tanggal' => now()->toDateString(),
            'jam_mulai' => '09:00',
            'jam_selesai' => '10:00',
            'status' => 'selesai',
            'catatan' => 'DEMO-OPERASI-001',
            'created_by' => $userId,
        ]);

        $record = OperasiRecord::create([
            'klienfk' => 1,
            'jadwal_operasi_id' => $schedule->id,
            'preop_checklist' => ['identitas' => true, 'informed_consent' => true],
            'anesthesia_plan' => 'Anestesi lokal.',
            'procedure_notes' => 'Prosedur berjalan lancar.',
            'consumables' => [['name' => 'Lidokain', 'qty' => 1]],
            'recovery_notes' => 'Pasien sadar baik.',
            'recovery_status' => 'stabil',
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);

        OperasiMonitoring::create([
            'klienfk' => 1,
            'operasi_record_id' => $record->id,
            'observed_at' => now()->subHours(2),
            'consciousness' => 'Compos mentis',
            'pain_scale' => 2,
            'systolic' => 120,
            'diastolic' => 80,
            'pulse' => 78,
            'spo2' => 99,
            'notes' => 'Monitoring pascaoperasi demo.',
            'recorded_by' => $userId,
        ]);
    }

    private function seedQualityAndIntegrations(int $userId, int $visitId): void
    {
        if (Schema::hasTable('t_quality_incidents') && ! QualityIncident::where('incident_no', 'DEMO-QI-001')->exists()) {
            app(QualityIntegrationHelper::class)->createIncident([
                'incident_no' => 'DEMO-QI-001',
                'occurred_at' => now()->subDays(2),
                'category' => 'Keselamatan pasien',
                'severity' => 'near_miss',
                'description' => 'Label spesimen hampir tertukar dan berhasil dikoreksi.',
                'immediate_action' => 'Verifikasi ulang identitas pasien.',
            ], 1, $userId);
        }

        if (Schema::hasTable('t_quality_indicators') && ! QualityIndicator::where('code', 'DEMO-LOS')->exists()) {
            QualityIndicator::create([
                'klienfk' => 1,
                'code' => 'DEMO-LOS',
                'name' => 'Rata-rata lama rawat',
                'unit' => 'hari',
                'target' => 3,
                'period_start' => now()->startOfMonth()->toDateString(),
                'period_end' => now()->endOfMonth()->toDateString(),
                'value' => 2.4,
                'notes' => 'Indikator demo',
                'recorded_by' => $userId,
            ]);
        }

        if (Schema::hasTable('t_integration_logs') && ! IntegrationLog::where('reference_id', $visitId)->where('system', 'satusehat')->exists()) {
            app(QualityIntegrationHelper::class)->queueIntegration([
                'system' => 'satusehat',
                'resource' => 'PatientVisit',
                'operation' => 'create',
                'reference_type' => 'DaftarPasien',
                'reference_id' => $visitId,
                'request_payload' => ['demo' => true],
            ], 1);
        }
    }

    private function seedProcurement(int $userId): void
    {
        if (! Schema::hasTable('t_procurement_requests') || ProcurementRequest::where('request_no', 'DEMO-PR-001')->exists()) {
            return;
        }

        $product = Product::where('klienfk', 1)->first();
        $supplier = Supplier::where('klienfk', 1)->first();
        if (! $product || ! $supplier) {
            return;
        }

        $request = app(ProcurementHelper::class)->createRequest([
            'request_no' => 'DEMO-PR-001',
            'requesting_unit' => 'Farmasi',
            'reason' => 'Pengadaan stok demo',
            'items' => [[
                'product_id' => $product->id,
                'item_name' => $product->name,
                'unit' => 'pcs',
                'qty' => 20,
                'notes' => 'Item demo',
            ]],
        ], 1, $userId);
        app(ProcurementHelper::class)->transitionRequest($request, 'submitted', $userId);
        app(ProcurementHelper::class)->transitionRequest($request, 'approved', $userId);
        app(ProcurementHelper::class)->transitionRequest($request, 'ordered', $userId);
        app(ProcurementHelper::class)->createPurchaseOrder([
            'procurement_request_id' => $request->id,
            'supplier_id' => $supplier->id,
            'po_no' => 'DEMO-PO-001',
            'po_date' => now()->toDateString(),
            'notes' => 'Purchase order demo',
        ], 1, $userId);
    }
}
